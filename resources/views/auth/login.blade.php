<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Admin Panel</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            color: white;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .login-card p {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
            color: white;
            box-shadow: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-login {
            background: white;
            color: #764ba2;
            border: none;
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 700;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .invalid-feedback {
            color: #ff9a9e;
        }

        .decor-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
        }

        .circle-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
        }
    </style>
</head>

<body>
    <div class="decor-circle circle-1"></div>
    <div class="decor-circle circle-2"></div>

    <div class="login-card">
        <h2>Bienvenido</h2>
        <p>Introduce tus credenciales para acceder</p>

        <form action="{{ route('acceder') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label small fw-bold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white opacity-50"><i
                            class="fas fa-envelope"></i></span>
                    <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror"
                        placeholder="nombre@empresa.com" value="{{ old('correo') }}" required autofocus>
                </div>
                @error('correo')
                    <div class="invalid-feedback d-block mt-2 small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white opacity-50"><i
                            class="fas fa-lock"></i></span>
                    <input type="password" name="contrasena" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="recordar" class="form-check-input" id="recordar">
                <label class="form-check-label small" for="recordar">Recordar mi sesión</label>
            </div>

            <button type="submit" class="btn btn-login">
                INGRESAR <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</body>

</html>