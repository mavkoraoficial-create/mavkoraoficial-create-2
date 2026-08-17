# Chatbot de WhatsApp de Mavkora

Guía completa para poner en marcha el asistente de WhatsApp: captura leads, responde
preguntas frecuentes, agenda reuniones y escala a un asesor humano.

---

## Cómo está armado

```
Cliente en WhatsApp
        │
        ▼
WhatsApp Cloud API (Meta)
        │  webhook
        ▼
      n8n  ──────────────►  API de Claude   (solo cuando el cliente escribe libre)
        │                          │
        │  X-Bot-Key               │
        ▼                          │
Laravel  /api/bot/*  ◄─────────────┘
        │
        ▼
     MySQL  →  Panel admin (leads, conversaciones, citas)
```

**El cerebro vive en Laravel, no en n8n.** La máquina de estados, el historial y las
reglas del negocio están en `app/Services/Bot/`. n8n solo transporta: recibe el
webhook de Meta, le pregunta a Laravel qué responder, y envía la respuesta.

Esto es a propósito. La lógica queda testeable en PHP, el historial queda junto a los
demás datos, y si algún día cambias n8n por otra herramienta no reescribes el bot.

### Archivos que componen el bot

| Archivo | Qué hace |
| --- | --- |
| `config/mavkora.php` | Catálogo de servicios, horarios y contacto. Fuente única de verdad. |
| `app/Services/Bot/ConversationFlow.php` | La máquina de estados. Aquí se decide qué responder. |
| `app/Services/Bot/Reply.php` | Construye las respuestas respetando los límites de Meta. |
| `app/Services/Bot/SlotFinder.php` | Calcula los horarios libres para agendar. |
| `app/Services/Bot/KnowledgeBase.php` | Arma el contexto y las reglas que se le pasan a la IA. |
| `app/Http/Controllers/Api/BotController.php` | Los cuatro endpoints que consume n8n. |
| `docker/n8n/mavkora-whatsapp-bot.json` | El workflow de n8n, listo para importar. |

---

## Antes de empezar

Necesitas tres cosas:

1. **Docker Desktop corriendo.** El proyecto no usa PHP ni Node instalados en tu
   máquina; todo vive en contenedores.
2. **Una cuenta de Meta Business.** Gratis, en [business.facebook.com](https://business.facebook.com).
3. **Un número de teléfono que NO esté registrado en WhatsApp** (ni normal ni Business).
   Meta lo exige. Para las pruebas iniciales puedes usar el número de prueba que
   Meta regala, sin necesidad de un número propio.

---

## Paso 1 · Preparar Laravel

```bash
docker compose up -d --build
```

La primera vez tarda varios minutos porque construye la imagen de PHP.

Luego, dentro del contenedor:

```bash
docker compose exec app cp .env.example .env
```

```bash
docker compose exec app php artisan key:generate
```

Ahora edita el `.env` y ajusta la conexión a MySQL, que por defecto apunta a SQLite:

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mavkora
DB_USERNAME=mavkora
DB_PASSWORD=secret
```

Y tus datos reales de contacto:

```dotenv
MAVKORA_EMAIL=tu-correo-real@mavkora.com
MAVKORA_PHONE="+57 300 000 0000"
MAVKORA_WHATSAPP=573000000000
BOT_HANDOFF_EMAIL=ventas@mavkora.com
```

Genera la clave que Laravel y n8n van a compartir:

```bash
docker compose exec app php artisan mavkora:bot-key
```

Guarda el valor que imprime: lo vas a pegar en n8n más adelante.

Corre las migraciones y limpia la configuración:

```bash
docker compose exec app php artisan migrate --force && docker compose exec app php artisan config:clear
```

Verifica que la web abra en <http://localhost:8000> y crea tu usuario del panel en
<http://localhost:8000/register>.

---

## Paso 2 · Exponer n8n a internet

Meta necesita alcanzar tu webhook por **HTTPS público**. `localhost` no le sirve.

Para desarrollo, un túnel resuelve esto. Con [ngrok](https://ngrok.com):

```bash
ngrok http 5678
```

Te dará una URL como `https://a1b2c3d4.ngrok-free.app`. Ponla en el `.env`:

```dotenv
N8N_WEBHOOK_URL=https://a1b2c3d4.ngrok-free.app/
N8N_ENCRYPTION_KEY=una-clave-larga-e-irrepetible-que-no-vas-a-cambiar
```

> **Cuidado con `N8N_ENCRYPTION_KEY`.** Si la cambias después de haber guardado
> credenciales en n8n, n8n ya no puede descifrarlas y toca cargarlas de nuevo.
> Defínela una vez, antes del primer arranque, y no la toques.

Reinicia n8n para que tome la URL:

```bash
docker compose up -d n8n
```

Abre <http://localhost:5678>. La primera vez te pide crear una cuenta de propietario:
es local, guárdala en tu gestor de contraseñas.

---

## Paso 3 · Configurar WhatsApp en Meta

1. Entra a [developers.facebook.com/apps](https://developers.facebook.com/apps) y
   crea una app de tipo **Empresa / Business**.
2. En el panel de la app, agrega el producto **WhatsApp**.
3. En **WhatsApp → Configuración de la API** vas a ver:
   - Un **número de prueba** que Meta te regala.
   - El **Identificador del número de teléfono** (`Phone number ID`). **No es el número**,
     es un ID numérico largo. Cópialo.
   - Un **token de acceso temporal** que dura 24 horas. Sirve para probar.
4. Agrega tu propio celular en **"Para"** como destinatario de prueba. Meta solo permite
   enviar a números verificados mientras uses el número de prueba.

Guarda estos valores en el `.env` de Laravel:

```dotenv
WHATSAPP_PHONE_NUMBER_ID=el-id-que-copiaste
WHATSAPP_VERIFY_TOKEN=inventa-una-cadena-larga-aqui
```

El `WHATSAPP_VERIFY_TOKEN` te lo inventas tú: es una contraseña que Meta y n8n comparten
para confirmar que el webhook es tuyo.

---

## Paso 4 · Importar el workflow en n8n

1. En n8n: **Workflows → ⋯ → Import from File** y elige
   `docker/n8n/mavkora-whatsapp-bot.json`.

2. Crea las **tres credenciales**. Todas son del tipo **Header Auth**
   (*Credentials → Add credential → Header Auth*):

   | Nombre de la credencial | Header Name | Header Value |
   | --- | --- | --- |
   | `Mavkora Bot API` | `X-Bot-Key` | el `BOT_API_KEY` del paso 1 |
   | `Anthropic API Key` | `x-api-key` | tu clave de [console.anthropic.com](https://console.anthropic.com) |
   | `Meta WhatsApp Token` | `Authorization` | `Bearer TU_TOKEN_DE_META` |

   El nombre debe coincidir exactamente, así el workflow las engancha solo.

3. Reemplaza los tres marcadores dentro del workflow:

   | Nodo | Marcador | Con qué |
   | --- | --- | --- |
   | ¿Token correcto? | `REEMPLAZAR_CON_WHATSAPP_VERIFY_TOKEN` | tu `WHATSAPP_VERIFY_TOKEN` |
   | Enviar por WhatsApp | `REEMPLAZAR_PHONE_NUMBER_ID` | tu `Phone number ID` |
   | Correo al equipo | `REEMPLAZAR_CORREO_DEL_EQUIPO` | a dónde quieres los avisos |

4. Si vas a usar el aviso por correo, configura el nodo **Correo al equipo** con una
   credencial SMTP. Para probar en local, usa mailpit que ya viene en tu Docker:
   host `mailpit`, puerto `1025`, sin usuario ni contraseña. Los correos aparecen en
   <http://localhost:8025>.

5. **Activa el workflow** con el interruptor de arriba a la derecha. Los webhooks de
   producción solo funcionan con el workflow activo.

6. Copia la URL de producción del nodo **Mensajes de WhatsApp**. Se verá así:

   ```
   https://a1b2c3d4.ngrok-free.app/webhook/whatsapp-mavkora
   ```

---

## Paso 5 · Conectar el webhook

De vuelta en Meta, en **WhatsApp → Configuración → Webhooks → Editar**:

- **URL de devolución de llamada:** la URL que copiaste de n8n.
- **Token de verificación:** el mismo `WHATSAPP_VERIFY_TOKEN`.

Dale a **Verificar y guardar**. Si todo está bien, Meta acepta al instante. Si falla,
revisa la sección de problemas más abajo.

Después, en la misma pantalla, pulsa **Administrar** y **suscríbete al campo `messages`**.
Este paso se olvida con frecuencia: sin él Meta nunca te enviará nada.

---

## Paso 6 · Probar

Escríbele **"hola"** al número de prueba desde tu celular.

Deberías recibir el menú principal con cinco opciones. Prueba el recorrido completo:

1. Toca **Solicitar cotización** → elige un servicio → da tu nombre, correo y una
   descripción.
2. Entra a <http://localhost:8000/admin/leads>. El lead debe estar ahí.
3. Vuelve a WhatsApp y toca **Agendar reunión** → elige un horario.
4. Revisa <http://localhost:8000/admin/citas>.
5. Escribe **"quiero hablar con un asesor"**. El bot debe escalar y quedarse callado.
6. En <http://localhost:8000/admin/conversaciones> verás el hilo completo y podrás
   devolvérselo al bot cuando termines de atender.

Para probar la IA, escribe algo que no esté en el menú, por ejemplo:
*"¿ustedes trabajan con empresas de logística?"*

---

## Cosas importantes que debes saber

### La ventana de 24 horas

Meta solo permite enviar **mensajes libres dentro de las 24 horas** siguientes al último
mensaje del cliente. Pasado ese plazo, solo puedes enviar **plantillas aprobadas** por Meta.

Para un bot que responde, esto no es problema: siempre contestas dentro de la ventana.
Pero si algún día quieres enviar recordatorios de reuniones agendadas, vas a necesitar
crear y aprobar una plantilla en Meta. No está implementado todavía.

### El token temporal caduca a las 24 horas

El token que te da Meta para probar dura un día. Para producción necesitas un token
permanente:

1. En [business.facebook.com](https://business.facebook.com) → **Configuración del negocio
   → Usuarios → Usuarios del sistema**.
2. Crea un usuario del sistema con rol de administrador.
3. Genera un token asignándole la app y los permisos `whatsapp_business_messaging` y
   `whatsapp_business_management`.
4. Elige **sin caducidad** y guárdalo en la credencial `Meta WhatsApp Token` de n8n.

### El costo de la IA (y cómo funciona sin ella)

**La IA viene apagada por defecto.** El bot funciona completo sin ella y sin costo alguno.

Cuando el cliente escribe texto libre, el bot responde en tres niveles, del más barato
al más caro:

1. **Base de conocimiento local** — `app/Services/Bot/FaqMatcher.php`. Gratis. Reconoce
   por palabras clave las preguntas más frecuentes y responde con datos de
   `config/mavkora.php`:

   | Pregunta del cliente | Qué responde |
   | --- | --- |
   | precios, cuánto cuesta, tarifas | Explica que se cotiza por proyecto y ofrece cotizar |
   | cuánto demora, plazos | Explica que depende del alcance y ofrece agendar |
   | horario, a qué hora atienden | El horario de `config/mavkora.php` |
   | dónde están, dirección, remoto | Ubicación y que se trabaja a distancia |
   | cómo trabajan, proceso | Los 6 pasos del proceso |
   | tecnologías, con qué programan | El stack completo |
   | soporte, garantía, mantenimiento | El servicio de soporte |
   | portafolio, experiencia, casos | Enlace al portafolio y oferta de reunión |
   | quiénes son, qué es Mavkora | Presentación de la empresa |
   | formas de pago, cuotas | Que se acuerdan en la propuesta |

2. **La IA (Claude)** — solo si `BOT_AI_ENABLED=true`. Cubre lo que la base de
   conocimiento no reconoce.

3. **Salida honesta** — si nada coincide y la IA está apagada, el bot admite que no
   entendió y ofrece tres botones concretos, incluido hablar con un asesor. No repite
   el menú entero, que es lo que más frustra.

> **Los precios se responden siempre desde el nivel 1, aunque la IA esté encendida.**
> Es a propósito: una respuesta fija no puede improvisar una cifra que después
> tengas que sostener.

**Si algún día quieres encender la IA:** carga saldo en
[console.anthropic.com](https://console.anthropic.com) (mínimo 5 USD), crea la credencial
`Anthropic API Key` en n8n y pon `BOT_AI_ENABLED=true` en el `.env`.

Costo aproximado: **un centavo de dólar por consulta** con el modelo configurado
(Claude Opus 5). Unas 500 consultas al mes salen en unos 5 USD. Si quieres gastar menos,
cambia `model` a `claude-haiku-4-5` en el nodo *Preparar consulta a Claude* de n8n:
es unas cinco veces más barato y para responder preguntas frecuentes rinde bien.

Después de cualquier cambio en el `.env`:

```bash
docker compose exec app php artisan config:clear
```

### Cambiar servicios u horarios

Todo está en `config/mavkora.php`. Si cambias un servicio, el cambio se refleja a la vez
en el formulario de la web, en el menú del bot, en lo que la IA sabe y en el panel.
Tras editarlo corre `php artisan config:clear`.

---

## Si algo falla

**Meta dice "No se pudo validar la URL de devolución de llamada"**
El workflow de n8n no está activo, o la URL del túnel cambió. ngrok da una URL nueva
cada vez que lo reinicias en el plan gratuito: hay que actualizarla en Meta y en
`N8N_WEBHOOK_URL`.

**El bot no responde nada**
Revisa que estés suscrito al campo `messages` en Meta. Es el olvido más común.
Después mira las ejecuciones en n8n (**Executions**) para ver si el webhook llegó.

**n8n muestra 401 en "Laravel: procesar mensaje"**
La `BOT_API_KEY` del `.env` y la credencial `Mavkora Bot API` no coinciden. Si acabas de
cambiar el `.env`, falta `php artisan config:clear`.

**n8n muestra 503 en "Laravel: procesar mensaje"**
`BOT_API_KEY` está vacía en el `.env`. Corre `php artisan mavkora:bot-key`.

**El bot responde dos veces al mismo mensaje**
No debería: la tabla `messages` tiene un índice único sobre `wa_message_id` que descarta
los reintentos de Meta. Si pasa, revisa que la migración se haya aplicado.

**Los horarios que ofrece están corridos**
La aplicación corre en UTC y el negocio en `America/Bogota`. La conversión es explícita en
`SlotFinder`. Si cambiaste `mavkora.schedule.timezone`, corre `php artisan config:clear`.

**Ver los registros de Laravel**

```bash
docker compose exec app php artisan pail
```

---

## Para llevarlo a producción

Esto queda pendiente y vale la pena hacerlo antes de darle el número a clientes reales:

- [ ] Subir todo a un VPS con dominio y HTTPS propio, en lugar del túnel de ngrok.
- [ ] Token permanente de Meta (usuario del sistema), no el temporal.
- [ ] Número propio verificado en Meta, no el de prueba.
- [ ] `APP_DEBUG=false` y `APP_ENV=production` en el `.env`.
- [ ] Cambiar las contraseñas de MySQL del `docker-compose.yml`.
- [ ] Cerrar el puerto 5678 de n8n al público: solo tú deberías entrar al panel.
- [ ] SMTP real para los avisos, en lugar de mailpit.
- [ ] Poner el proyecto bajo git, con respaldos.
- [ ] Plantillas de Meta aprobadas si vas a enviar recordatorios de reuniones.
