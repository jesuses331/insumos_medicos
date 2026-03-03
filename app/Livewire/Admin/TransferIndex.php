<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transfer;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;

class TransferIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        if (!session()->has('active_sucursal_id') || !Sucursal::find(session('active_sucursal_id'))) {
            return redirect()->route('admin.sucursales.seleccionar');
        }
    }
    public function updateStatus($transferId, $status)
    {
        $transfer = Transfer::findOrFail($transferId);

        if ($status == 'Enviado') {
            // Lógica de salida de stock ya se maneja en TransferCreate, o aquí si es desde index
            $transfer->update(['status' => 'Enviado']);
        } elseif ($status == 'Recibido') {
            DB::transaction(function () use ($transfer) {
                foreach ($transfer->details as $detail) {
                    $product = $detail->product;
                    $currentStock = $product->sucursales()->where('branch_id', $transfer->to_branch_id)->first()->pivot->stock ?? 0;
                    $product->sucursales()->syncWithoutDetaching([$transfer->to_branch_id => ['stock' => $currentStock + $detail->cantidad]]);
                }
                $transfer->update(['status' => 'Recibido']);
            });
        }

        $this->dispatch('swal', ['title' => 'Estado actualizado', 'icon' => 'success']);
    }

    public function render()
    {
        $transfers = Transfer::with(['fromSucursal', 'toSucursal', 'user'])
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        return view('livewire.admin.transfer-index', compact('transfers'))
            ->layout('layouts.plantilla');
    }
}
