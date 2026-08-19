# Todo lo que responde el chatbot

Inventario completo de cada mensaje que el bot puede enviar, qué lo dispara y en
qué archivo editarlo.

---

## Los tres archivos donde vive todo

| Archivo | Qué contiene | Cuándo tocarlo |
| --- | --- | --- |
| [`config/mavkora.php`](../config/mavkora.php) | Servicios, proceso, horarios, contacto | Cambian los datos del negocio |
| [`app/Services/Bot/FaqMatcher.php`](../app/Services/Bot/FaqMatcher.php) | Respuestas a preguntas libres | Quieres cambiar qué contesta ante una pregunta |
| [`app/Services/Bot/ConversationFlow.php`](../app/Services/Bot/ConversationFlow.php) | Menús y el paso a paso | Cambian los menús o el orden de las preguntas |

> **`config/mavkora.php` es especial:** lo que cambies ahí se refleja a la vez en
> el bot, en el formulario de la web y en el panel. Es el único archivo que
> necesita `php artisan config:clear` después de editarlo.

---

## A. Respuestas a preguntas libres

Están en `FaqMatcher.php`. Son **gratis**: no llaman a la IA. Si el cliente
escribe algo que contiene una de estas palabras, recibe esa respuesta.

| # | Tema | Palabras que lo disparan | Método a editar |
| --- | --- | --- | --- |
| 1 | **Precios** | precio, precios, costo, costos, tarifa, tarifas, valor, cobran, cuánto cuesta, cuánto vale, cuánto sale, qué tan caro, económico | `precios()` · línea 92 |
| 2 | **Formas de pago** | forma de pago, cómo se paga, cómo pago, financiación, cuotas, anticipo, factura, facturan | `pagos()` · línea 108 |
| 3 | **Tiempos de entrega** | cuánto demora, cuánto tarda, cuánto toma, tiempo de entrega, plazo, plazos, cuándo estaría, demoran, rápido | `tiempos()` · línea 121 |
| 4 | **Horario** | horario, horarios, a qué hora, qué horas, cuándo atienden, están abiertos, días de atención, atienden hoy | `horario()` · línea 136 |
| 5 | **Ubicación** | dónde están, dónde quedan, ubicación, dirección, oficina, qué ciudad, presencial, remoto, remota | `ubicacion()` · línea 153 |
| 6 | **Cómo trabajan** | cómo trabajan, proceso, metodología, pasos, etapas, cómo funciona, cómo empezamos | `proceso()` · línea 169 |
| 7 | **Tecnologías** | tecnología, lenguaje, framework, stack, con qué trabajan, con qué programan, laravel, react, vue, python, node, flutter, wordpress | `tecnologias()` · línea 186 |
| 8 | **Soporte y garantía** | soporte, garantía, mantenimiento, después de entregar, acompañan, postventa | `soporte()` · línea 202 |
| 9 | **Portafolio** | portafolio, trabajos, casos de éxito, clientes, ejemplos, experiencia, proyectos anteriores, han hecho | `portafolio()` · línea 219 |
| 10 | **Quiénes son** | quiénes son, qué es Mavkora, a qué se dedican, sobre ustedes, de qué se trata, qué hacen | `empresa()` · línea 233 |

> **El orden importa:** gana el primero que coincida. Si alguien escribe
> *"¿cuánto cuesta el soporte?"*, responde **Precios**, no Soporte, porque
> Precios está arriba en la lista.

### Cambiar una de estas respuestas

Abre `FaqMatcher.php`, busca el método (por ejemplo `precios()`) y edita el texto
entre comillas. No hay que reiniciar nada: vuelve a correr el bot y ya.

### Agregar palabras a un tema existente

En la lista `topics()` (línea 51), agrega la palabra separada con `|`:

```php
'/\b(precio|precios|costo|cuanto cuesta|vale la pena)\b/' => fn () => $this->precios(),
```

> Escribe las palabras **sin tildes y en minúscula**. El bot le quita las tildes
> al mensaje antes de comparar, así que `cotización` y `cotizacion` funcionan
> igual, pero el patrón debe ir sin tilde.

---

## B. Menús y paso a paso

Están en `ConversationFlow.php`.

| Qué responde | Cuándo aparece | Método · línea |
| --- | --- | --- |
| **Menú principal** (5 opciones) | Al saludar, al escribir `menu`, o al volver | `mainMenu()` · 147 |
| **Lista de servicios** | Toca «Nuestros servicios» | `showServices()` · 177 |
| **Detalle de un servicio** | Elige un servicio de la lista | `handleServiceChoice()` · 189 |
| **Pide el servicio a cotizar** | Toca «Solicitar cotización» | `startQuote()` · 221 |
| **No reconoció el servicio** | Escribe un servicio que no existe | `handleQuoteService()` · 238 |
| **Pide el nombre** (paso 2 de 4) | Después de elegir servicio | `askQuoteName()` · 253 |
| **Pide el correo** (paso 3 de 4) | Después del nombre | `handleQuoteName()` · 268 |
| **Correo inválido** | Escribe algo que no es un correo | `handleQuoteEmail()` · 290 |
| **Pide los detalles** (paso 4 de 4) | Después del correo | `askQuoteDetails()` · 312 |
| **Confirma el lead creado** | Al terminar los 4 pasos | `handleQuoteDetails()` · 323 |
| **Pide nombre para agendar** | Toca «Agendar» sin haber dado datos | `startAppointment()` · 373 |
| **Ofrece horarios** | Al agendar | `offerSlots()` · 408 |
| **No hay horarios libres** | Agenda llena o fuera de plazo | `offerSlots()` · 408 |
| **Confirma la reunión** | Elige un horario | `handleSlotChoice()` · 445 |
| **Datos de contacto** | Toca «Datos de contacto» | `showContact()` · 516 |
| **Escala a un asesor** | Pide hablar con una persona | `startHandoff()` · 534 |
| **No entendí** (3 botones) | Nada coincidió y la IA está apagada | `fallback()` · 565 |

### Palabras que funcionan en cualquier momento

| Escribe... | Qué pasa | Dónde se define |
| --- | --- | --- |
| `menu`, `inicio`, `volver`, `atras`, `cancelar`, `salir`, `empezar` | Vuelve al menú principal | `wantsMenu()` · 793 |
| asesor, humano, persona, agente, ejecutivo, vendedor, alguien real, hablar con alguien | Escala a una persona | `wantsHuman()` · 781 |
| hola, buenas, buenos días, buenas tardes, hey, qué tal, saludos | Se reconoce como saludo | `looksLikeGreeting()` · 802 |
| servicio, catálogo | Abre la lista de servicios | `matchMenuByText()` · 764 |
| cotiz…, presupuesto, propuesta | Inicia la cotización | `matchMenuByText()` · 764 |
| agend…, reunión, cita, reservar, calendario | Inicia el agendamiento | `matchMenuByText()` · 764 |
| contacto, correo, teléfono, dirección | Muestra los datos de contacto | `matchMenuByText()` · 764 |

---

## C. Datos del negocio

Están en `config/mavkora.php`. **Es el archivo que más vas a tocar.**

| Qué cambias | Dónde | Dónde se ve el cambio |
| --- | --- | --- |
| Correo, teléfono, WhatsApp, ciudad | `company` · línea 21 | Bot, pie de página de la web, panel |
| Los 6 servicios | `services` · línea 43 | Menú del bot, formulario de la web, panel |
| Los 6 pasos del proceso | `process` · línea 115 | Bot y web |
| Horario de atención | `schedule` · línea 133 | Horarios que ofrece el bot al agendar |
| Días hábiles | `schedule.days` · línea 137 | `[1,2,3,4,5]` = lunes a viernes. Agrega `6` para sábados |
| Duración de las reuniones | `schedule.slot_minutes` | 30 minutos por defecto |
| Anticipación mínima | `schedule.lead_time_hours` | 24 horas por defecto |
| Encender la IA | `bot.ai_enabled` | Ver [CHATBOT-WHATSAPP.md](CHATBOT-WHATSAPP.md) |

### Cada servicio tiene cuatro campos

```php
'web' => [
    'label' => 'Desarrollo Web',              // Título en el menú de WhatsApp (máx. 24 caracteres)
    'name' => 'Desarrollo Web a Medida',      // Nombre completo, en la web y el panel
    'summary' => 'Sitios corporativos...',    // Descripción corta bajo el título
    'details' => [                            // Viñetas del detalle
        'Tecnologías: Laravel, React...',
    ],
],
```

> **`label` no puede pasar de 24 caracteres** y **`summary` no puede pasar de 72**.
> Son límites de WhatsApp. El código los recorta solo para que Meta no rechace el
> mensaje, pero si te pasas se verá cortado.

**Después de editar este archivo:**

```bash
docker compose exec app php artisan config:clear
```

---

## Cómo probar un cambio

1. Edita el texto
2. Si tocaste `config/mavkora.php`, corre `config:clear`
3. Corre la conversación de ejemplo:

```bash
docker compose exec app php artisan mavkora:bot-demo --guion
```

4. O conversa tú mismo para probar una pregunta puntual:

```bash
docker compose exec app php artisan mavkora:bot-demo
```

---

## Formato de los mensajes en WhatsApp

WhatsApp usa su propio formato, no markdown:

| Escribes | Se ve |
| --- | --- |
| `*negrita*` | **negrita** (un solo asterisco) |
| `_cursiva_` | _cursiva_ |
| `~tachado~` | ~~tachado~~ |
| ` ```código``` ` | monoespaciado |

Los saltos de línea se escriben con `\n` dentro del texto. Los emojis funcionan
tal cual.

---

## Lo que el bot NO responde todavía

Para que sepas dónde están los límites actuales:

- **Audios, imágenes, ubicaciones y documentos.** Se registran en el panel para
  que un asesor los vea, pero el bot no los interpreta ni responde a ellos.
- **Preguntas fuera de los 10 temas** de la sección A. Sin IA, responde «no
  entendí» y ofrece hablar con un asesor.
- **Cancelar o cambiar una reunión ya agendada.** Hay que hacerlo desde el panel.
- **Recordatorios antes de la reunión.** Requiere plantillas aprobadas por Meta.
