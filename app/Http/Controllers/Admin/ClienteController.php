<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function inicio(Request $request)
    {
        $search = $request->input('search');
        $clientes = Client::when($search, function ($q, $s) {
            $q->where('name', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%");
        })->orderBy('name')->paginate(15);

        return view('admin.clientes.inicio', compact('clientes', 'search'));
    }

    public function crear()
    {
        return view('admin.clientes.formulario');
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        Client::create($request->only(['name', 'phone']));
        return redirect()->route('admin.clientes.inicio')->with('success', 'Cliente creado');
    }

    public function editar($id)
    {
        $cliente = Client::findOrFail($id);
        return view('admin.clientes.formulario', compact('cliente'));
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        $cliente = Client::findOrFail($id);
        $cliente->update($request->only(['name', 'phone']));

        return redirect()->route('admin.clientes.inicio')->with('success', 'Cliente actualizado');
    }

    public function eliminar($id)
    {
        $cliente = Client::findOrFail($id);
        $cliente->delete();
        return redirect()->route('admin.clientes.inicio')->with('success', 'Cliente eliminado');
    }

    // AJAX search for POS
    public function search(Request $request)
    {
        $q = $request->input('q');
        $results = Client::when($q, function ($query, $q) {
            $query->where('name', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%");
        })->limit(10)->get();

        return response()->json($results);
    }
}
