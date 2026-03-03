<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public function getNotificationsProperty()
    {
        $dbNotifications = Auth::user()->notifications()->latest()->take(5)->get();

        $alertsService = new \App\Services\InsumosAlerts();
        $medicalAlerts = collect();

        // Low stock alerts
        foreach ($alertsService->getLowStock() as $alert) {
            $medicalAlerts->push((object) [
                'id' => 'low-stock-' . $alert->lote,
                'type' => 'medical-warning',
                'data' => [
                    'message' => "Stock bajo: {$alert->nombre_comercial} (Lote: {$alert->lote}) - Solo {$alert->stock} unid.",
                    'type' => 'low-stock'
                ],
                'created_at' => now(),
                'read_at' => null
            ]);
        }

        // Expiring soon (30 days)
        foreach ($alertsService->getExpiringProducts(30) as $alert) {
            $medicalAlerts->push((object) [
                'id' => 'expiring-' . $alert->lote,
                'type' => 'medical-danger',
                'data' => [
                    'message' => "¡VENCE PRONTO!: {$alert->nombre_comercial} (Lote: {$alert->lote}) vence el " . \Carbon\Carbon::parse($alert->fecha_vencimiento)->format('d/m/Y'),
                    'type' => 'expiring'
                ],
                'created_at' => now(),
                'read_at' => null
            ]);
        }

        return $medicalAlerts->concat($dbNotifications)->take(10);
    }

    public function getUnreadCountProperty()
    {
        $count = Auth::user()->unreadNotifications()->count();
        if ($count > session('last_notification_count', 0)) {
            $this->dispatch('new-notification');
        }
        session(['last_notification_count' => $count]);
        return $count;
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.admin.notification-bell');
    }
}
