# Mavkora

Sitio corporativo y panel administrativo de Mavkora, con chatbot de WhatsApp integrado.

**Stack:** Laravel 13 · PHP 8.3 · AdminLTE 3 · Vite · MySQL 8.4 · Redis · n8n

---

## Qué incluye

- **Sitio público** — landing con servicios, proceso, tecnologías y portafolio.
- **Formulario de cotización** — guarda los leads en base de datos.
- **Panel administrativo** — leads, conversaciones de WhatsApp y citas agendadas.
- **Chatbot de WhatsApp** — responde preguntas, captura leads, agenda reuniones y
  escala a un asesor humano. Ver [docs/CHATBOT-WHATSAPP.md](docs/CHATBOT-WHATSAPP.md).

---

## Levantar el proyecto

Solo necesitas Docker Desktop; PHP, Node y Composer corren dentro de los contenedores.

```bash
docker compose up -d --build
```

```bash
docker compose exec app cp .env.example .env && docker compose exec app php artisan key:generate
```

Ajusta la conexión a MySQL en el `.env` (por defecto viene en SQLite):

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mavkora
DB_USERNAME=mavkora
DB_PASSWORD=secret
```

```bash
docker compose exec app php artisan migrate --force
```

### Dónde queda cada cosa

| Servicio | URL |
| --- | --- |
| Sitio y panel | <http://localhost:8000> |
| Vite (assets en desarrollo) | <http://localhost:5173> |
| n8n (chatbot) | <http://localhost:5678> |
| phpMyAdmin | <http://localhost:8080> |
| Mailpit (correos de prueba) | <http://localhost:8025> |

Crea tu usuario del panel en <http://localhost:8000/register>.

---

## Configuración del negocio

Servicios, proceso de trabajo, horarios de atención y datos de contacto viven en un
solo archivo: [`config/mavkora.php`](config/mavkora.php).

Lo consumen a la vez el formulario de la web, el menú del chatbot, la base de
conocimiento de la IA y el panel administrativo. Si cambias un servicio ahí, cambia
en los cuatro lugares.

Después de editarlo:

```bash
docker compose exec app php artisan config:clear
```

---

## Comandos útiles

Ver los registros en vivo:

```bash
docker compose exec app php artisan pail
```

Generar la clave que comparten Laravel y n8n:

```bash
docker compose exec app php artisan mavkora:bot-key
```

Correr las pruebas:

```bash
docker compose exec app php artisan test
```

---

## Documentación

- [Respuestas del bot](docs/RESPUESTAS-DEL-BOT.md) — inventario de todo lo que contesta el chatbot y dónde editar cada texto.
- [Cómo probar](docs/COMO-PROBAR.md) — el día a día: arrancar, probar el bot, y qué volver a correr después de cambiar cada cosa.
- [Chatbot de WhatsApp](docs/CHATBOT-WHATSAPP.md) — instalación paso a paso, conexión
  con Meta, configuración de n8n y solución de problemas.
