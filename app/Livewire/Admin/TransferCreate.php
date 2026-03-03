<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;

class TransferCreate extends Component
{
    public $from_branch_id;
    public $to_branch_id;
    public $items = []; // [[id, nombre, cantidad, max]]
    public $search = '';

    public function mount()
    {
        $this->from_branch_id = session('active_sucursal_id');
        if (!$this->from_branch_id || !Sucursal::find($this->from_branch_id)) {
            return redirect()->route('admin.sucursales.seleccionar');
        }
    }

    public function addItem($productId)
    {
        $product = Product::with([
            'sucursales' => function ($q) {
                $q->where('sucursales.id', $this->from_branch_id);
            }
        ])->find($productId);

        $stock = $product->sucursales->first()->pivot->stock ?? 0;

        if ($stock <= 0) {
            $this->dispatch('swal', ['title' => 'Sin stock en origen', 'icon' => 'error']);
            return;
        }

        foreach ($this->items as $item) {
            if ($item['id'] == $productId)
                return;
        }

        $this->items[] = [
            'id' => $product->id,
            'nombre' => $product->marca->nombre . ' ' . $product->modelo,
            'cantidad' => 1,
            'max' => $stock
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function saveTransfer()
    {
        $this->validate([
            'to_branch_id' => 'required|different:from_branch_id',
            'items' => 'required|array|min:1'
        ]);

        DB::transaction(function () use (&$transfer) {
            $transfer = Transfer::create([
                'from_branch_id' => $this->from_branch_id,
                'to_branch_id' => $this->to_branch_id,
                'user_id' => auth()->id(),
                'status' => 'Enviado',
                'fecha' => now()
            ]);

            foreach ($this->items as $item) {
                TransferDetail::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $item['id'],
                    'cantidad' => $item['cantidad']
                ]);

                // Descontar del origen inmediatamente
                $product = Product::find($item['id']);
                $currentStock = $product->sucursales()->where('branch_id', $this->from_branch_id)->first()->pivot->stock;
                $product->sucursales()->updateExistingPivot($this->from_branch_id, ['stock' => $currentStock - $item['cantidad']]);
            }
        });

        // Notificar a los usuarios de la sucursal destino
        $destBranch = Sucursal::find($this->to_branch_id);
        $usersToNotify = \App\Models\User::whereHas('sucursales', function ($q) {
            $q->where('sucursales.id', $this->to_branch_id);
        })->get();

        foreach ($usersToNotify as $user) {
            $user->notify(new \App\Notifications\TransferNotification($transfer, "Nuevo traslado enviado desde " . session('active_sucursal_nombre')));
        }

        return redirect()->route('admin.traslados.inicio')->with('success', 'Traslado realizado con éxito.');
    }

    public function render()
    {
        $products = Product::where(function ($q) {
            $q->where('modelo', 'like', '%' . $this->search . '%')
                ->orWhereHas('marca', function ($m) {
                    $m->where('nombre', 'like', '%' . $this->search . '%');
                });
        })
            ->whereHas('sucursales', function ($q) {
                $q->where('sucursales.id', $this->from_branch_id)
                    ->where('stock', '>', 0);
            })
            ->get();

        $sucursales = Sucursal::where('id', '!=', $this->from_branch_id)->get();

        return view('livewire.admin.transfer-create', compact('products', 'sucursales'))
            ->layout('layouts.plantilla');
    }
}
