@extends('adminlte::page')

@section('title', 'Conversaciones de WhatsApp - Mavkora')

@section('content_header')
    <h1><i class="fab fa-whatsapp text-success"></i> Conversaciones de WhatsApp</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.conversations.index') }}" class="form-inline">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2"
                       placeholder="Buscar por número o nombre">

                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">Todos</option>
                    <option value="bot" @selected(request('status') === 'bot')>Atendidas por el bot</option>
                    <option value="human" @selected(request('status') === 'human')>Esperando a un asesor</option>
                    <option value="closed" @selected(request('status') === 'closed')>Cerradas</option>
                </select>

                <button type="submit" class="btn btn-sm btn-primary mr-2">Filtrar</button>

                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.conversations.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Contacto</th>
                        <th>Número</th>
                        <th>Estado</th>
                        <th>Paso</th>
                        <th class="text-center">Mensajes</th>
                        <th>Último mensaje</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($conversations as $conversation)
                        <tr class="{{ $conversation->status === 'human' ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $conversation->profile_name ?: 'Sin nombre' }}</strong>
                                @if ($conversation->lead)
                                    <a href="{{ route('admin.leads.show', $conversation->lead) }}" class="badge badge-info">lead</a>
                                @endif
                            </td>
                            <td>
                                <a href="https://wa.me/{{ $conversation->wa_id }}" target="_blank" rel="noopener">
                                    +{{ $conversation->wa_id }}
                                </a>
                            </td>
                            <td>
                                @if ($conversation->status === 'human')
                                    <span class="badge badge-warning">Esperando asesor</span>
                                @elseif ($conversation->status === 'closed')
                                    <span class="badge badge-secondary">Cerrada</span>
                                @else
                                    <span class="badge badge-success">Bot</span>
                                @endif
                            </td>
                            <td><code class="small">{{ $conversation->step }}</code></td>
                            <td class="text-center">{{ $conversation->messages_count }}</td>
                            <td>
                                {{ $conversation->last_message_at?->diffForHumans() ?? '—' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.conversations.show', $conversation) }}" class="btn btn-xs btn-outline-primary">Abrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Todavía no hay conversaciones. Aparecerán en cuanto alguien le escriba al número de
                                WhatsApp conectado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($conversations->hasPages())
            <div class="card-footer">
                {{ $conversations->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@stop
