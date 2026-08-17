<?php

/*
|--------------------------------------------------------------------------
| Configuración de negocio de Mavkora
|--------------------------------------------------------------------------
|
| Fuente única de verdad para los servicios, el proceso de trabajo, los datos
| de contacto y el horario de atención. La consumen tres lugares a la vez:
|
|   1. El formulario de cotización de la landing (select de servicios).
|   2. El menú y la base de conocimiento del chatbot de WhatsApp.
|   3. El panel administrativo, para mostrar etiquetas legibles.
|
| Si un servicio cambia, se cambia aquí y los tres lugares quedan al día.
|
*/

return [

    'company' => [
        'name' => 'Mavkora',
        'tagline' => 'Desarrollamos el futuro de tu empresa',
        'description' => 'Software a medida, Inteligencia Artificial, Automatización y Soporte Tecnológico.',
        'email' => env('MAVKORA_EMAIL', 'info@mavkora.com'),
        'phone' => env('MAVKORA_PHONE', '+57 300 123 4567'),
        'whatsapp' => env('MAVKORA_WHATSAPP', '573001234567'),
        'location' => 'Medellín, Colombia',
    ],

    /*
    |--------------------------------------------------------------------------
    | Catálogo de servicios
    |--------------------------------------------------------------------------
    |
    | Las claves son los mismos value que ya usa el <select> del modal de
    | cotización, así que no hay que traducir nada entre la web y el bot.
    |
    | 'label' se usa en menús de WhatsApp: Meta corta los títulos de fila a
    | 24 caracteres, por eso son cortos. 'name' es el nombre completo.
    |
    */
    'services' => [
        'web' => [
            'label' => 'Desarrollo Web',
            'name' => 'Desarrollo Web a Medida',
            'summary' => 'Sitios corporativos, e-commerce y aplicaciones SaaS rápidas y escalables.',
            'details' => [
                'Tecnologías: Laravel, React, Vue.js, MySQL y TailwindCSS',
                'Enfoque UX/UI personalizado, intuitivo y premium',
                'Optimización de velocidad y Core Web Vitals para mejor posicionamiento',
            ],
        ],
        'mobile' => [
            'label' => 'Apps Móviles',
            'name' => 'Aplicaciones Móviles',
            'summary' => 'Apps nativas e híbridas de alto rendimiento para iOS y Android.',
            'details' => [
                'Plataformas: iOS (Swift), Android (Kotlin) e híbridas (Flutter)',
                'Diseños interactivos con micro-animaciones premium',
                'Integración de notificaciones push, GPS y pagos seguros',
            ],
        ],
        'ai' => [
            'label' => 'Inteligencia Artificial',
            'name' => 'Inteligencia Artificial y Automatización',
            'summary' => 'Automatización de procesos, asistentes conversacionales y modelos predictivos.',
            'details' => [
                'Agentes de soporte conversacionales (chatbots inteligentes)',
                'Modelos predictivos y análisis inteligente de datos corporativos',
                'Integración de APIs de OpenAI, Google Cloud y Anthropic',
            ],
        ],
        'cloud' => [
            'label' => 'Infraestructura Cloud',
            'name' => 'Infraestructura Cloud',
            'summary' => 'Migración y administración en la nube con 99.9% de disponibilidad.',
            'details' => [
                'Proveedores líderes: AWS, Google Cloud y Microsoft Azure',
                'Kubernetes, orquestación de contenedores y dockerización',
                'Automatización de despliegues (CI/CD) e infraestructura como código',
            ],
        ],
        'cyber' => [
            'label' => 'Ciberseguridad',
            'name' => 'Ciberseguridad',
            'summary' => 'Auditorías, pentesting y blindaje de tus datos e infraestructura.',
            'details' => [
                'Auditorías periódicas de seguridad y pruebas de penetración',
                'Implementación de SSL, cifrado AES de bases de datos y firewalls avanzados',
                'Cumplimiento estricto de normativas internacionales de privacidad',
            ],
        ],
        'support' => [
            'label' => 'Soporte Técnico',
            'name' => 'Soporte Técnico Especializado',
            'summary' => 'Monitoreo 24/7, help desk con SLA y mantenimiento preventivo.',
            'details' => [
                'Monitoreo reactivo y proactivo de servidores las 24 horas',
                'Help desk con tiempos de respuesta SLA mínimos y soporte telefónico',
                'Mantenimiento periódico, backups mensuales e informes de infraestructura',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Proceso de trabajo
    |--------------------------------------------------------------------------
    |
    | Los mismos 6 pasos que muestra la landing. El bot los cita cuando le
    | preguntan "¿cómo trabajan?".
    |
    */
    'process' => [
        ['step' => 1, 'name' => 'Reunión', 'detail' => 'Entendemos tus necesidades'],
        ['step' => 2, 'name' => 'Análisis', 'detail' => 'Evaluamos y planteamos la solución'],
        ['step' => 3, 'name' => 'Diseño', 'detail' => 'Diseñamos la solución a tu medida'],
        ['step' => 4, 'name' => 'Desarrollo', 'detail' => 'Construimos tu solución'],
        ['step' => 5, 'name' => 'Entrega', 'detail' => 'Probamos y entregamos el producto'],
        ['step' => 6, 'name' => 'Soporte', 'detail' => 'Te acompañamos siempre'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Horario de atención y agendamiento
    |--------------------------------------------------------------------------
    |
    | Define qué franjas ofrece el bot cuando alguien quiere agendar la reunión
    | inicial (paso 1 del proceso).
    |
    */
    'schedule' => [
        'timezone' => 'America/Bogota',

        // Días hábiles en formato ISO-8601: 1 = lunes ... 7 = domingo.
        'days' => [1, 2, 3, 4, 5],

        'start' => '09:00',
        'end' => '17:00',

        // Duración de cada franja ofrecida, en minutos.
        'slot_minutes' => 30,

        // Anticipación mínima para agendar. Evita que alguien reserve
        // una reunión para dentro de diez minutos.
        'lead_time_hours' => 24,

        // Hasta cuántos días hacia adelante se ofrecen franjas.
        'horizon_days' => 10,

        // Cuántas franjas mostrar en el menú de WhatsApp.
        // Meta permite máximo 10 filas por lista interactiva.
        'slots_offered' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Chatbot
    |--------------------------------------------------------------------------
    */
    'bot' => [
        // Clave compartida con n8n. Se envía en la cabecera X-Bot-Key.
        'api_key' => env('BOT_API_KEY'),

        // Apagada por defecto, a propósito: la IA se cobra por consulta y nadie
        // debería empezar a gastar por olvidar una variable de entorno.
        //
        // Con la IA apagada el bot sigue respondiendo texto libre gracias a
        // App\Services\Bot\FaqMatcher, que no tiene ningún costo. La IA solo
        // añade capacidad para preguntas que la base de conocimiento no cubre.
        'ai_enabled' => (bool) env('BOT_AI_ENABLED', false),

        // Tras cuántas horas de silencio la conversación vuelve a empezar
        // desde el saludo en lugar de continuar a mitad de un formulario.
        'session_timeout_hours' => (int) env('BOT_SESSION_TIMEOUT_HOURS', 12),

        // A dónde avisar cuando un cliente pide hablar con una persona.
        'handoff_email' => env('BOT_HANDOFF_EMAIL', env('MAVKORA_EMAIL', 'info@mavkora.com')),

        // Cuánto tiempo queda el bot en silencio tras un escalado a humano,
        // para no interrumpir mientras el asesor atiende.
        'handoff_silence_hours' => (int) env('BOT_HANDOFF_SILENCE_HOURS', 24),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Cloud API (Meta)
    |--------------------------------------------------------------------------
    |
    | Laravel no llama directamente a Meta: eso lo hace n8n. Estos valores
    | están aquí para validar la firma del webhook y para que la guía de
    | instalación tenga un único lugar de referencia.
    |
    */
    'whatsapp' => [
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),
    ],

];
