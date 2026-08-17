@extends('adminlte::page')

@section('title', 'Panel de Control - Mavkora')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $leadsTotal }}</h3>
                    <p>Leads totales</p>
                </div>
                <div class="icon"><i class="fas fa-user-tie"></i></div>
                <a href="{{ route('admin.leads.index') }}" class="small-box-footer">
                    Ver todos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $leadsNew }}</h3>
                    <p>Sin contactar</p>
                </div>
                <div class="icon"><i class="fas fa-bell"></i></div>
                <a href="{{ route('admin.leads.index', ['status' => 'new']) }}" class="small-box-footer">
                    Atender ahora <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $appointmentsUpcoming }}</h3>
                    <p>Reuniones próximas</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <a href="{{ route('admin.appointments.index') }}" class="small-box-footer">
                    Ver agenda <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box {{ $conversationsWaiting > 0 ? 'bg-danger' : 'bg-secondary' }}">
                <div class="inner">
                    <h3>{{ $conversationsWaiting }}</h3>
                    <p>Esperando a un asesor</p>
                </div>
                <div class="icon"><i class="fab fa-whatsapp"></i></div>
                <a href="{{ route('admin.conversations.index', ['status' => 'human']) }}" class="small-box-footer">
                    Responder <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-inbox mr-1"></i> Últimos leads</h3>
                    <div class="card-tools">
                        <span class="badge badge-info">{{ $leadsThisMonth }} este mes</span>
                        <span class="badge badge-success">{{ $leadsFromBot }} desde WhatsApp</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse ($recentLeads as $lead)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="d-flex align-items-center px-3 py-2 border-bottom text-dark text-decoration-none">
                            <i class="{{ $lead->source === 'whatsapp' ? 'fab fa-whatsapp text-success' : 'fas fa-globe text-info' }} fa-fw mr-3"></i>
                            <div class="flex-grow-1">
                                <strong>{{ $lead->name }}</strong>
                                <div class="text-muted small">{{ $lead->serviceName() }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $lead->status === 'new' ? 'warning' : 'secondary' }}">
                                    {{ $lead->statusLabel() }}
                                </span>
                                <div class="text-muted small">{{ $lead->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Todavía no hay leads. Cuando alguien llene el formulario de la web o escriba
                            por WhatsApp, aparecerá aquí.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> Próximas reuniones</h3>
                </div>
                <div class="card-body p-0">
                    @forelse ($nextAppointments as $appointment)
                        <div class="px-3 py-2 border-bottom">
                            <strong>{{ $appointment->lead?->name ?? 'Sin lead asociado' }}</strong>
                            <span class="badge badge-{{ $appointment->status === 'confirmed' ? 'success' : 'warning' }} float-right">
                                {{ $appointment->statusLabel() }}
                            </span>
                            <div class="text-muted small">
                                <i class="far fa-clock"></i>
                                {{ $appointment->scheduled_at->timezone(config('mavkora.schedule.timezone'))->format('d/m/Y g:i a') }}
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="far fa-calendar fa-2x mb-2 d-block"></i>
                            No hay reuniones agendadas.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fab fa-whatsapp mr-1"></i> Chatbot</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>{{ $conversationsTotal }}</strong> conversaciones registradas.
                    </p>
                    <p class="text-muted small mb-3">
                        Respuestas con IA: {{ config('mavkora.bot.ai_enabled') ? 'activadas' : 'desactivadas' }}.
                    </p>
                    <a href="{{ route('admin.conversations.index') }}" class="btn btn-sm btn-outline-success">
                        Ver conversaciones
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
