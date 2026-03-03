<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Caja;
use App\Models\Sucursal;
use Livewire\WithPagination;

class CajaManager extends Component
{
    use WithPagination;

    public $nombre;
    public $estado = 'Activo';
    public $sucursal_id;
    public $selected_id;
    public $updateMode = false;

    protected $rules = [
        'nombre' => 'required|min:3',
        'estado' => 'required',
        'sucursal_id' => 'required',
    ];

    public function mount()
    {
        $this->sucursal_id = session('active_sucursal_id');
    }

    public function resetInputFields()
    {
        $this->nombre = '';
        $this->estado = 'Activo';
        $this->sucursal_id = session('active_sucursal_id');
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();

        Caja::create([
            'sucursal_id' => $this->sucursal_id,
            'nombre' => $this->nombre,
            'estado' => $this->estado,
        ]);

        session()->flash('success', 'Caja creada correctamente.');
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $record = Caja::findOrFail($id);
        $this->selected_id = $id;
        $this->nombre = $record->nombre;
        $this->estado = $record->estado;
        $this->sucursal_id = $record->sucursal_id;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();

        if ($this->selected_id) {
            $record = Caja::find($this->selected_id);
            $record->update([
                'sucursal_id' => $this->sucursal_id,
                'nombre' => $this->nombre,
                'estado' => $this->estado,
            ]);

            session()->flash('success', 'Caja actualizada correctamente.');
            $this->resetInputFields();
        }
    }

    public function delete($id)
    {
        Caja::find($id)->delete();
        session()->flash('success', 'Caja eliminada correctamente.');
    }

    public function render()
    {
        $query = Caja::with('sucursal');
        if (!auth()->user()->can('ver-reportes-globales')) {
            $query->where('sucursal_id', session('active_sucursal_id'));
        }

        return view('livewire.admin.caja-manager', [
            'cajas' => $query->paginate(10),
            'sucursales' => Sucursal::all()
        ])->layout('layouts.plantilla');
    }
}
