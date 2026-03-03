<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Sucursal;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class QuotationCreate extends Component
{
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $sucursal;
    public $client_name = '';
    public $client_search = '';
    public $clientResults = [];
    public $selected_client_id = null;

    public function mount()
    {
        $this->sucursal = Sucursal::find(session('active_sucursal_id'));
        if (!$this->sucursal) {
            return redirect()->route('admin.sucursales.seleccionar');
        }
    }

    public function addToCart($productId)
    {
        $product = Product::with([
            'sucursales' => function ($q) {
                $q->where('sucursales.id', $this->sucursal->id);
            }
        ])->find($productId);

        $stock = $product->sucursales->first()->pivot->stock ?? 0;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['cantidad']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'nombre' => $product->marca->nombre . ' ' . $product->modelo . ($product->tipoRepuesto ? ' (' . $product->tipoRepuesto->nombre . ')' : ''),
                'precio' => $product->precio_venta,
                'cantidad' => 1,
                'stock' => $stock
            ];
        }
        $this->calculateTotal();
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
        $this->cart[$productId]['cantidad'] = $quantity;
        $this->calculateTotal();
    }

    public function increaseQuantity($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['cantidad']++;
            $this->calculateTotal();
        }
    }

    public function decreaseQuantity($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['cantidad'] > 1) {
                $this->cart[$productId]['cantidad']--;
            } else {
                $this->removeFromCart($productId);
            }
            $this->calculateTotal();
        }
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

    public function saveQuotation()
    {
        if (empty($this->cart)) {
            $this->dispatch('swal', ['title' => 'Cotización vacía', 'icon' => 'warning']);
            return;
        }

        if (!$this->selected_client_id) {
            $this->dispatch('swal', ['title' => 'Cliente requerido', 'text' => 'Debe seleccionar un cliente para generar la cotización.', 'icon' => 'warning']);
            return;
        }

        $client = Client::find($this->selected_client_id);
        if (!$client || empty($client->phone)) {
            $this->dispatch('swal', ['title' => 'Teléfono requerido', 'text' => 'El cliente seleccionado debe tener un número de teléfono registrado.', 'icon' => 'warning']);
            return;
        }

        try {
            DB::transaction(function () use ($client) {
                $quotation = Quotation::create([
                    'branch_id' => $this->sucursal->id,
                    'user_id' => auth()->id(),
                    'client_id' => $this->selected_client_id,
                    'total' => $this->total,
                    'client_name' => $client->name,
                    'status' => 'Pendiente',
                    'fecha' => now()
                ]);

                foreach ($this->cart as $item) {
                    QuotationDetail::create([
                        'quotation_id' => $quotation->id,
                        'product_id' => $item['id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio']
                    ]);
                }
            });

            $this->cart = [];
            $this->total = 0;
            $this->client_name = '';
            $this->selected_client_id = null;
            $this->dispatch('swal', ['title' => '¡Cotización Guardada!', 'text' => 'La cotización se ha registrado correctamente.', 'icon' => 'success']);
            return redirect()->route('admin.quotations.index');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Error', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        $products = Product::where(function ($q) {
            $q->where('modelo', 'like', '%' . $this->search . '%')
                ->orWhereHas('marca', function ($m) {
                    $m->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('tipoRepuesto', function ($t) {
                    $t->where('nombre', 'like', '%' . $this->search . '%');
                });
        })
            ->whereHas('sucursales', function ($q) {
                $q->where('sucursales.id', $this->sucursal->id);
            })
            ->with([
                'sucursales' => function ($q) {
                    $q->where('sucursales.id', $this->sucursal->id);
                }
            ])
            ->get();

        return view('livewire.admin.quotation-create', compact('products'))
            ->layout('layouts.plantilla');
    }
}
