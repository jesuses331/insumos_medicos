@extends('layouts.plantilla')

@section('titulo', 'Clientes')
@section('encabezado', 'Gestión de Clientes')

@section('contenido')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-white">Clientes</h1>
            <a href="{{ route('admin.clientes.crear') }}" class="btn btn-glass">Nuevo Cliente</a>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table table-dark mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->name }}</td>
                                <td>{{ $cliente->phone }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.clientes.editar', $cliente->id) }}"
                                        class="btn btn-sm btn-outline-light">Editar</a>
                                    <form action="{{ route('admin.clientes.eliminar', $cliente->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Eliminar cliente?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($clientes->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>
@endsection