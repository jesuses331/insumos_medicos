<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario no está autenticado, dejar pasar (el middleware 'auth' se encargará si es necesario)
        if (!auth()->check()) {
            return $next($request);
        }

        // Si es una ruta de selección de sucursal, dejar pasar
        if ($request->routeIs('admin.sucursales.seleccionar') || $request->routeIs('admin.sucursales.establecer')) {
            return $next($request);
        }

        // Validar que la sucursal en sesión realmente exista en la DB (por si se refrescó)
        if (session()->has('active_sucursal_id')) {
            if (!\App\Models\Sucursal::where('id', session('active_sucursal_id'))->exists()) {
                session()->forget(['active_sucursal_id', 'active_sucursal_nombre']);
            }
        }

        // Si el usuario no tiene una sucursal activa en la sesión
        if (!session()->has('active_sucursal_id')) {

            $user = auth()->user();
            $sucursales = $user->sucursales;

            // Si el usuario solo tiene una sucursal, la asignamos automáticamente
            if ($sucursales->count() === 1) {
                session(['active_sucursal_id' => $sucursales->first()->id]);
                session(['active_sucursal_nombre' => $sucursales->first()->nombre]);
                return $next($request);
            }

            // Si tiene más de una o ninguna (y necesita una), redirigir a selección
            if ($sucursales->count() > 1) {
                return redirect()->route('admin.sucursales.seleccionar');
            }

            // Si es SuperAdmin y no tiene sucursales asignadas, tal vez permitir pasar o mostrar advertencia
            if ($user->hasRole('super-admin') && $sucursales->count() === 0) {
                return $next($request);
            }
        }

        return $next($request);
    }
}
