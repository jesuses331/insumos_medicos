<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function inicio()
    {
        $sucursales = \App\Models\Sucursal::orderBy('nombre')->paginate(10);
        return view('admin.sucursales.inicio', compact('sucursales'));
    }

    public function crear()
    {
        return view('admin.sucursales.crear');
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        \App\Models\Sucursal::create($request->all());

        return redirect()->route('admin.sucursales.inicio')->with('exito', 'Sucursal creada correctamente.');
    }

    public function editar($id)
    {
        $sucursal = \App\Models\Sucursal::findOrFail($id);
        return view('admin.sucursales.editar', compact('sucursal'));
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        $sucursal = \App\Models\Sucursal::findOrFail($id);
        $sucursal->update($request->all());

        return redirect()->route('admin.sucursales.inicio')->with('exito', 'Sucursal actualizada correctamente.');
    }

    public function eliminar($id)
    {
        $sucursal = \App\Models\Sucursal::findOrFail($id);
        $sucursal->delete();

        return redirect()->route('admin.sucursales.inicio')->with('exito', 'Sucursal eliminada correctamente.');
    }
}
