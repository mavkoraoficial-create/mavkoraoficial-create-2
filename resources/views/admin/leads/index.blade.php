@extends('adminlte::page')

@section('title', 'Leads - Mavkora')

@section('content_header')
    <h1>Leads <small class="text-muted">{{ $total }} en total</small></h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.leads.index') }}" class="form-inline">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2"
                       placeholder="Buscar por nombre, correo o teléfono">

                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\Lead::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>
                            {{ $label }} ({{ $counts[$key] ?? 0 }})
                        </option>
                    @endforeach
                </select>

                <select name="source" class="form-control form-control-sm mr-2">
                    <option value="">Todos los orígenes</option>
                    @foreach (\App\Models\Lead::SOURCES as $key => $label)
                        <option value="{{ $key }}" @selected(request('source') === $key)>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-sm btn-primary mr-2">Filtrar</button>

                @if (request()->hasAny(['q', 'status', 'source']))
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Origen</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Recibido</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        <tr>
                            <td>
                                <i class="{{ $lead->source === 'whatsapp' ? 'fab fa-whatsapp text-success' : 'fas fa-globe text-info' }}"
                                   title="{{ $lead->sourceLabel() }}"></i>
                            </td>
                            <td><strong>{{ $lead->name }}</strong></td>
                            <td>
                                @if ($lead->email)
                                    <div><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></div>
                                @endif
                                @if ($lead->phone)
                                    <div class="small text-muted">
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone) }}" target="_blank" rel="noopener">
                                            {{ $lead->phone }}
                                        </a>
                                    </div>
                                @endif
                                @if (! $lead->email && ! $lead->phone)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $lead->serviceName() }}</td>
                            <td>
                                <span class="badge badge-{{ $lead->status === 'new' ? 'warning' : ($lead->status === 'won' ? 'success' : ($lead->status === 'lost' ? 'danger' : 'secondary')) }}">
                                    {{ $lead->statusLabel() }}
                                </span>
                            </td>
                            <td title="{{ $lead->created_at }}">{{ $lead->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-xs btn-outline-primary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay leads que coincidan con el filtro.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leads->hasPages())
            <div class="card-footer">
                {{ $leads->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@stop
