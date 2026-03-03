<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;

class SaleIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $fecha_inicio;
    public $fecha_fin;
    public $selected_sale = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFechaInicio()
    {
        $this->resetPage();
    }

    public function updatingFechaFin()
    {
        $this->resetPage();
    }

    public function viewDetails($saleId)
    {
        $this->selected_sale = Sale::with('details.product')->find($saleId);
        $this->dispatch('openDetailModal');
    }

    public function closeDetails()
    {
        $this->selected_sale = null;
    }

    public function render()
    {
        $query = Sale::with(['user', 'cashRegister.caja', 'sucursal']);

        if (auth()->user()->can('ver-reportes-globales')) {
            $query->withoutGlobalScope('branch');
        }

        $sales = $query->where(function ($q) {
            $q->where('client_name', 'like', '%' . $this->search . '%')
                ->orWhere('id', 'like', '%' . $this->search . '%');
        })
            ->when($this->fecha_inicio, function ($q) {
                $q->whereDate('fecha', '>=', $this->fecha_inicio);
            })
            ->when($this->fecha_fin, function ($q) {
                $q->whereDate('fecha', '<=', $this->fecha_fin);
            })
            ->when(!auth()->user()->can('ver-reportes-globales'), function ($q) {
                $q->where('user_id', auth()->id());
                $q->where('branch_id', session('active_sucursal_id'));
            })
            ->latest('fecha')
            ->paginate(10);

        return view('livewire.admin.sale-index', compact('sales'))
            ->layout('layouts.plantilla');
    }
}
