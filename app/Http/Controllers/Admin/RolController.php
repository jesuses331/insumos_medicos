<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Listado de roles.
     */
    public function inicio()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('admin.roles.inicio', compact('roles'));
    }

    /**
     * Formulario de creación.
     */
    public function crear()
    {
        $permisos = Permission::all();
        return view('admin.roles.crear', compact('permisos'));
    }

    /**
     * Guardar nuevo rol.
     */
    public function guardar(Request $solicitud)
    {
        $solicitud->validate([
            'nombre' => 'required|string|unique:roles,name',
            'permisos' => 'nullable|array'
        ]);

        $rol = Role::create(['name' => $solicitud->nombre]);

        if ($solicitud->permisos) {
            $rol->syncPermissions($solicitud->permisos);
        }

        return redirect()->route('admin.roles.inicio')->with('exito', 'Rol creado exitosamente.');
    }

    /**
     * Formulario de edición.
     */
    public function editar($id)
    {
        $rol = Role::findOrFail($id);
        $permisos = Permission::all();
        return view('admin.roles.editar', compact('rol', 'permisos'));
    }

    /**
     * Actualizar rol.
     */
    public function actualizar(Request $solicitud, $id)
    {
        $rol = Role::findOrFail($id);

        $solicitud->validate([
            'nombre' => 'required|string|unique:roles,name,' . $rol->id,
            'permisos' => 'nullable|array'
        ]);

        $rol->update(['name' => $solicitud->nombre]);
        $rol->syncPermissions($solicitud->permisos ?? []);

        return redirect()->route('admin.roles.inicio')->with('exito', 'Rol actualizado exitosamente.');
    }

    /**
     * Eliminar rol.
     */
    public function eliminar($id)
    {
        $rol = Role::findOrFail($id);

        if ($rol->name === 'super-admin') {
            return back()->withErrors('El rol de Super Administrador no puede ser eliminado.');
        }

        $rol->delete();
        return redirect()->route('admin.roles.inicio')->with('exito', 'Rol eliminado exitosamente.');
    }
}
