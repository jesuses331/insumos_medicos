<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Listado de usuarios.
     */
    public function inicio()
    {
        $usuarios = User::with('roles')->paginate(10);
        return view('admin.usuarios.inicio', compact('usuarios'));
    }

    /**
     * Formulario de creación.
     */
    public function crear()
    {
        $roles = Role::all();
        return view('admin.usuarios.crear', compact('roles'));
    }

    /**
     * Guardar nuevo usuario.
     */
    public function guardar(Request $solicitud)
    {
        $solicitud->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:users,email',
            'contrasena' => 'required|string|min:8|confirmed',
            'roles' => 'required|array'
        ]);

        $usuario = User::create([
            'name' => $solicitud->nombre,
            'email' => $solicitud->correo,
            'password' => Hash::make($solicitud->contrasena),
        ]);

        $usuario->assignRole($solicitud->roles);

        return redirect()->route('admin.usuarios.inicio')->with('exito', 'Usuario creado exitosamente.');
    }

    /**
     * Formulario de edición.
     */
    public function editar($id)
    {
        $usuario = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.usuarios.editar', compact('usuario', 'roles'));
    }

    /**
     * Actualizar usuario.
     */
    public function actualizar(Request $solicitud, $id)
    {
        $usuario = User::findOrFail($id);

        $solicitud->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'roles' => 'required|array'
        ]);

        $usuario->update([
            'name' => $solicitud->nombre,
            'email' => $solicitud->correo,
        ]);

        if ($solicitud->contrasena) {
            $solicitud->validate(['contrasena' => 'string|min:8|confirmed']);
            $usuario->update(['password' => Hash::make($solicitud->contrasena)]);
        }

        $usuario->syncRoles($solicitud->roles);

        return redirect()->route('admin.usuarios.inicio')->with('exito', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar usuario.
     */
    public function eliminar($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return back()->withErrors('No puedes eliminarte a ti mismo.');
        }

        $usuario->delete();
        return redirect()->route('admin.usuarios.inicio')->with('exito', 'Usuario eliminado exitosamente.');
    }
}
