<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SucursalSelectionController extends Controller
{
    public function seleccionar()
    {
        $sucursales = auth()->user()->sucursales()->distinct()->get();

        // Si no tiene sucursales y no es superadmin, error o mensaje
        if ($sucursales->isEmpty() && !auth()->user()->hasRole('super-admin')) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'No tienes sucursales asignadas. contacta al administrador.');
        }

        return view('admin.sucursales.seleccionar', compact('sucursales'));
    }

    public function establecer(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id'
        ]);

        $user = auth()->user();

        // Verificar que el usuario pertenezca a la sucursal (o sea superadmin)
        if (!$user->hasRole('super-admin') && !$user->sucursales->contains($request->sucursal_id)) {
            return back()->with('error', 'No tienes permiso para acceder a esta sucursal.');
        }

        $sucursal = \App\Models\Sucursal::find($request->sucursal_id);

        session(['active_sucursal_id' => $sucursal->id]);
        session(['active_sucursal_nombre' => $sucursal->nombre]);

        return redirect()->route('admin.tablero')->with('success', "Has ingresado a la sucursal: {$sucursal->nombre}");
    }
}
