<?php

namespace App\Services\Bot;

use Illuminate\Support\Str;

/**
 * Responde las preguntas frecuentes sin llamar a ninguna API.
 *
 * Es la capa gratuita del bot: reconoce por palabras clave las preguntas que más
 * hace un cliente y contesta con datos de config/mavkora.php. Se consulta SIEMPRE
 * antes que la IA, por dos motivos:
 *
 *   1. No cuesta nada, y cubre la mayoría del texto libre que llega.
 *   2. En temas sensibles —sobre todo precios— una respuesta fija es mejor que
 *      una generada: no hay forma de que el bot improvise una cifra.
 *
 * Si no reconoce nada devuelve null, y ahí sí decide ConversationFlow si pasa a
 * la IA o cae al menú.
 */
class FaqMatcher
{
    /**
     * Devuelve la respuesta lista para enviar, o null si no reconoció la pregunta.
     *
     * @return array<string, mixed>|null
     */
    public function answer(string $text): ?array
    {
        $needle = $this->normalize($text);

        if ($needle === '') {
            return null;
        }

        foreach ($this->topics() as $pattern => $builder) {
            if (preg_match($pattern, $needle)) {
                return $builder();
            }
        }

        return null;
    }

    /**
     * Patrón => constructor de la respuesta. El orden importa: gana el primero
     * que coincida, así que los temas más específicos van arriba.
     *
     * @return array<string, callable(): array<string, mixed>>
     */
    private function topics(): array
    {
        return [
            // Precios. El más frecuente y el más delicado: nunca damos una cifra.
            '/\b(precio|precios|costo|costos|tarifa|tarifas|valor|cobran|cobras|cuanto (cuesta|vale|sale|seria)|que tan caro|economico)\b/' => fn () => $this->precios(),

            // Formas de pago.
            '/\b(forma de pago|formas de pago|como se paga|como pago|financiacion|cuotas|anticipo|facturan|factura)\b/' => fn () => $this->pagos(),

            // Tiempos de entrega.
            '/\b(cuanto (demora|tarda|toma|se demora)|tiempo de entrega|plazo|plazos|cuando (estaria|lo entregan)|demoran|rapido)\b/' => fn () => $this->tiempos(),

            // Horario de atención.
            '/\b(horario|horarios|a que hora|que horas|cuando atienden|estan abiertos|dias de atencion|atienden hoy)\b/' => fn () => $this->horario(),

            // Ubicación.
            '/\b(donde (estan|quedan|los encuentro|se ubican)|ubicacion|direccion|oficina|oficinas|que ciudad|son de|presencial|remot(o|a|amente))\b/' => fn () => $this->ubicacion(),

            // Cómo trabajan.
            '/\b(como (trabajan|es el proceso|empezamos|iniciamos)|proceso|metodologia|pasos|etapas|como funciona)\b/' => fn () => $this->proceso(),

            // Tecnologías.
            '/\b(tecnologia|tecnologias|lenguaje|lenguajes|framework|frameworks|stack|con que (trabajan|programan|desarrollan)|laravel|react|vue|python|node|flutter|wordpress)\b/' => fn () => $this->tecnologias(),

            // Soporte y garantía.
            '/\b(soporte|garantia|mantenimiento|despues de (entregar|la entrega)|acompanan|post venta|postventa)\b/' => fn () => $this->soporte(),

            // Portafolio y experiencia.
            '/\b(portafolio|portfolio|trabajos|casos de exito|clientes|ejemplos|experiencia|proyectos anteriores|han hecho)\b/' => fn () => $this->portafolio(),

            // Quiénes son.
            '/\b(quienes son|quien es mavkora|que es mavkora|a que se dedican|sobre ustedes|de que se trata|que hacen)\b/' => fn () => $this->empresa(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Respuestas
    |--------------------------------------------------------------------------
    */

    private function precios(): array
    {
        return Reply::buttons(
            "Buena pregunta 💬\n\n"
            ."En Mavkora *no manejamos precios de lista*, porque cada proyecto es distinto: "
            ."no cuesta lo mismo una web corporativa que una plataforma a medida con integraciones.\n\n"
            ."Lo que sí hacemos es darte una *cotización concreta y sin compromiso*. "
            ."Para eso necesitamos entender qué necesitas: son 4 preguntas rápidas.",
            [
                ['id' => 'menu_quote', 'title' => 'Quiero cotizar'],
                ['id' => 'menu_appointment', 'title' => 'Agendar reunión'],
                ['id' => 'back_menu', 'title' => 'Menú principal'],
            ]
        );
    }

    private function pagos(): array
    {
        return Reply::buttons(
            "Las condiciones de pago se acuerdan con cada cliente según el tamaño y la "
            ."duración del proyecto, y quedan por escrito en la propuesta 📄\n\n"
            ."Un asesor te explica las opciones cuando revisemos tu caso.",
            [
                ['id' => 'menu_quote', 'title' => 'Pedir propuesta'],
                ['id' => 'menu_human', 'title' => 'Hablar con asesor'],
            ]
        );
    }

    private function tiempos(): array
    {
        return Reply::buttons(
            "Depende del alcance ⏱️\n\n"
            ."Un sitio corporativo no toma lo mismo que una plataforma con varios módulos. "
            ."Por eso el *paso 2 de nuestro proceso es el análisis*: ahí evaluamos lo que "
            ."necesitas y te damos un cronograma real, no una promesa al aire.\n\n"
            ."Y cumplimos: la entrega a tiempo es uno de nuestros compromisos.",
            [
                ['id' => 'menu_quote', 'title' => 'Contarles mi caso'],
                ['id' => 'menu_appointment', 'title' => 'Agendar reunión'],
            ]
        );
    }

    private function horario(): array
    {
        $inicio = config('mavkora.schedule.start', '09:00');
        $fin = config('mavkora.schedule.end', '17:00');

        return Reply::buttons(
            "🕘 *Horario comercial*\nLunes a viernes, de {$inicio} a {$fin} (hora de Colombia).\n\n"
            ."🛠️ *Soporte técnico*\nDisponible 24/7 para clientes con contrato de soporte.\n\n"
            ."Este asistente responde a cualquier hora, así que puedes dejarme tus datos "
            ."ahora y un asesor te contacta en horario comercial.",
            [
                ['id' => 'menu_quote', 'title' => 'Dejar mis datos'],
                ['id' => 'back_menu', 'title' => 'Menú principal'],
            ]
        );
    }

    private function ubicacion(): array
    {
        $company = config('mavkora.company');

        return Reply::buttons(
            "📍 Estamos en *{$company['location']}*.\n\n"
            ."Trabajamos con clientes de forma remota sin problema: la mayoría de nuestros "
            ."proyectos se ejecutan así, con reuniones virtuales de seguimiento.\n\n"
            ."📧 {$company['email']}\n📞 {$company['phone']}",
            [
                ['id' => 'menu_appointment', 'title' => 'Agendar reunión'],
                ['id' => 'back_menu', 'title' => 'Menú principal'],
            ]
        );
    }

    private function proceso(): array
    {
        $body = "Así trabajamos en Mavkora 👇\n\n";

        foreach (config('mavkora.process') as $paso) {
            $body .= "*{$paso['step']}. {$paso['name']}* — {$paso['detail']}\n";
        }

        $body .= "\nTodo empieza con una reunión para entender qué necesitas.";

        return Reply::buttons($body, [
            ['id' => 'menu_appointment', 'title' => 'Agendar reunión'],
            ['id' => 'menu_quote', 'title' => 'Solicitar cotización'],
            ['id' => 'back_menu', 'title' => 'Menú principal'],
        ]);
    }

    private function tecnologias(): array
    {
        return Reply::buttons(
            "Trabajamos con tecnología actual y probada 🧩\n\n"
            ."*Web:* Laravel, PHP, React, Vue.js, Node.js, TailwindCSS\n"
            ."*Móvil:* Swift (iOS), Kotlin (Android), Flutter\n"
            ."*Datos e IA:* Python, MySQL, APIs de OpenAI, Google Cloud y Anthropic\n"
            ."*Infraestructura:* AWS, Azure, Google Cloud, Docker, Kubernetes, Git\n\n"
            ."Elegimos la tecnología según lo que tu proyecto necesita, no al revés.",
            [
                ['id' => 'menu_services', 'title' => 'Ver servicios'],
                ['id' => 'menu_quote', 'title' => 'Cotizar proyecto'],
            ]
        );
    }

    private function soporte(): array
    {
        $servicio = config('mavkora.services.support');

        return Reply::buttons(
            "*{$servicio['name']}* 🛠️\n\n"
            ."No desaparecemos después de entregar: el soporte es el paso 6 de nuestro proceso.\n\n"
            ."✅ Monitoreo de servidores las 24 horas\n"
            ."✅ Help desk con tiempos de respuesta garantizados\n"
            ."✅ Mantenimiento periódico, backups mensuales e informes",
            [
                ['id' => 'menu_quote', 'title' => 'Quiero soporte'],
                ['id' => 'menu_human', 'title' => 'Hablar con asesor'],
            ]
        );
    }

    private function portafolio(): array
    {
        return Reply::buttons(
            "Puedes ver nuestro portafolio en la web 🌐\n\n"
            .config('app.url', 'https://mavkora.com')."/portfolio\n\n"
            ."Si prefieres, en una reunión te mostramos casos parecidos al proyecto que "
            ."tienes en mente, que suele ser más útil que un listado general.",
            [
                ['id' => 'menu_appointment', 'title' => 'Agendar reunión'],
                ['id' => 'back_menu', 'title' => 'Menú principal'],
            ]
        );
    }

    private function empresa(): array
    {
        $company = config('mavkora.company');

        return Reply::buttons(
            "*{$company['name']}* — {$company['tagline']} 🚀\n\n"
            ."{$company['description']}\n\n"
            ."Somos una empresa de tecnología en {$company['location']}. Acompañamos a "
            ."otras empresas a digitalizarse: desde el software a medida hasta la "
            ."infraestructura y el soporte del día a día.",
            [
                ['id' => 'menu_services', 'title' => 'Ver servicios'],
                ['id' => 'menu_quote', 'title' => 'Solicitar cotización'],
                ['id' => 'back_menu', 'title' => 'Menú principal'],
            ]
        );
    }

    /**
     * Minúsculas y sin tildes, para que «cuánto» y «cuanto» sean lo mismo.
     */
    private function normalize(string $text): string
    {
        return trim(Str::lower(Str::ascii(trim($text))));
    }
}
