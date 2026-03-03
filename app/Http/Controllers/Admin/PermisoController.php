<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    /**
     * Listado de permisos.
     */
    public function inicio()
    {
        $permisos = Permission::paginate(15);
        return view('admin.permisos.inicio', compact('permisos'));
    }

    /**
     * Guardar nuevo permiso.
     */
    public function guardar(Request $solicitud)
    {
        $solicitud->validate([
            'nombre' => 'required|string|unique:permissions,name'
        ]);

        Permission::create(['name' => $solicitud->nombre]);

        return redirect()->route('admin.permisos.inicio')->with('exito', 'Permiso creado exitosamente.');
    }

    /**
     * Eliminar permiso.
     */
    public function eliminar($id)
    {
        $permiso = Permission::findOrFail($id);
        $permiso->delete();
        return redirect()->route('admin.permisos.inicio')->with('exito', 'Permiso eliminado exitosamente.');
    }
}
