@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <h1>Configuración de Perfil</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Actualizar Datos</h3>
                </div>
                
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        
                        <hr>
                        <p class="text-muted">Deja la contraseña en blanco si no deseas cambiarla.</p>

                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 8 caracteres">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">
                    Información de la Cuenta
                </div>
                <div class="card-body pt-0">
                    <div class="row mt-3">
                        <div class="col-7">
                            <h2 class="lead"><b>{{ $user->name }}</b></h2>
                            <p class="text-muted text-sm"><b>Rol: </b> Administrador </p>
                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                <li class="small mb-2"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> Correo: {{ $user->email }}</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-calendar"></i></span> Registrado el: {{ $user->created_at->format('d/m/Y') }}</li>
                            </ul>
                        </div>
                        <div class="col-5 text-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0056b3&color=fff&size=128" alt="user-avatar" class="img-circle img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
