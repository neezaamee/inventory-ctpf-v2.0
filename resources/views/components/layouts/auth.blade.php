<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - City Traffic Police Faisalabad</title>
    
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --ctpf-emerald: #0b3c24;
            --ctpf-emerald-dark: #062215;
            --ctpf-gold: #d4af37;
            --ctpf-bg-gradient: linear-gradient(135deg, #0b3c24 0%, #062215 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--ctpf-bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin: 0;
            padding: 1.5rem;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-top: 5px solid var(--ctpf-gold);
            border-radius: 16px;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-2px);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo i {
            font-size: 3.5rem;
            color: var(--ctpf-gold);
            text-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .auth-title {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
            margin-top: 1rem;
            text-transform: uppercase;
        }

        .auth-subtitle {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .form-control-ctpf {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .form-control-ctpf:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--ctpf-gold);
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }

        .form-label-ctpf {
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 0.5rem;
        }

        .btn-ctpf-login {
            background-color: var(--ctpf-gold);
            color: var(--ctpf-emerald-dark);
            font-weight: 700;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-ctpf-login:hover {
            background-color: #e5be3b;
            color: var(--ctpf-emerald-dark);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
    @livewireStyles
</head>
<body>

    {{ $slot }}

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @livewireScripts
</body>
</html>
