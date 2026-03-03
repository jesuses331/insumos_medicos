<div wire:poll.15s class="dropdown notification-bell"
    style="position: relative !important; z-index: 100001 !important; overflow: visible !important;">
    <button class="btn btn-glass position-relative p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false"
        style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-bell text-white"></i>
        @if($this->unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="font-size: 0.6rem; z-index: 1;">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg p-0"
        style="min-width: 300px; max-height: 400px; overflow-y: auto; z-index: 100002 !important; position: absolute !important;">
        <li class="p-3 border-bottom border-white-10 d-flex justify-content-between align-items-center bg-dark">
            <h6 class="m-0 text-white fw-bold">Notificaciones</h6>
            @if($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="btn btn-link text-primary p-0 small text-decoration-none"
                    style="font-size: 0.75rem;">
                    Marcar todo como leído
                </button>
            @endif
        </li>
        <div class="notification-list bg-dark">
            @forelse($this->notifications as $notification)
                <li class="border-bottom border-white-10 bg-dark">
                    <div class="dropdown-item p-3 {{ $notification->read_at ? 'opacity-50' : '' }}"
                        wire:click="markAsRead('{{ $notification->id }}')" style="cursor: pointer; white-space: normal;">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                @if(($notification->data['type'] ?? '') == 'low-stock')
                                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 35px; height: 35px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                @elseif(($notification->data['type'] ?? '') == 'expiring')
                                    <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 35px; height: 35px;">
                                        <i class="fas fa-biohazard"></i>
                                    </div>
                                @elseif(($notification->data['type'] ?? '') == 'transfer')
                                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 35px; height: 35px;">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                @else
                                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 35px; height: 35px;">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1 text-white small">
                                    {{ $notification->data['message'] ?? 'Nueva notificación' }}
                                </p>
                                <span class="text-white-50 small" style="font-size: 0.7rem;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="p-4 text-center text-white-50 bg-dark">
                    <i class="fas fa-bell-slash d-block mb-2 opacity-25" style="font-size: 2rem;"></i>
                    <span class="small">No tienes notificaciones pendientes</span>
                </li>
            @endforelse
        </div>
    </ul>

    <style>
        .notification-bell .dropdown-menu {
            z-index: 100002 !important;
            margin-top: 10px !important;
            background: #1a1d21 !important;
            /* Solid dark background */
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.8) !important;
        }

        .notification-bell .dropdown-item {
            background: #1a1d21 !important;
            color: white !important;
        }

        .notification-bell .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .notification-bell .dropdown-menu::-webkit-scrollbar {
            width: 5px;
        }

        .notification-bell .dropdown-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('new-notification', (event) => {
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                audio.play().catch(e => console.log('Audio play failed'));

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: '¡Tienes una nueva notificación!',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            });
        });
    </script>
</div>