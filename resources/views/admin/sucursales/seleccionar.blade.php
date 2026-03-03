<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Sucursal - Sistema Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .sucursal-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sucursal-item:hover {
            transform: translateY(-5px);
            background: rgba(56, 189, 248, 0.1);
            border-color: rgba(56, 189, 248, 0.4);
        }

        .gradient-text {
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-premium {
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
            transform: scale(1.02);
        }
    </style>
</head>

<body>
    <div class="glass-card p-10 w-full max-w-2xl mx-4 relative overflow-hidden">
        <!-- Decoración -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-sky-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl"></div>

        <div class="text-center mb-10">
            <h1 class="text-4xl font-semibold mb-2 gradient-text">Bienvenido de nuevo</h1>
            <p class="text-slate-400">Por favor, selecciona la sucursal donde operarás hoy</p>
        </div>

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($sucursales as $sucursal)
                <form action="{{ route('admin.sucursales.establecer') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sucursal_id" value="{{ $sucursal->id }}">
                    <button type="submit"
                        class="w-full text-left sucursal-item group glass-card p-6 border border-slate-700/50 block">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-slate-800 rounded-xl group-hover:bg-sky-500/20 transition-colors">
                                <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">{{ $sucursal->nombre }}</h3>
                                <p class="text-slate-400 text-xs truncate max-w-[180px]">{{ $sucursal->direccion }}</p>
                            </div>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="text-slate-500 hover:text-white transition-colors text-sm flex items-center justify-center mx-auto space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</body>

</html>