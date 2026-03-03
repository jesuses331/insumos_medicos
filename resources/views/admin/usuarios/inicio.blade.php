@extends('layouts.plantilla')

@section('encabezado', 'Control de Acceso')

@section('contenido')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 fw-bold text-white mb-1">Usuarios</h1>
                <p class="text-white-50 mb-0">Gestión de credenciales y roles del personal</p>
            </div>
            <a href="{{ route('admin.usuarios.crear') }}" class="btn btn-glass px-4 py-2">
                <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
            </a>
        </div>

        <div class="glass-card p-0 overflow-hidden border-0 shadow-lg">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small text-uppercase fw-bold" style="background: rgba(255,255,255,0.03);">
                            <th class="px-4 py-3 border-0">Colaborador</th>
                            <th class="px-4 py-3 border-0">Identidad Digital</th>
                            <th class="px-4 py-3 border-0">Roles Asignados</th>
                            <th class="px-4 py-3 border-0 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-white-50">
                        @foreach($usuarios as $usuario)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold text-white"
                                            style="width: 40px; height: 40px; background: var(--primary-gradient); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                                            {{ substr($usuario->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white">{{ $usuario->name }}</div>
                                            <div class="small opacity-50">Usuario Activo</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <i class="fas fa-envelope me-2 text-primary opacity-50"></i>
                                    {{ $usuario->email }}
                                </td>
                                <td class="px-4 py-4">
                                    @foreach($usuario->roles as $rol)
                                        <span class="badge rounded-pill px-3 py-2 fw-bold text-uppercase"
                                            style="font-size: 0.6rem; background: rgba(102, 126, 234, 0.1); color: #667eea; border: 1px solid rgba(102, 126, 234, 0.2);">
                                            {{ $rol->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.usuarios.editar', $usuario->id) }}"
                                            class="btn btn-sm btn-outline-light rounded-circle p-2 opacity-50 hover-opacity-100"
                                            style="width: 34px; height: 34px;">
                                            <i class="fas fa-edit small"></i>
                                        </a>
                                        <form action="{{ route('admin.usuarios.eliminar', $usuario->id) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este usuario?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-circle p-2 opacity-50 hover-opacity-100"
                                                style="width: 34px; height: 34px;">
                                                <i class="fas fa-trash small"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($usuarios->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
@endsection