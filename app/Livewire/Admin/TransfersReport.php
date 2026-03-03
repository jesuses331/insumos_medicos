<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Transfer;
use App\Models\Sucursal;
use Carbon\Carbon;

class TransfersReport extends Component
{
    public $search = '';
    public $perPage = 10;
    public $sortField = 'fecha';
    public $sortDirection = 'desc';

    // Filtros de Traslados
    public $transfers_from_date = '';
    public $transfers_to_date = '';
    public $transfers_from_branch = '';
    public $transfers_to_branch = '';
    public $transfers_status = '';

    // Datos
    public $transfers = [];
    public $branches = [];

    public function mount()
    {
        $this->validateBranch();
        $this->branches = Sucursal::all();

        // Inicializar fechas por defecto (último 30 días)
        $this->transfers_to_date = now()->format('Y-m-d');
        $this->transfers_from_date = now()->subDays(30)->format('Y-m-d');

        $this->loadTransfersReport();
    }

    public function validateBranch()
    {
        if (!session('active_sucursal_id')) {
            redirect()->route('admin.sucursales.seleccionar');
        }
    }

    #[On('loadTransfersReport')]
    public function loadTransfersReport()
    {
        $query = Transfer::query()
            ->byDateRange($this->transfers_from_date, $this->transfers_to_date)
            ->withRelations();

        if ($this->transfers_from_branch) {
            $query->fromBranch($this->transfers_from_branch);
        }

        if ($this->transfers_to_branch) {
            $query->toBranch($this->transfers_to_branch);
        }

        if ($this->transfers_status) {
            $query->byStatus($this->transfers_status);
        }

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('usuario', 'like', '%' . $this->search . '%')
                    ->orWhere('fecha', 'like', '%' . $this->search . '%');
            });
        }

        $this->transfers = $query->orderBy($this->sortField, $this->sortDirection)->get()->toArray();
    }

    public function updatedTransfersFromDate()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersToDate()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersFromBranch()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersToBranch()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersStatus()
    {
        $this->loadTransfersReport();
    }

    public function updatedSearch()
    {
        $this->loadTransfersReport();
    }

    public function setSortField($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadTransfersReport();
    }

    /**
     * Generar PDF de reporte de traslados
     */
    public function printReport()
    {
        try {
            $transfers = Transfer::query()
                ->byDateRange($this->transfers_from_date, $this->transfers_to_date)
                ->withRelations();

            if ($this->transfers_from_branch) {
                $transfers->fromBranch($this->transfers_from_branch);
            }

            if ($this->transfers_to_branch) {
                $transfers->toBranch($this->transfers_to_branch);
            }

            if ($this->transfers_status) {
                $transfers->byStatus($this->transfers_status);
            }

            $transfersData = $transfers->orderBy('fecha', 'desc')->get();

            // Calcular resumen
            $totalTransfers = $transfersData->count();
            $totalItems = $transfersData->sum(function ($transfer) {
                return $transfer->details->sum('cantidad');
            });

            $byStatus = $transfersData->groupBy('status')->map->count();

            $data = [
                'transfers' => $transfersData,
                'totalTransfers' => $totalTransfers,
                'totalItems' => $totalItems,
                'byStatus' => $byStatus,
                'fromDate' => $this->transfers_from_date,
                'toDate' => $this->transfers_to_date,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.transfers-pdf', $data);
                return $pdf->download('reporte_traslados_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            } else {
                return response()->json([
                    'message' => 'PDF library not installed.',
                    'data' => $data,
                    'status' => 'pending_dompdf'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Calcular resumen de traslados
     */
    public function getTransfersSummary()
    {
        if (empty($this->transfers)) {
            return [
                'totalTransfers' => 0,
                'totalItems' => 0,
                'byStatus' => [],
            ];
        }

        $transfers = collect($this->transfers);
        $totalItems = $transfers->sum(function ($transfer) {
            return collect($transfer['details'])->sum('cantidad');
        });

        $byStatus = $transfers->groupBy('status')->map->count();

        return [
            'totalTransfers' => count($this->transfers),
            'totalItems' => $totalItems,
            'byStatus' => $byStatus,
        ];
    }

    public function render()
    {
        return view('livewire.admin.transfers-report')->layout('layouts.plantilla');
    }
}
