<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Emdell</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --orange: #FF6B35;
            --gold: #FFC107;
            --card: rgba(13, 15, 22, 0.82);
            --input: rgba(255, 255, 255, 0.06);
            --border: rgba(255, 255, 255, 0.10);
        }

        body {
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .bg-image {
            position: fixed;
            inset: 0;
            background-image: url("{{ asset('imagenes/almacenn.png') }}");
            background-size: cover;
            background-position: center;
            z-index: 0;
        }

        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(160deg,
                    rgba(10, 11, 18, 0.72) 0%,
                    rgba(10, 11, 18, 0.60) 50%,
                    rgba(10, 11, 18, 0.80) 100%);
            z-index: 1;
        }

        .page-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 1.5rem 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-wrap {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .logo-wrap img {
            max-width: 330px;
            max-height: 250px;
            object-fit: contain;
            filter: drop-shadow(0 4px 20px rgba(255, 107, 53, 0.45));
        }

        .login-card {
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2rem 1.75rem 1.75rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .login-heading {
            font-family: 'Syne', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            text-align: center;
            margin-bottom: 0.2rem;
        }

        .login-sub {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.35);
            text-align: center;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            margin-bottom: 1.75rem;
        }

        .form-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.4rem;
            display: block;
        }

        .input-group-text {
            background: var(--input) !important;
            border: 1px solid var(--border) !important;
            border-right: none !important;
            color: rgba(255, 255, 255, 0.3) !important;
            padding-left: 1rem;
        }

        .form-control {
            background: var(--input) !important;
            border: 1px solid var(--border) !important;
            border-left: none !important;
            color: #fff !important;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            padding: 0.7rem 0.9rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: rgba(255, 193, 7, 0.5) !important;
            box-shadow: none !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.18) !important;
        }

        .input-group:focus-within .input-group-text {
            border-color: rgba(255, 193, 7, 0.5) !important;
        }

        .btn-toggle-pass {
            background: var(--input) !important;
            border: 1px solid var(--border) !important;
            border-left: none !important;
            color: rgba(255, 255, 255, 0.3) !important;
        }

        .btn-toggle-pass:hover {
            color: var(--gold) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--orange), var(--gold));
            border: none;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.04em;
            padding: 0.8rem;
            border-radius: 14px;
            width: 100%;
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 0.5rem;
            cursor: pointer;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(255, 107, 53, 0.55);
            color: #fff;
        }

        .alert-login {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            color: #FCA5A5;
            font-size: 0.84rem;
            padding: 0.7rem 1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.18);
        }
    </style>
</head>

<body>
    <div class="bg-image"></div>
    <div class="bg-overlay"></div>
    <div class="page-content">
        <div class="logo-wrap">
            <img src="{{ asset('imagenes/logo_emdell_2.png') }}" alt="Emdell Logo">
        </div>
        <div class="login-card">
            @if(session('error'))
                <div class="alert-login">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope-fill"></i>
                        </span>
                        <input type="email" name="email" class="form-control" placeholder="correo@emdell.com"
                            value="{{ old('email') }}" autocomplete="email" required autofocus>
                    </div>
                    @error('email')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" name="password" id="passwordInput" class="form-control"
                            placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="btn btn-toggle-pass" onclick="togglePassword()">
                            <i class="bi bi-eye-fill" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            icon.className = visible ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
        }
    </script>
</body>

</html>