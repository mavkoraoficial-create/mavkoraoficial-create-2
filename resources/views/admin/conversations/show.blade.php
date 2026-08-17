@extends('adminlte::page')

@section('title', 'Conversación con '.($conversation->profile_name ?: $conversation->wa_id).' - Mavkora')

@section('content_header')
    <h1>
        <i class="fab fa-whatsapp text-success"></i>
        {{ $conversation->profile_name ?: 'Sin nombre' }}
        <small class="text-muted">+{{ $conversation->wa_id }}</small>
    </h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card direct-chat direct-chat-success">
                <div class="card-header">
                    <h3 class="card-title">Historial · {{ $messages->total() }} mensajes</h3>
                </div>

                <div class="card-body" style="max-height: 65vh; overflow-y: auto;">
                    @forelse ($messages as $message)
                        @php
                            $incoming = $message->isIncoming();
                        @endphp
                        <div class="direct-chat-msg {{ $incoming ? '' : 'right' }}">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name {{ $incoming ? 'float-left' : 'float-right' }}">
                                    @if ($incoming)
                                        {{ $conversation->profile_name ?: 'Cliente' }}
                                    @else
                                        @if ($message->generated_by === 'ai')
                                            <i class="fas fa-robot" title="Respuesta generada con IA"></i> Bot (IA)
                                        @elseif ($message->generated_by === 'human')
                                            <i class="fas fa-user" title="Enviado por un asesor"></i> Asesor
                                        @else
                                            <i class="fas fa-list" title="Respuesta del menú"></i> Bot
                                        @endif
                                    @endif
                                </span>
                                <span class="direct-chat-timestamp {{ $incoming ? 'float-right' : 'float-left' }}">
                                    {{ $message->created_at->timezone(config('mavkora.schedule.timezone'))->format('d/m/Y g:i a') }}
                                </span>
                            </div>
                            <div class="direct-chat-text" style="white-space: pre-wrap;">{{ $message->body ?: '['.$message->type.']' }}</div>
                            @if ($replyId = data_get($message->payload, 'reply_id'))
                                <div class="{{ $incoming ? 'ml-5' : 'mr-5 text-right' }}">
                                    <span class="badge badge-light">tocó: {{ $replyId }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Sin mensajes registrados.</p>
                    @endforelse
                </div>

                @if ($messages->hasPages())
                    <div class="card-footer">
                        {{ $messages->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Control del bot</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        Estado actual:
                        @if ($conversation->status === 'human')
                            <span class="badge badge-warning">Esperando a un asesor</span>
                        @elseif ($conversation->status === 'closed')
                            <span class="badge badge-secondary">Cerrada</span>
                        @else
                            <span class="badge badge-success">Atendida por el bot</span>
                        @endif
                    </p>

                    @if ($conversation->status === 'human')
                        <p class="text-muted small">
                            El bot está en silencio en esta conversación. Responde desde WhatsApp Business
                            y, cuando termines, devuélvela al bot.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('admin.conversations.update', $conversation) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="bot" @selected($conversation->status === 'bot')>Que responda el bot</option>
                                <option value="human" @selected($conversation->status === 'human')>La atiendo yo</option>
                                <option value="closed" @selected($conversation->status === 'closed')>Marcar como cerrada</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Aplicar</button>
                    </form>

                    <hr>

                    <a href="https://wa.me/{{ $conversation->wa_id }}" target="_blank" rel="noopener"
                       class="btn btn-success btn-block">
                        <i class="fab fa-whatsapp"></i> Escribirle por WhatsApp
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles</h3>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Paso del flujo</dt>
                        <dd><code>{{ $conversation->step }}</code></dd>

                        <dt>Último mensaje</dt>
                        <dd>{{ $conversation->last_message_at?->diffForHumans() ?? '—' }}</dd>

                        <dt>Lead asociado</dt>
                        <dd>
                            @if ($conversation->lead)
                                <a href="{{ route('admin.leads.show', $conversation->lead) }}">
                                    {{ $conversation->lead->name }}
                                </a>
                            @else
                                <span class="text-muted">Todavía no dejó sus datos</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.conversations.index') }}" class="btn btn-outline-secondary">Volver a la lista</a>
@stop
