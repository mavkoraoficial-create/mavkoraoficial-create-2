<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = Conversation::query()
            ->with('lead')
            ->withCount('messages')
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('q'), function ($query, $term) {
                $like = '%'.$term.'%';

                $query->where(fn ($q) => $q
                    ->where('wa_id', 'like', $like)
                    ->orWhere('profile_name', 'like', $like));
            })
            ->orderByDesc('last_message_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation): View
    {
        $conversation->load('lead');

        $messages = $conversation->messages()
            ->orderBy('id')
            ->paginate(100);

        return view('admin.conversations.show', compact('conversation', 'messages'));
    }

    /**
     * Devuelve la conversación al bot o la marca como atendida por una persona.
     *
     * Poner el estado en 'human' silencia al bot durante las horas configuradas
     * en mavkora.bot.handoff_silence_hours, para que no interrumpa al asesor.
     */
    public function update(Request $request, Conversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([
                Conversation::STATUS_BOT,
                Conversation::STATUS_HUMAN,
                Conversation::STATUS_CLOSED,
            ])],
        ]);

        $conversation->status = $data['status'];

        // Al devolverla al bot se limpia la marca de escalado; si no, el bot
        // seguiría callado hasta que expire el silencio.
        $conversation->handoff_at = $data['status'] === Conversation::STATUS_HUMAN
            ? ($conversation->handoff_at ?? now())
            : null;

        $conversation->save();

        return back()->with('success', 'Conversación actualizada.');
    }
}
