<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sucursal;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\TipoRepuesto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function inicio(Request $request)
    {
        $search = $request->input('search');
        $productos = Product::with(['sucursales', 'categoria'])
            ->when($search, function ($query, $search) {
                return $query->where('nombre_comercial', 'like', "%{$search}%")
                    ->orWhere('nombre_generico', 'like', "%{$search}%")
                    ->orWhereHas('categoria', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->paginate(15);

        if ($request->ajax() || $request->has('ajax')) {
            return view('admin.productos._table_rows', compact('productos'))->render();
        }

        return view('admin.productos.inicio', compact('productos', 'search'));
    }

    public function crear()
    {
        $sucursales = Sucursal::all();
        $categorias = Categoria::all();
        $unidades = ['Caja', 'Unidad', 'Blister', 'Frasco'];
        return view('admin.productos.crear', compact('sucursales', 'categorias', 'unidades'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre_generico' => 'nullable|string',
            'nombre_comercial' => 'required|string',
            'unidad_medida' => 'required|in:Caja,Unidad,Blister,Frasco',
            'costo' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'stock' => 'required|array',
            'stock.*' => 'integer|min:0'
        ]);

        $producto = Product::create($request->only(['nombre_generico', 'nombre_comercial', 'unidad_medida', 'costo', 'precio_venta', 'categoria_id']));

        foreach ($request->stock as $sucursal_id => $stock) {
            $producto->sucursales()->attach($sucursal_id, ['stock' => $stock]);
        }

        return redirect()->route('admin.productos.inicio')->with('success', 'Producto creado exitosamente.');
    }

    public function editar($id)
    {
        $producto = Product::with('sucursales')->findOrFail($id);
        $sucursales = Sucursal::all();
        $categorias = Categoria::all();
        $unidades = ['Caja', 'Unidad', 'Blister', 'Frasco'];
        return view('admin.productos.editar', compact('producto', 'sucursales', 'categorias', 'unidades'));
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre_generico' => 'nullable|string',
            'nombre_comercial' => 'required|string',
            'unidad_medida' => 'required|in:Caja,Unidad,Blister,Frasco',
            'costo' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'stock' => 'required|array',
            'stock.*' => 'integer|min:0'
        ]);

        $producto = Product::findOrFail($id);
        $producto->update($request->only(['nombre_generico', 'nombre_comercial', 'unidad_medida', 'costo', 'precio_venta', 'categoria_id']));

        $stocks = [];
        foreach ($request->stock as $sucursal_id => $stock) {
            $stocks[$sucursal_id] = ['stock' => $stock];
        }
        $producto->sucursales()->sync($stocks);

        return redirect()->route('admin.productos.inicio')->with('success', 'Producto actualizado exitosamente.');
    }

    public function eliminar($id)
    {
        $producto = Product::findOrFail($id);
        $producto->delete();
        return redirect()->route('admin.productos.inicio')->with('success', 'Producto eliminado exitosamente.');
    }

    public function reportarDefectuoso(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'cantidad' => 'required|integer|min:1',
            'detalle' => 'required|string|min:5'
        ]);

        $productId = $request->id;
        $sucursalId = session('active_sucursal_id');
        $cantidad = $request->cantidad;

        // Verificar stock antes de reportar
        $product = Product::find($productId);
        $currentStock = $product->sucursales()->where('branch_id', $sucursalId)->first()->pivot->stock ?? 0;

        if ($currentStock < $cantidad) {
            return response()->json(['success' => false, 'message' => 'No hay suficiente stock para reportar esta cantidad.'], 400);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($productId, $sucursalId, $cantidad, $request, $product, $currentStock) {
            // 1. Crear el registro de defectuoso
            \App\Models\DefectiveProduct::create([
                'product_id' => $productId,
                'sucursal_id' => $sucursalId,
                'user_id' => auth()->id(),
                'cantidad' => $cantidad,
                'detalle' => $request->detalle,
                'estado' => 'Pendiente'
            ]);

            // 2. Descontar del stock de la sucursal
            $product->sucursales()->updateExistingPivot($sucursalId, ['stock' => $currentStock - $cantidad]);
        });

        return response()->json(['success' => true, 'message' => 'Producto defectuoso reportado y descontado del stock.']);
    }
    public function reporteDefectuosos()
    {
        $defectuosos = \App\Models\DefectiveProduct::with(['product', 'sucursal', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.productos.reporte-defectuosos', compact('defectuosos'));
    }

    public function repuestoDefectuoso($id)
    {
        $defectuoso = \App\Models\DefectiveProduct::findOrFail($id);
        $defectuoso->update(['estado' => 'Repuesto']);

        return back()->with('success', 'Producto marcado como repuesto.');
    }

    public function inventarioGlobal()
    {
        $sucursales = Sucursal::all();
        $productos = Product::with('sucursales')->get();
        return view('admin.productos.inventario-global', compact('productos', 'sucursales'));
    }
}
