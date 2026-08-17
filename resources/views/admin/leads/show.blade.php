@extends('adminlte::page')

@section('title', $lead->name.' - Leads - Mavkora')

@section('content_header')
    <h1>
        {{ $lead->name }}
        <span class="badge badge-{{ $lead->source === 'whatsapp' ? 'success' : 'info' }}">{{ $lead->sourceLabel() }}</span>
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
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Solicitud</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Servicio de interés</dt>
                        <dd class="col-sm-8">{{ $lead->serviceName() }}</dd>

                        <dt class="col-sm-4">Correo</dt>
                        <dd class="col-sm-8">
                            @if ($lead->email)
                                <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                            @else
                                <span class="text-muted">No proporcionado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Teléfono</dt>
                        <dd class="col-sm-8">
                            @if ($lead->phone)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone) }}" target="_blank" rel="noopener">
                                    {{ $lead->phone }} <i class="fab fa-whatsapp text-success"></i>
                                </a>
                            @else
                                <span class="text-muted">No proporcionado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Recibido</dt>
                        <dd class="col-sm-8">{{ $lead->created_at->format('d/m/Y g:i a') }} ({{ $lead->created_at->diffForHumans() }})</dd>
                    </dl>

                    <hr>

                    <h6 class="text-muted text-uppercase small">Qué necesita</h6>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $lead->message ?: 'Sin detalles.' }}</p>
                </div>
            </div>

            @if ($lead->appointments->isNotEmpty())
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-check mr-1"></i> Reuniones</h3>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($lead->appointments as $appointment)
                            <div class="px-3 py-2 border-bottom">
                                <i class="far fa-clock text-muted"></i>
                                {{ $appointment->scheduled_at->timezone(config('mavkora.schedule.timezone'))->format('d/m/Y g:i a') }}
                                <span class="badge badge-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning') }} float-right">
                                    {{ $appointment->statusLabel() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-5">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Seguimiento</h3>
                </div>
                <form method="POST" action="{{ route('admin.leads.update', $lead) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (\App\Models\Lead::STATUSES as $key => $label)
                                    <option value="{{ $key }}" @selected(old('status', $lead->status) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="notes">Notas internas</label>
                            <textarea name="notes" id="notes" rows="6" class="form-control"
                                      placeholder="Qué se habló, próximos pasos, presupuesto estimado...">{{ old('notes', $lead->notes) }}</textarea>
                            @error('notes')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-secondary">Volver</a>
                    </div>
                </form>
            </div>

            @if ($lead->conversation)
                <div class="card card-outline card-success">
                    <div class="card-body">
                        <p class="mb-2"><i class="fab fa-whatsapp text-success"></i> Este lead llegó por WhatsApp.</p>
                        <a href="{{ route('admin.conversations.show', $lead->conversation) }}" class="btn btn-sm btn-outline-success">
                            Ver la conversación completa
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
