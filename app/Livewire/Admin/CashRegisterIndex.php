<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\CashRegister;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CashRegisterIndex extends Component
{
    public $monto_apertura;
    public $observaciones;
    public $caja_id;
    public $user_id; // Nuevo: Para elegir a quién se le abre la caja
    public $active_register;

    protected $rules = [
        'monto_apertura' => 'required|numeric|min:0',
        'caja_id' => 'required',
        'user_id' => 'required',
    ];

    public function mount()
    {
        if (!Auth::user()->can('gestionar-cajas')) {
            abort(403, 'No tienes permiso para gestionar cajas.');
        }
        $this->checkActiveRegister();
    }

    public function checkActiveRegister()
    {
        $this->active_register = CashRegister::where('sucursal_id', session('active_sucursal_id'))
            ->where('status', 'Abierta')
            ->first();
    }

    public function abrirCaja()
    {
        $this->validate();

        $caja = \App\Models\Caja::find($this->caja_id);

        $register = CashRegister::create([
            'user_id' => $this->user_id, // Usar el usuario seleccionado
            'sucursal_id' => $caja->sucursal_id,
            'caja_id' => $this->caja_id,
            'fecha_apertura' => Carbon::now(),
            'monto_apertura' => $this->monto_apertura,
            'status' => 'Abierta',
            'observaciones' => $this->observaciones,
        ]);

        // Notificar al usuario seleccionado
        $targetUser = \App\Models\User::find($this->user_id);
        if ($targetUser) {
            $targetUser->notify(new \App\Notifications\CashRegisterNotification($register, "Tu caja ha sido abierta correctamente. ¡Buen turno!"));
        }

        $this->reset(['monto_apertura', 'observaciones', 'user_id']);
        $this->checkActiveRegister();
        session()->flash('success', 'Caja abierta correctamente.');
    }

    public function cerrarCaja()
    {
        if (!Auth::user()->can('gestionar-cajas')) {
            $this->dispatch('swal', ['title' => 'Acceso denegado', 'text' => 'Solo administradores pueden cerrar la caja.', 'icon' => 'error']);
            return;
        }

        if ($this->active_register) {
            $this->cerrarCajaEspecifica($this->active_register->id);
            $this->active_register = null;
        }
    }

    public function cerrarCajaEspecifica($id)
    {
        if (!Auth::user()->can('gestionar-cajas')) {
            $this->dispatch('swal', ['title' => 'Acceso denegado', 'icon' => 'error']);
            return;
        }

        $register = CashRegister::findOrFail($id);
        $register->update([
            'fecha_cierre' => Carbon::now(),
            'status' => 'Cerrada',
            'monto_cierre' => $register->total_ventas + $register->monto_apertura,
        ]);

        $this->checkActiveRegister();
        session()->flash('success', 'Caja cerrada correctamente.');
    }

    public function render()
    {
        $regQuery = CashRegister::query();
        if (!Auth::user()->can('ver-reportes-globales')) {
            $regQuery->where('sucursal_id', session('active_sucursal_id'));
        }
        $registers = $regQuery->orderBy('id', 'desc')->paginate(10);

        $cajasQuery = \App\Models\Caja::where('estado', 'Activo');
        if (!Auth::user()->can('ver-reportes-globales')) {
            $cajasQuery->where('sucursal_id', session('active_sucursal_id'));
        }
        $cajas = $cajasQuery->get();

        $users = \App\Models\User::orderBy('name')->get();

        return view('livewire.admin.cash-register-index', [
            'registers' => $registers,
            'available_cajas' => $cajas,
            'users' => $users,
        ])->layout('layouts.plantilla');
    }
}
