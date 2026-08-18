# Cómo probar Mavkora

Guía rápida para el día a día: arrancar, probar el bot, ver los datos y saber qué
hay que volver a correr después de cambiar cada cosa.

Todos los comandos se ejecutan desde la carpeta del proyecto. En VS Code:
menú **Terminal → Nueva terminal** (ya se abre en la carpeta correcta).

---

## 1. Arrancar todo

```bash
docker compose up -d
```

Espera unos 30 segundos y comprueba que los 8 servicios estén arriba:

```bash
docker compose ps
```

Todos deben decir `Up`. Los que tienen chequeo de salud deben decir `(healthy)`.

> **Si sale `failed to connect to the docker API`**, es que Docker Desktop está
> cerrado. Ábrelo desde el menú Inicio, espera a que el ícono de la ballena deje
> de moverse (tarda 1–2 minutos) y repite el comando.

### Dónde queda cada cosa

| Servicio | URL |
| --- | --- |
| Sitio web y panel | <http://localhost:8000> |
| n8n (chatbot) | <http://localhost:5678> |
| phpMyAdmin (base de datos) | <http://localhost:8080> |
| Mailpit (correos de prueba) | <http://localhost:8025> |

---

## 2. Probar el bot

### Opción A — Conversación de ejemplo (la más rápida)

Corre sola una conversación completa: pregunta de precios, menú, cotización,
agendamiento y escalado a un asesor.

```bash
docker compose exec app php artisan mavkora:bot-demo --guion
```

**Úsalo como prueba de humo:** cada vez que cambies algo del bot, córrelo. Si
llega hasta el final sin errores, no rompiste nada.

### Opción B — Conversar tú mismo

```bash
docker compose exec app php artisan mavkora:bot-demo
```

Escribe como si fueras el cliente. Cuando el bot muestre opciones numeradas,
escribe el número (`1`, `2`, `3`) — equivale a tocar el botón en WhatsApp.
Escribe `salir` para terminar.

Para empezar con la conversación en blanco:

```bash
docker compose exec app php artisan mavkora:bot-demo --reset
```

### Opción C — Simular a varios clientes

Cada número es una conversación independiente, con su propio historial y su
propio paso del flujo:

```bash
docker compose exec app php artisan mavkora:bot-demo --numero=573009998877
```

---

## 3. Ver los resultados

Después de conversar con el bot, los datos quedan guardados. Entra al panel
(<http://localhost:8000>, inicia sesión) y revisa:

| Pantalla | Qué muestra |
| --- | --- |
| `/admin/dashboard` | Resumen: leads nuevos, reuniones próximas, quién espera un asesor |
| `/admin/leads` | Todos los leads, del bot y del formulario web |
| `/admin/conversaciones` | El hilo completo de cada chat; aquí devuelves la conversación al bot |
| `/admin/citas` | Reuniones agendadas |

También puedes mirar la base de datos directamente en <http://localhost:8080>
(usuario `mavkora`, contraseña `secret`, base de datos `mavkora`).

---

## 4. Qué correr después de cambiar cada cosa

Esta es la tabla más útil de la guía.

| Cambiaste... | Qué tienes que hacer |
| --- | --- |
| **Lógica del bot** (`app/Services/Bot/…`) | Nada. Vuelve a correr el comando y ya. |
| **Un controlador o modelo** (`app/…`) | Nada. Refresca el navegador. |
| **Una vista** (`resources/views/…`) | Nada. Refresca el navegador. |
| **`config/mavkora.php`** (servicios, horarios, contacto) | `docker compose exec app php artisan config:clear` |
| **El archivo `.env`** | `docker compose exec app php artisan config:clear` |
| **Una migración nueva** | `docker compose exec app php artisan migrate` |
| **`composer.json`** | `docker compose exec app composer install` |
| **`docker-compose.yml`** | `docker compose up -d` |
| **`docker/php/Dockerfile`** | Ver «Reconstruir la imagen» abajo |

> **La regla práctica:** si tocas un archivo `.php` de `app/` o una vista, no hay
> que hacer nada. Si tocas configuración, limpia la caché.

### Reconstruir la imagen de PHP

Solo hace falta si cambias el `Dockerfile`. En esta máquina el constructor moderno
de Docker no resuelve DNS, así que hay que usar el clásico:

```bash
DOCKER_BUILDKIT=0 COMPOSE_DOCKER_CLI_BUILD=0 docker compose build app
```

Y en PowerShell:

```bash
$env:DOCKER_BUILDKIT="0"; $env:COMPOSE_DOCKER_CLI_BUILD="0"; docker compose build app
```

Después: `docker compose up -d`

---

## 5. Probar la web

El formulario de cotización de la página principal guarda en la misma tabla que
el bot. Para probarlo: abre <http://localhost:8000>, dale a **Solicitar
Cotización**, llénalo y envíalo. Debe aparecerte un mensaje verde de
confirmación, y el lead debe salir en `/admin/leads` con origen «Formulario web».

---

## 6. Empezar de cero

**Borrar solo las conversaciones y leads de prueba**, conservando tu usuario:

```bash
docker compose exec app php artisan tinker --execute "App\Models\Message::query()->delete(); App\Models\Appointment::query()->delete(); App\Models\Conversation::query()->delete(); App\Models\Lead::query()->delete(); echo 'Datos de prueba borrados';"
```

> El orden importa: primero los mensajes, después las citas, luego las
> conversaciones y por último los leads. Al revés falla, porque unas tablas
> apuntan a otras.

**Borrar toda la base de datos y volver a crearla** (se pierde también tu usuario
del panel):

```bash
docker compose exec app php artisan migrate:fresh
```

---

## 7. Ver los errores

Cuando algo falle, el registro de Laravel dice qué pasó:

```bash
docker compose exec app php artisan pail
```

Déjalo abierto en una terminal mientras pruebas en otra. Para salir: `Ctrl + C`.

Los registros de un contenedor específico:

```bash
docker compose logs -f app
```

---

## 8. Apagar

```bash
docker compose stop
```

Esto apaga los contenedores pero conserva la base de datos. Para volver:
`docker compose up -d`.

> **Cuidado con `docker compose down -v`**: la `-v` borra los volúmenes, y con
> ellos toda la base de datos y la configuración de n8n. Sin la `-v` es seguro.

---

## Problemas comunes

**`failed to connect to the docker API`**
Docker Desktop está cerrado. Ábrelo y espera a que arranque.

**El navegador no carga <http://localhost:8000>**
Revisa `docker compose ps`. Si `nginx` o `app` no están arriba, míra los registros
con `docker compose logs app`.

**Cambié `config/mavkora.php` y no se ve el cambio**
Falta `docker compose exec app php artisan config:clear`.

**El bot no ofrece horarios para agendar**
Es correcto si ya pasó la hora. Se exigen 24 horas de anticipación y el horario es
de lunes a viernes de 9:00 a 17:00; un martes por la tarde el primer hueco cae el
jueves. Se ajusta en `config/mavkora.php`, en `schedule`.

**«Nothing to migrate» con base de datos nueva**
Es normal: el contenedor corre las migraciones solo al arrancar.

**Errores de descarga de Docker (`no such host`)**
El DNS de tu red falla de forma intermitente. Reintenta el comando; Docker
conserva lo que ya bajó. La solución de fondo: Docker Desktop → Settings →
Docker Engine → agregar `"dns": ["8.8.8.8", "1.1.1.1"]` y aplicar.
