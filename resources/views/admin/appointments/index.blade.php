@extends('adminlte::page')

@section('title', 'Citas agendadas - Mavkora')

@section('content_header')
    <h1><i class="fas fa-calendar-check"></i> Citas agendadas</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.appointments.index') }}"
                   class="btn btn-{{ $showPast ? 'outline-primary' : 'primary' }}">Próximas</a>
                <a href="{{ route('admin.appointments.index', ['past' => 1]) }}"
                   class="btn btn-{{ $showPast ? 'primary' : 'outline-primary' }}">Historial</a>
            </div>
            <span class="text-muted small ml-3">
                Horarios en {{ config('mavkora.schedule.timezone') }}
            </span>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Contacto</th>
                        <th>Servicio</th>
                        <th>Duración</th>
                        <th>Estado</th>
                        <th>Cambiar a</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        @php
                            $localTime = $appointment->scheduled_at->timezone(config('mavkora.schedule.timezone'));
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $localTime->format('d/m/Y') }}</strong>
                                {{ $localTime->format('g:i a') }}
                                <div class="small text-muted">{{ $appointment->scheduled_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if ($appointment->lead)
                                    <a href="{{ route('admin.leads.show', $appointment->lead) }}">
                                        {{ $appointment->lead->name }}
                                    </a>
                                    <div class="small text-muted">{{ $appointment->lead->phone }}</div>
                                @else
                                    <span class="text-muted">Sin lead asociado</span>
                                @endif
                            </td>
                            <td>{{ $appointment->lead?->serviceName() ?? '—' }}</td>
                            <td>{{ $appointment->duration_minutes }} min</td>
                            <td>
                                <span class="badge badge-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : ($appointment->status === 'done' ? 'secondary' : 'warning')) }}">
                                    {{ $appointment->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="form-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-control form-control-sm mr-1"
                                            onchange="this.form.submit()">
                                        @foreach (\App\Models\Appointment::STATUSES as $key => $label)
                                            <option value="{{ $key }}" @selected($appointment->status === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <noscript><button type="submit" class="btn btn-sm btn-primary">Guardar</button></noscript>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                {{ $showPast ? 'No hay citas en el historial.' : 'No hay reuniones próximas agendadas.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appointments->hasPages())
            <div class="card-footer">
                {{ $appointments->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@stop
