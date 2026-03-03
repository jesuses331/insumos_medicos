<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class QuotationIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteQuotation($id)
    {
        $quotation = Quotation::find($id);
        if ($quotation) {
            $quotation->delete();
            $this->dispatch('swal', ['title' => 'Cotización eliminada', 'icon' => 'success']);
        }
    }

    public function convertToSale($id)
    {
        $quotation = Quotation::with('details')->find($id);

        if (!$quotation || $quotation->status !== 'Pendiente') {
            $this->dispatch('swal', ['title' => 'Error', 'text' => 'La cotización no puede ser procesada.', 'icon' => 'error']);
            return;
        }

        try {
            DB::transaction(function () use ($quotation) {
                // Create Sale
                $sale = Sale::create([
                    'branch_id' => $quotation->branch_id,
                    'user_id' => auth()->id(),
                    'total' => $quotation->total,
                    'client_name' => $quotation->client_name,
                    'payment_method' => 'Efectivo', // Default for conversion
                    'fecha' => now()
                ]);

                foreach ($quotation->details as $detail) {
                    // Create Sale Detail
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $detail->product_id,
                        'cantidad' => $detail->cantidad,
                        'precio_unitario' => $detail->precio_unitario
                    ]);

                    // Discount Stock
                    $product = Product::find($detail->product_id);
                    $productBranch = $product->sucursales()
                        ->where('branch_id', $quotation->branch_id)
                        ->first();

                    if (!$productBranch || $productBranch->pivot->stock < $detail->cantidad) {
                        throw new \Exception("Stock insuficiente para " . $product->modelo);
                    }

                    $product->sucursales()->updateExistingPivot(
                        $quotation->branch_id,
                        ['stock' => $productBranch->pivot->stock - $detail->cantidad]
                    );
                }

                // Update Quotation Status
                $quotation->update(['status' => 'Confirmada']);
            });

            $this->dispatch('swal', ['title' => '¡Venta Exitosa!', 'text' => 'Cotización convertida en venta correctamente.', 'icon' => 'success']);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Error al convertir', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function sendByWhatsApp($id)
    {
        $quotation = Quotation::with(['details.product', 'client'])->find($id);
        if (!$quotation)
            return;

        $phone = $quotation->client ? $quotation->client->phone : null;
        if (!$phone) {
            $this->dispatch('swal', ['title' => 'Sin número', 'text' => 'El cliente no tiene un teléfono registrado.', 'icon' => 'warning']);
            return;
        }

        // Clean phone number (remove +, spaces, etc)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $message = "*COTIZACIÓN # " . str_pad($quotation->id, 5, '0', STR_PAD_LEFT) . "*\n\n";
        $message .= "Hola " . $quotation->client_name . ", adjunto el detalle de su pedido:\n\n";

        foreach ($quotation->details as $detail) {
            $subtotal = $detail->cantidad * $detail->precio_unitario;
            $message .= "• " . $detail->cantidad . "x " . $detail->product->marca . " " . $detail->product->modelo . " (Bs " . number_format($detail->precio_unitario, 2) . ") = *Bs " . number_format($subtotal, 2) . "*\n";
        }

        $message .= "\n*TOTAL ESTIMADO: Bs " . number_format($quotation->total, 2) . "*\n\n";
        $message .= "Por favor, confírmeme si desea proceder con el pedido. ¡Gracias!";

        $url = "https://wa.me/" . $phone . "?text=" . urlencode($message);

        $this->dispatch('openInNewTab', ['url' => $url]);
    }

    public function render()
    {
        $quotations = Quotation::where(function ($q) {
            $q->where('client_name', 'like', '%' . $this->search . '%')
                ->orWhere('id', 'like', '%' . $this->search . '%');
        })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.quotation-index', compact('quotations'))
            ->layout('layouts.plantilla');
    }
}
