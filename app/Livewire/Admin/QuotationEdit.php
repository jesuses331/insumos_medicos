<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Sucursal;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class QuotationEdit extends Component
{
    public $quotationId;
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $sucursal;
    public $client_name = '';
    public $client_search = '';
    public $clientResults = [];
    public $selected_client_id = null;

    public function mount($id)
    {
        $this->quotationId = $id;
        $quotation = Quotation::with('details.product')->findOrFail($id);

        if ($quotation->status !== 'Pendiente') {
            session()->flash('error', 'Solo se pueden editar cotizaciones pendientes.');
            return redirect()->route('admin.quotations.index');
        }

        $this->sucursal = Sucursal::find($quotation->branch_id);
        $this->client_name = $quotation->client_name;
        $this->selected_client_id = $quotation->client_id;
        $this->total = $quotation->total;

        foreach ($quotation->details as $detail) {
            $product = $detail->product;
            $stock = $product->sucursales()->where('branch_id', $this->sucursal->id)->first()->pivot->stock ?? 0;

            $this->cart[$detail->product_id] = [
                'id' => $detail->product_id,
                'nombre' => $product->marca->nombre . ' ' . $product->modelo . ($product->tipoRepuesto ? ' (' . $product->tipoRepuesto->nombre . ')' : ''),
                'precio' => $detail->precio_unitario,
                'cantidad' => $detail->cantidad,
                'stock' => $stock
            ];
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

    public function updateQuotation()
    {
        if (empty($this->cart)) {
            $this->dispatch('swal', ['title' => 'Cotización vacía', 'icon' => 'warning']);
            return;
        }

        try {
            DB::transaction(function () {
                $quotation = Quotation::findOrFail($this->quotationId);
                $quotation->update([
                    'client_id' => $this->selected_client_id,
                    'client_name' => $this->client_name ?: 'Cliente General',
                    'total' => $this->total,
                ]);


                // Clear and recreate details
                $quotation->details()->delete();

                foreach ($this->cart as $item) {
                    QuotationDetail::create([
                        'quotation_id' => $quotation->id,
                        'product_id' => $item['id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio']
                    ]);
                }
            });

            $this->dispatch('swal', ['title' => '¡Cotización Actualizada!', 'icon' => 'success']);
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

        return view('livewire.admin.quotation-create', compact('products')) // Reuse create view with logic changes
            ->layout('layouts.plantilla');
    }
}
