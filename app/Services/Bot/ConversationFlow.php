<?php

namespace App\Services\Bot;

use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Lead;
use App\Models\Message;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * El cerebro del chatbot. Recibe un mensaje ya normalizado por n8n y decide
 * qué responder, manteniendo el estado en la tabla `conversations`.
 *
 * Vive en Laravel y no en n8n a propósito: aquí es testeable, versionable y
 * queda junto a los datos. n8n solo transporta.
 *
 * Modo híbrido: los caminos frecuentes (servicios, cotización, agenda) van por
 * menús deterministas; el texto libre que no encaja en ningún paso se delega a
 * la IA devolviendo needs_ai = true.
 */
class ConversationFlow
{
    // Pasos de la máquina de estados.
    private const STEP_START = 'start';
    private const STEP_MENU = 'menu';
    private const STEP_SERVICES = 'services';
    private const STEP_QUOTE_SERVICE = 'quote_service';
    private const STEP_QUOTE_NAME = 'quote_name';
    private const STEP_QUOTE_EMAIL = 'quote_email';
    private const STEP_QUOTE_DETAILS = 'quote_details';
    private const STEP_APPT_NAME = 'appt_name';
    private const STEP_APPT_SLOT = 'appt_slot';
    private const STEP_HANDOFF = 'handoff';

    public function __construct(
        private readonly KnowledgeBase $knowledge,
        private readonly SlotFinder $slots,
        private readonly FaqMatcher $faq,
    ) {}

    /**
     * Punto de entrada único.
     *
     * @param  array{wa_id: string, profile_name?: ?string, wa_message_id?: ?string, type?: string, text?: ?string, reply_id?: ?string}  $input
     * @return array<string, mixed>
     */
    public function handle(array $input): array
    {
        $conversation = $this->resolveConversation($input);

        // Meta reintenta la entrega de webhooks. Sin este corte, un reintento
        // haría que el bot conteste dos veces al mismo mensaje.
        if (! $this->recordIncoming($conversation, $input)) {
            return $this->respond($conversation, Reply::silence(), duplicate: true);
        }

        $text = trim((string) ($input['text'] ?? ''));
        $replyId = $input['reply_id'] ?? null;

        // Un asesor tomó la conversación: el bot se calla salvo que el cliente
        // pida explícitamente volver al menú. Igual se avisa al equipo, porque
        // un cliente esperando en silencio es un cliente que se pierde.
        if ($conversation->isSilencedForHandoff() && ! $this->wantsMenu($text, $replyId)) {
            return $this->respond($conversation, Reply::silence(), notify: [
                'type' => 'human_message',
                'conversation_id' => $conversation->id,
                'phone' => $conversation->wa_id,
                'name' => $conversation->profile_name ?? $conversation->lead?->name,
                'text' => $text,
            ]);
        }

        // Volver días después no debería caer a mitad de un formulario.
        if ($conversation->isStale() && $conversation->step !== self::STEP_START) {
            $conversation->step = self::STEP_MENU;
            $conversation->contextForget('quote', 'appointment');

            return $this->respond($conversation, $this->mainMenu($conversation, welcomeBack: true));
        }

        // Comandos globales, válidos en cualquier paso.
        if ($this->wantsHuman($text, $replyId)) {
            return $this->startHandoff($conversation);
        }

        if ($this->wantsMenu($text, $replyId)) {
            $conversation->status = Conversation::STATUS_BOT;

            return $this->respond($conversation, $this->mainMenu($conversation));
        }

        return match ($conversation->step) {
            self::STEP_START => $this->handleFirstContact($conversation, $text),
            self::STEP_SERVICES => $this->handleServiceChoice($conversation, $text, $replyId),
            self::STEP_QUOTE_SERVICE => $this->handleQuoteService($conversation, $text, $replyId),
            self::STEP_QUOTE_NAME => $this->handleQuoteName($conversation, $text),
            self::STEP_QUOTE_EMAIL => $this->handleQuoteEmail($conversation, $text, $replyId),
            self::STEP_QUOTE_DETAILS => $this->handleQuoteDetails($conversation, $text),
            self::STEP_APPT_NAME => $this->handleAppointmentName($conversation, $text),
            self::STEP_APPT_SLOT => $this->handleSlotChoice($conversation, $text, $replyId),
            default => $this->handleMenuChoice($conversation, $text, $replyId),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Primer contacto y menú
    |--------------------------------------------------------------------------
    */

    private function handleFirstContact(Conversation $conversation, string $text): array
    {
        $conversation->step = self::STEP_MENU;

        // Si el primer mensaje ya trae una pregunta de verdad, contestarla vale
        // más que soltar un menú encima. Un "hola" pelado sí va directo al menú.
        if ($text !== '' && ! $this->looksLikeGreeting($text)) {
            if ($answer = $this->faq->answer($text)) {
                return $this->respond($conversation, $answer);
            }

            if ($this->aiEnabled()) {
                return $this->respondWithAi($conversation, $text);
            }
        }

        return $this->respond($conversation, $this->mainMenu($conversation));
    }

    private function handleMenuChoice(Conversation $conversation, string $text, ?string $replyId): array
    {
        $choice = $replyId ?? $this->matchMenuByText($text);

        return match ($choice) {
            'menu_services' => $this->showServices($conversation),
            'menu_quote' => $this->startQuote($conversation),
            'menu_appointment' => $this->startAppointment($conversation),
            'menu_contact' => $this->showContact($conversation),
            default => $this->fallback($conversation, $text),
        };
    }

    private function mainMenu(Conversation $conversation, bool $welcomeBack = false): array
    {
        $conversation->step = self::STEP_MENU;

        $name = $this->firstName($conversation->profile_name);
        $greeting = $welcomeBack
            ? ($name ? "¡Hola de nuevo, {$name}!" : '¡Hola de nuevo!')
            : ($name ? "¡Hola, {$name}!" : '¡Hola!');

        $body = "{$greeting} 👋 Soy el asistente virtual de *Mavkora*.\n\n"
            ."Desarrollamos software a medida, inteligencia artificial, automatización y soporte tecnológico.\n\n"
            .'¿En qué te puedo ayudar hoy?';

        return Reply::list($body, 'Ver opciones', [[
            'rows' => [
                ['id' => 'menu_services', 'title' => 'Nuestros servicios', 'description' => 'Las 6 áreas en las que trabajamos'],
                ['id' => 'menu_quote', 'title' => 'Solicitar cotización', 'description' => 'Cuéntanos tu proyecto y te contactamos'],
                ['id' => 'menu_appointment', 'title' => 'Agendar reunión', 'description' => 'Reserva una reunión inicial sin costo'],
                ['id' => 'menu_contact', 'title' => 'Datos de contacto', 'description' => 'Correo, teléfono y ubicación'],
                ['id' => 'menu_human', 'title' => 'Hablar con un asesor', 'description' => 'Te conectamos con una persona del equipo'],
            ],
        ]], 'Escribe *menu* en cualquier momento para volver aquí');
    }

    /*
    |--------------------------------------------------------------------------
    | Servicios
    |--------------------------------------------------------------------------
    */

    private function showServices(Conversation $conversation): array
    {
        $conversation->step = self::STEP_SERVICES;

        return $this->respond($conversation, Reply::list(
            "Estos son nuestros servicios 👇\n\nElige uno para ver el detalle.",
            'Ver servicios',
            [['rows' => $this->serviceRows('svc_')]],
            'Mavkora · Medellín, Colombia'
        ));
    }

    private function handleServiceChoice(Conversation $conversation, string $text, ?string $replyId): array
    {
        $key = $this->matchService($text, $replyId, 'svc_');

        if ($key === null) {
            return $this->fallback($conversation, $text);
        }

        $service = config("mavkora.services.{$key}");
        $conversation->contextPut('quote.service', $key);

        $body = "*{$service['name']}*\n\n{$service['summary']}\n\n";

        foreach ($service['details'] as $detail) {
            $body .= "✅ {$detail}\n";
        }

        $body .= "\n¿Te interesa? Podemos preparar una cotización a tu medida.";

        return $this->respond($conversation, Reply::buttons($body, [
            ['id' => 'menu_quote', 'title' => 'Cotizar esto'],
            ['id' => 'menu_services', 'title' => 'Ver otros'],
            ['id' => 'back_menu', 'title' => 'Menú principal'],
        ]));
    }

    /*
    |--------------------------------------------------------------------------
    | Cotización
    |--------------------------------------------------------------------------
    */

    private function startQuote(Conversation $conversation): array
    {
        // Si venía de mirar un servicio, ese paso ya está resuelto.
        if ($conversation->contextGet('quote.service')) {
            return $this->askQuoteName($conversation);
        }

        $conversation->step = self::STEP_QUOTE_SERVICE;

        return $this->respond($conversation, Reply::list(
            "¡Perfecto! Vamos a preparar tu cotización 📝\n\n¿Sobre qué servicio necesitas la propuesta?",
            'Elegir servicio',
            [['rows' => $this->serviceRows('qsvc_')]],
            'Paso 1 de 4'
        ));
    }

    private function handleQuoteService(Conversation $conversation, string $text, ?string $replyId): array
    {
        $key = $this->matchService($text, $replyId, 'qsvc_');

        if ($key === null) {
            return $this->respond($conversation, Reply::text(
                'No logré identificar el servicio. Toca el botón *Elegir servicio* y selecciónalo de la lista, o escribe *menu* para volver al inicio.'
            ));
        }

        $conversation->contextPut('quote.service', $key);

        return $this->askQuoteName($conversation);
    }

    private function askQuoteName(Conversation $conversation): array
    {
        // El nombre del perfil de WhatsApp sirve como valor por defecto, pero se
        // confirma: mucha gente tiene apodos o emojis en el perfil.
        $suggestion = $conversation->profile_name
            ? "\n\nSi prefieres, responde *sí* para usar «{$conversation->profile_name}»."
            : '';

        $conversation->step = self::STEP_QUOTE_NAME;

        return $this->respond($conversation, Reply::text(
            "Genial. ¿Cuál es tu *nombre completo*?{$suggestion}\n\n_Paso 2 de 4_"
        ));
    }

    private function handleQuoteName(Conversation $conversation, string $text): array
    {
        $name = $this->confirmsProfileName($text) && $conversation->profile_name
            ? $conversation->profile_name
            : $text;

        if (mb_strlen($name) < 2) {
            return $this->respond($conversation, Reply::text('Necesito tu nombre para continuar. ¿Cómo te llamas?'));
        }

        $conversation->contextPut('quote.name', Str::limit($name, 120, ''));
        $conversation->step = self::STEP_QUOTE_EMAIL;

        $thanks = ($first = $this->firstName($name)) ? "Gracias, {$first} 🙌" : 'Gracias 🙌';

        return $this->respond($conversation, Reply::buttons(
            "{$thanks}\n\n¿A qué *correo electrónico* te enviamos la propuesta?",
            [['id' => 'skip_email', 'title' => 'Prefiero no darlo']],
            'Paso 3 de 4'
        ));
    }

    private function handleQuoteEmail(Conversation $conversation, string $text, ?string $replyId): array
    {
        if ($replyId === 'skip_email' || $this->saysSkip($text)) {
            $conversation->contextPut('quote.email', null);

            return $this->askQuoteDetails($conversation);
        }

        $valid = Validator::make(['email' => $text], ['email' => 'required|email:rfc'])->passes();

        if (! $valid) {
            return $this->respond($conversation, Reply::buttons(
                "Ese correo no parece válido 🤔\n\n¿Puedes escribirlo de nuevo? Por ejemplo: nombre@empresa.com",
                [['id' => 'skip_email', 'title' => 'Prefiero no darlo']]
            ));
        }

        $conversation->contextPut('quote.email', mb_strtolower($text));

        return $this->askQuoteDetails($conversation);
    }

    private function askQuoteDetails(Conversation $conversation): array
    {
        $conversation->step = self::STEP_QUOTE_DETAILS;

        return $this->respond($conversation, Reply::text(
            "Por último, cuéntame *brevemente qué necesita tu empresa* 💬\n\n"
            ."Mientras más contexto nos des, más precisa será la cotización.\n\n"
            .'_Paso 4 de 4_'
        ));
    }

    private function handleQuoteDetails(Conversation $conversation, string $text): array
    {
        if (mb_strlen($text) < 5) {
            return $this->respond($conversation, Reply::text(
                'Cuéntame un poco más, por favor. ¿Qué problema quieres resolver o qué te gustaría construir?'
            ));
        }

        $lead = $this->createLead($conversation, $text);

        $conversation->lead_id = $lead->id;
        $conversation->contextForget('quote');
        $conversation->step = self::STEP_MENU;

        $body = "¡Listo, {$this->firstName($lead->name)}! ✅\n\n"
            ."Registré tu solicitud de *{$lead->serviceName()}*. "
            ."Un asesor de Mavkora te contactará muy pronto para darte una propuesta a tu medida.\n\n"
            .'¿Quieres agendar de una vez la reunión inicial?';

        return $this->respond($conversation, Reply::buttons($body, [
            ['id' => 'menu_appointment', 'title' => 'Agendar reunión'],
            ['id' => 'back_menu', 'title' => 'Ahora no'],
        ]), notify: [
            'type' => 'lead',
            'lead_id' => $lead->id,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'service' => $lead->serviceName(),
        ]);
    }

    private function createLead(Conversation $conversation, string $details): Lead
    {
        return Lead::create([
            'name' => $conversation->contextGet('quote.name', $conversation->profile_name ?? 'Contacto de WhatsApp'),
            'email' => $conversation->contextGet('quote.email'),
            'phone' => $conversation->wa_id,
            'service' => $conversation->contextGet('quote.service'),
            'message' => $details,
            'source' => 'whatsapp',
            'status' => 'new',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Agendamiento
    |--------------------------------------------------------------------------
    */

    private function startAppointment(Conversation $conversation): array
    {
        // Para agendar basta con un nombre; no hace falta el formulario completo.
        $name = $conversation->lead?->name ?? $conversation->contextGet('quote.name');

        if (blank($name)) {
            $conversation->step = self::STEP_APPT_NAME;

            $suggestion = $conversation->profile_name
                ? " Responde *sí* para usar «{$conversation->profile_name}»."
                : '';

            return $this->respond($conversation, Reply::text(
                "Con gusto agendamos la reunión inicial 📅\n\n¿A nombre de quién la registro?{$suggestion}"
            ));
        }

        return $this->offerSlots($conversation);
    }

    private function handleAppointmentName(Conversation $conversation, string $text): array
    {
        $name = $this->confirmsProfileName($text) && $conversation->profile_name
            ? $conversation->profile_name
            : $text;

        if (mb_strlen($name) < 2) {
            return $this->respond($conversation, Reply::text('Necesito un nombre para la reunión. ¿Cómo te llamas?'));
        }

        $conversation->contextPut('quote.name', Str::limit($name, 120, ''));

        return $this->offerSlots($conversation);
    }

    private function offerSlots(Conversation $conversation, ?string $notice = null): array
    {
        $slots = $this->slots->available();

        if ($slots === []) {
            $conversation->step = self::STEP_MENU;

            return $this->respond($conversation, Reply::buttons(
                "Ahora mismo no tengo horarios libres en los próximos días 😕\n\n"
                .'Puedo pasarte con un asesor para coordinar una fecha manualmente.',
                [
                    ['id' => 'menu_human', 'title' => 'Hablar con asesor'],
                    ['id' => 'back_menu', 'title' => 'Menú principal'],
                ]
            ));
        }

        $conversation->step = self::STEP_APPT_SLOT;

        $rows = array_map(fn (array $slot) => [
            'id' => $slot['id'],
            'title' => $slot['title'],
            'description' => $slot['description'],
        ], $slots);

        $body = $notice
            ? $notice."\n\nElige el que mejor te quede."
            : "Estos son los horarios disponibles 📅\n\nElige el que mejor te quede.";

        return $this->respond($conversation, Reply::list(
            $body,
            'Ver horarios',
            [['rows' => $rows]],
            'Hora de Colombia (GMT-5)'
        ));
    }

    private function handleSlotChoice(Conversation $conversation, string $text, ?string $replyId): array
    {
        $slotId = $replyId ?? $text;
        $at = $this->slots->resolve($slotId);

        if ($at === null) {
            // El id puede venir mal, o alguien pudo tomar la franja mientras el
            // cliente decidía. En ambos casos se vuelven a ofrecer horarios.
            return $this->offerSlots(
                $conversation,
                'Esa franja ya no está disponible 😕 Estos son los horarios actualizados:'
            );
        }

        $lead = $conversation->lead ?? $this->ensureLeadForAppointment($conversation);

        $appointment = Appointment::create([
            'lead_id' => $lead->id,
            'conversation_id' => $conversation->id,
            'scheduled_at' => $at->utc(),
            'duration_minutes' => (int) config('mavkora.schedule.slot_minutes', 30),
            'status' => 'pending',
            'notes' => 'Agendada por el chatbot de WhatsApp.',
        ]);

        $conversation->lead_id = $lead->id;
        $conversation->step = self::STEP_MENU;

        $body = "¡Reunión agendada! ✅\n\n"
            ."📅 *".SlotFinder::longLabel($at)."*\n"
            ."⏱️ Duración: {$appointment->duration_minutes} minutos\n\n"
            ."Te enviaremos la confirmación y el enlace de la reunión al correo o por aquí mismo.\n\n"
            .'¿Necesitas algo más?';

        return $this->respond($conversation, Reply::buttons($body, [
            ['id' => 'back_menu', 'title' => 'Menú principal'],
            ['id' => 'menu_human', 'title' => 'Hablar con asesor'],
        ]), notify: [
            'type' => 'appointment',
            'appointment_id' => $appointment->id,
            'lead_id' => $lead->id,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'scheduled_at' => $at->toIso8601String(),
            'scheduled_label' => SlotFinder::longLabel($at),
        ]);
    }

    /**
     * Alguien puede agendar sin haber pasado por la cotización. En ese caso se
     * crea igual un lead, porque una reunión agendada es un lead por definición.
     */
    private function ensureLeadForAppointment(Conversation $conversation): Lead
    {
        return Lead::create([
            'name' => $conversation->contextGet('quote.name', $conversation->profile_name ?? 'Contacto de WhatsApp'),
            'email' => $conversation->contextGet('quote.email'),
            'phone' => $conversation->wa_id,
            'service' => $conversation->contextGet('quote.service'),
            'message' => 'Solicitó agendar una reunión inicial desde WhatsApp.',
            'source' => 'whatsapp',
            'status' => 'new',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Contacto y escalado a humano
    |--------------------------------------------------------------------------
    */

    private function showContact(Conversation $conversation): array
    {
        $company = config('mavkora.company');
        $conversation->step = self::STEP_MENU;

        $body = "*Datos de contacto de Mavkora* 📇\n\n"
            ."📧 {$company['email']}\n"
            ."📞 {$company['phone']}\n"
            ."📍 {$company['location']}\n\n"
            .'🕘 Horario comercial: '.$this->scheduleLine()."\n"
            .'🛠️ Soporte técnico: 24/7';

        return $this->respond($conversation, Reply::buttons($body, [
            ['id' => 'menu_human', 'title' => 'Hablar con asesor'],
            ['id' => 'back_menu', 'title' => 'Menú principal'],
        ]));
    }

    private function startHandoff(Conversation $conversation): array
    {
        $conversation->step = self::STEP_HANDOFF;
        $conversation->status = Conversation::STATUS_HUMAN;
        $conversation->handoff_at = now();

        $body = "Claro, te conecto con una persona del equipo 🤝\n\n"
            .'Un asesor de Mavkora continuará esta conversación en el horario comercial ('
            .$this->scheduleLine().").\n\n"
            .'Si mientras tanto quieres volver al asistente automático, escribe *menu*.';

        return $this->respond($conversation, Reply::text($body), notify: [
            'type' => 'handoff',
            'conversation_id' => $conversation->id,
            'phone' => $conversation->wa_id,
            'name' => $conversation->profile_name ?? $conversation->lead?->name,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Texto libre: rama de IA
    |--------------------------------------------------------------------------
    */

    /**
     * Nada encajó en el paso actual. Se intenta en tres niveles, del más barato
     * al más caro.
     *
     * @return array<string, mixed>
     */
    private function fallback(Conversation $conversation, string $text): array
    {
        // 1. Base de conocimiento local: gratis, y cubre la mayoría del texto
        //    libre que llega. En precios además conviene una respuesta fija:
        //    así no hay manera de que el bot improvise una cifra.
        if ($answer = $this->faq->answer($text)) {
            $conversation->step = self::STEP_MENU;

            return $this->respond($conversation, $answer);
        }

        // 2. La IA, solo si está activa y hay algo que interpretar.
        if ($text !== '' && $this->aiEnabled()) {
            return $this->respondWithAi($conversation, $text);
        }

        // 3. Admitir que no se entendió y ofrecer salidas concretas.
        //    Repetir el menú entero después de una pregunta frustra al cliente.
        $conversation->step = self::STEP_MENU;

        return $this->respond($conversation, Reply::buttons(
            "No estoy seguro de haber entendido eso 🤔\n\n"
            .'Puedo ayudarte con lo siguiente, o pasarte con una persona del equipo:',
            [
                ['id' => 'menu_services', 'title' => 'Ver servicios'],
                ['id' => 'menu_quote', 'title' => 'Solicitar cotización'],
                ['id' => 'menu_human', 'title' => 'Hablar con asesor'],
            ]
        ));
    }

    /**
     * Devuelve el contexto para que n8n llame a Claude. Laravel no habla con el
     * modelo: mantiene la clave de API fuera de la aplicación y deja el gasto
     * visible en un solo lugar.
     *
     * @return array<string, mixed>
     */
    private function respondWithAi(Conversation $conversation, string $text): array
    {
        $history = $conversation->messages()
            ->latest('id')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn (Message $m) => [
                'role' => $m->isIncoming() ? 'user' : 'assistant',
                'content' => (string) $m->body,
            ])
            ->filter(fn (array $m) => $m['content'] !== '')
            ->values()
            ->all();

        return $this->respond($conversation, Reply::silence(), ai: [
            'system' => $this->knowledge->systemPrompt(),
            'history' => $history,
            'message' => $text,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Persistencia y utilidades
    |--------------------------------------------------------------------------
    */

    private function resolveConversation(array $input): Conversation
    {
        $conversation = Conversation::firstOrNew(['wa_id' => $input['wa_id']]);

        if (! $conversation->exists) {
            $conversation->step = self::STEP_START;
            $conversation->status = Conversation::STATUS_BOT;
        }

        // El nombre del perfil puede cambiar entre mensajes; siempre gana el último.
        if (filled($input['profile_name'] ?? null)) {
            $conversation->profile_name = $input['profile_name'];
        }

        try {
            $conversation->save();
        } catch (QueryException $e) {
            // Si alguien manda dos mensajes seguidos siendo su primer contacto,
            // ambos webhooks intentan crear la conversación: uno gana el insert
            // y el otro choca con el índice único de wa_id. Basta releer el que ganó.
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            $conversation = Conversation::where('wa_id', $input['wa_id'])->firstOrFail();
        }

        return $conversation;
    }

    /**
     * Guarda el mensaje entrante. Devuelve false si ya estaba registrado, que es
     * como se detectan los reintentos de Meta.
     */
    private function recordIncoming(Conversation $conversation, array $input): bool
    {
        try {
            $conversation->messages()->create([
                'wa_message_id' => $input['wa_message_id'] ?? null,
                'direction' => Message::IN,
                'type' => $input['type'] ?? 'text',
                'body' => $input['text'] ?? null,
                'payload' => ['reply_id' => $input['reply_id'] ?? null],
            ]);
        } catch (QueryException $e) {
            // 23000 = violación de restricción de integridad (el índice único
            // sobre wa_message_id). Cualquier otro error sí es un problema real.
            if ($e->getCode() === '23000') {
                return false;
            }

            throw $e;
        }

        $conversation->last_message_at = now();
        $conversation->save();

        return true;
    }

    /**
     * @param  array<string, mixed>  $reply
     * @param  array<string, mixed>|null  $ai
     * @param  array<string, mixed>|null  $notify
     * @return array<string, mixed>
     */
    private function respond(
        Conversation $conversation,
        array $reply,
        ?array $ai = null,
        ?array $notify = null,
        bool $duplicate = false,
    ): array {
        $conversation->save();

        return [
            'conversation_id' => $conversation->id,
            'step' => $conversation->step,
            'status' => $conversation->status,
            'duplicate' => $duplicate,
            'reply' => $reply,
            'needs_ai' => $ai !== null,
            'ai' => $ai,
            'notify' => $notify,
        ];
    }

    /**
     * @return list<array{id: string, title: string, description: string}>
     */
    private function serviceRows(string $prefix): array
    {
        $rows = [];

        foreach (config('mavkora.services') as $key => $service) {
            $rows[] = [
                'id' => $prefix.$key,
                'title' => $service['label'],
                'description' => $service['summary'],
            ];
        }

        return $rows;
    }

    /**
     * Resuelve el servicio elegido, ya sea por id de fila o por lo que el
     * cliente escribió a mano.
     */
    private function matchService(string $text, ?string $replyId, string $prefix): ?string
    {
        if ($replyId !== null && str_starts_with($replyId, $prefix)) {
            $key = substr($replyId, strlen($prefix));

            return config("mavkora.services.{$key}") ? $key : null;
        }

        $needle = $this->normalize($text);

        if ($needle === '') {
            return null;
        }

        foreach (config('mavkora.services') as $key => $service) {
            if (str_contains($this->normalize($service['label']), $needle)
                || str_contains($this->normalize($service['name']), $needle)) {
                return $key;
            }
        }

        return null;
    }

    private function matchMenuByText(string $text): ?string
    {
        $needle = $this->normalize($text);

        return match (true) {
            $needle === '' => null,
            (bool) preg_match('/servicio|catalogo/', $needle) => 'menu_services',
            // 'precio' y 'cuanto cuesta' salen a propósito de aquí: los atiende
            // FaqMatcher, que explica cómo cotizamos antes de meter a nadie a un
            // formulario. Preguntar el precio no es lo mismo que pedir cotización.
            (bool) preg_match('/cotiz|presupuesto|propuesta/', $needle) => 'menu_quote',
            (bool) preg_match('/agend|reunion|cita|reservar|calendario/', $needle) => 'menu_appointment',
            (bool) preg_match('/contacto|correo|telefono|direccion|ubicacion|donde estan/', $needle) => 'menu_contact',
            default => null,
        };
    }

    private function wantsHuman(string $text, ?string $replyId): bool
    {
        if ($replyId === 'menu_human') {
            return true;
        }

        return (bool) preg_match(
            '/\b(asesor|humano|persona|agente|ejecutivo|vendedor|alguien real|hablar con alguien)\b/',
            $this->normalize($text)
        );
    }

    private function wantsMenu(string $text, ?string $replyId): bool
    {
        if ($replyId === 'back_menu') {
            return true;
        }

        return (bool) preg_match('/^(menu|inicio|volver|atras|cancelar|salir|empezar)$/', $this->normalize($text));
    }

    private function looksLikeGreeting(string $text): bool
    {
        return (bool) preg_match(
            '/^(hola+|buenas|buenos dias|buenas tardes|buenas noches|hey|hi|hello|que tal|saludos|holi|ola)[\s!.,]*$/',
            $this->normalize($text)
        );
    }

    private function confirmsProfileName(string $text): bool
    {
        return (bool) preg_match('/^(si|sí|claro|correcto|ok|dale|exacto|asi es)[\s!.,]*$/', $this->normalize($text));
    }

    private function saysSkip(string $text): bool
    {
        return (bool) preg_match('/^(no|omitir|saltar|prefiero no|sin correo|paso)[\s!.,]*$/', $this->normalize($text));
    }

    /**
     * Minúsculas y sin tildes, para que «cotización» y «cotizacion» sean lo mismo.
     */
    private function normalize(string $text): string
    {
        return trim(Str::lower(Str::ascii(trim($text))));
    }

    private function firstName(?string $name): ?string
    {
        if (blank($name)) {
            return null;
        }

        return Str::limit(trim(explode(' ', trim($name))[0]), 30, '');
    }

    private function scheduleLine(): string
    {
        return sprintf(
            'lunes a viernes de %s a %s',
            config('mavkora.schedule.start', '09:00'),
            config('mavkora.schedule.end', '17:00')
        );
    }

    private function aiEnabled(): bool
    {
        return (bool) config('mavkora.bot.ai_enabled', true);
    }
}
