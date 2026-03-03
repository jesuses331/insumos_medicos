<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Client;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;

class PosComponent extends Component
{
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $sucursal;
    public $qtyErrors = []; // inline quantity errors per product id
    public $client_name = '';
    public $client_search = '';
    public $clientResults = [];
    public $selected_client_id = null;
    public $new_client_name = '';
    public $new_client_phone = '';
    public $payment_method = 'Efectivo';

    public $active_register;

    public function mount()
    {
        $this->sucursal = Sucursal::find(session('active_sucursal_id'));
        if (!$this->sucursal) {
            return redirect()->route('admin.sucursales.seleccionar');
        }

        $this->checkActiveRegister();
    }

    public function checkActiveRegister()
    {
        $this->active_register = \App\Models\CashRegister::where('sucursal_id', $this->sucursal->id)
            ->where('status', 'Abierta')
            ->first();
    }

    public function addToCart($pivotId)
    {
        if (!$this->active_register) {
            $this->dispatch('swal', ['title' => 'Caja Cerrada', 'text' => 'Debe abrir una caja antes de poder realizar ventas.', 'icon' => 'warning']);
            return;
        }

        $item = DB::table('product_branch')
            ->join('products', 'product_branch.product_id', '=', 'products.id')
            ->where('product_branch.id', $pivotId)
            ->select('product_branch.*', 'products.nombre_generico', 'products.nombre_comercial', 'products.precio_venta', 'products.unidad_medida')
            ->first();

        if (!$item || $item->stock <= 0) {
            $this->dispatch('swal', ['title' => 'Sin stock', 'icon' => 'error']);
            return;
        }

        $cartKey = $pivotId; // Use pivot ID as unique key in cart

        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['cantidad'] < $item->stock) {
                $this->cart[$cartKey]['cantidad']++;
            } else {
                $this->dispatch('swal', ['title' => 'Stock insuficiente', 'icon' => 'error']);
                return;
            }
        } else {
            $this->cart[$cartKey] = [
                'id' => $item->product_id,
                'pivot_id' => $pivotId,
                'nombre' => $item->nombre_comercial . ' (' . ($item->nombre_generico ?: 'Genérico') . ')',
                'lote' => $item->lote,
                'fecha_vencimiento' => $item->fecha_vencimiento,
                'precio' => $item->precio_venta,
                'cantidad' => 1,
                'stock' => $item->stock,
                'unidad' => $item->unidad_medida
            ];
        }
        $this->calculateTotal();
    }

    public function increaseQuantity($productId)
    {
        if (!isset($this->cart[$productId]))
            return;

        $stock = $this->cart[$productId]['stock'] ?? 0;

        if ($this->cart[$productId]['cantidad'] < $stock) {
            $this->cart[$productId]['cantidad']++;
            // clear any previous inline error for this product
            if (isset($this->qtyErrors[$productId]))
                unset($this->qtyErrors[$productId]);
            $this->calculateTotal();
        } else {
            // set inline error instead of modal alert
            $this->qtyErrors[$productId] = 'Stock insuficiente';
        }
    }

    public function decreaseQuantity($productId)
    {
        if (!isset($this->cart[$productId]))
            return;

        if ($this->cart[$productId]['cantidad'] > 1) {
            $this->cart[$productId]['cantidad']--;
            // clear any inline error when user decreases
            if (isset($this->qtyErrors[$productId]))
                unset($this->qtyErrors[$productId]);
            $this->calculateTotal();
        } else {
            // si llega a 1 y bajan, se elimina del carrito
            $this->removeFromCart($productId);
        }
    }

    public function setQuantity($productId, $quantity)
    {
        if (!isset($this->cart[$productId]))
            return;
        $quantity = (int) $quantity;
        if ($quantity < 1) {
            $this->removeFromCart($productId);
            return;
        }
        $stock = $this->cart[$productId]['stock'] ?? 0;

        if ($quantity > $stock) {
            // set inline error and clamp to stock
            $this->qtyErrors[$productId] = 'Stock insuficiente';
            $this->cart[$productId]['cantidad'] = $stock;
        } else {
            // valid quantity: clear error and set
            if (isset($this->qtyErrors[$productId]))
                unset($this->qtyErrors[$productId]);
            $this->cart[$productId]['cantidad'] = $quantity;
        }

        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['precio'] * $item['cantidad']);
        }, 0);
    }

    public function processSale()
    {
        if (empty($this->cart)) {
            $this->dispatch('swal', ['title' => 'Cesta vacía', 'icon' => 'warning']);
            return;
        }

        try {
            DB::transaction(function () {
                $sale = Sale::create([
                    'branch_id' => $this->sucursal->id,
                    'user_id' => auth()->id(),
                    'cash_register_id' => $this->active_register->id,
                    'total' => $this->total,
                    'client_name' => $this->client_name ?: 'Cliente General',
                    'payment_method' => $this->payment_method,
                    'fecha' => now()
                ]);

                // Actualizar el total de la caja
                $this->active_register->increment('total_ventas', $this->total);

                foreach ($this->cart as $item) {
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio']
                    ]);

                    // Descontar stock del LOTE específico (pivot_id)
                    $affected = DB::table('product_branch')
                        ->where('id', $item['pivot_id'])
                        ->where('stock', '>=', $item['cantidad'])
                        ->decrement('stock', $item['cantidad']);

                    if (!$affected) {
                        throw new \Exception("Stock insuficiente para el lote " . ($item['lote'] ?? 'N/A') . " de " . $item['nombre']);
                    }
                }
            });

            $this->cart = [];
            $this->total = 0;
            $this->client_name = '';
            $this->selected_client_id = null;
            $this->payment_method = 'Efectivo';
            $this->dispatch('swal', ['title' => '¡Venta Exitosa!', 'text' => 'La transacción se ha registrado correctamente.', 'icon' => 'success']);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Error en la venta', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    // Livewire: update search and results
    public function updatedClientSearch($value)
    {
        $this->clientResults = Client::where('name', 'like', "%{$value}%")
            ->orWhere('phone', 'like', "%{$value}%")
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function selectClient($id)
    {
        $client = Client::find($id);
        if (!$client)
            return;
        $this->selected_client_id = $client->id;
        $this->client_name = $client->name;
        $this->client_search = '';
        $this->clientResults = [];
    }

    public function clearClient()
    {
        $this->selected_client_id = null;
        $this->client_name = '';
        $this->client_search = '';
        $this->clientResults = [];
    }

    public function createClient()
    {
        $name = trim($this->new_client_name);
        $phone = trim($this->new_client_phone);
        if (!$name) {
            $this->dispatch('swal', ['title' => 'Nombre requerido', 'icon' => 'error']);
            return;
        }
        $client = Client::create(['name' => $name, 'phone' => $phone ?: null]);
        $this->selectClient($client->id);
        $this->new_client_name = '';
        $this->new_client_phone = '';
        $this->dispatch('swal', ['title' => 'Cliente creado', 'text' => $client->name, 'icon' => 'success']);
        $this->dispatch('close-new-client-modal');
    }

    public function render()
    {
        $products = Product::join('product_branch', 'products.id', '=', 'product_branch.product_id')
            ->where('product_branch.branch_id', $this->sucursal->id)
            ->where('product_branch.stock', '>', 0)
            ->where(function ($q) {
                $q->where('products.nombre_comercial', 'like', '%' . $this->search . '%')
                    ->orWhere('products.nombre_generico', 'like', '%' . $this->search . '%')
                    ->orWhere('product_branch.lote', 'like', '%' . $this->search . '%');
            })
            ->select('products.*', 'product_branch.id as pivot_id', 'product_branch.stock', 'product_branch.lote', 'product_branch.fecha_vencimiento')
            ->orderBy('product_branch.fecha_vencimiento', 'asc')
            ->get();

        return view('livewire.admin.pos-component', compact('products'))
            ->layout('layouts.plantilla');
    }
}
