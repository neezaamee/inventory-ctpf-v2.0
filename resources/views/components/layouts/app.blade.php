<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Wardi Godaam Portal - City Traffic Police Faisalabad' }}</title>
    
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
            --ctpf-emerald-light: #164f33;
            --ctpf-gold: #d4af37;
            --ctpf-gold-dark: #b89428;
            --ctpf-accent: #f4ebd0;
            --ctpf-bg: #f8f9fa;
            --ctpf-text: #2d3748;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--ctpf-bg);
            color: var(--ctpf-text);
            overflow-x: hidden;
        }

        /* Premium Top Navbar */
        .ctpf-navbar {
            background-color: var(--ctpf-emerald);
            border-bottom: 3px solid var(--ctpf-gold);
            padding: 0.75rem 1.5rem;
        }

        .ctpf-brand {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .ctpf-brand span {
            color: var(--ctpf-gold);
            margin-left: 0.5rem;
        }

        /* Sidebar Styling */
        .ctpf-sidebar {
            background-color: var(--ctpf-emerald);
            min-height: calc(100vh - 62px);
            border-right: 3px solid var(--ctpf-gold);
            padding-top: 1.5rem;
        }

        .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            border-left: 4px solid transparent;
            font-size: 0.95rem;
        }

        .sidebar-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .sidebar-link:hover {
            color: var(--ctpf-gold);
            background-color: var(--ctpf-emerald-light);
            border-left-color: var(--ctpf-gold);
        }

        .sidebar-link.active {
            color: var(--ctpf-gold);
            background-color: var(--ctpf-emerald-light);
            border-left-color: var(--ctpf-gold);
            font-weight: 600;
        }

        .sidebar-heading {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1.5rem 1.5rem 0.5rem;
            font-weight: 700;
        }

        /* Content Card Styling */
        .ctpf-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            margin-bottom: 1.5rem;
            border-top: 4px solid var(--ctpf-gold);
        }

        .ctpf-card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            color: var(--ctpf-emerald);
        }

        .btn-ctpf-primary {
            background-color: var(--ctpf-emerald);
            color: #ffffff;
            font-weight: 600;
            border: 1px solid var(--ctpf-emerald);
            transition: all 0.2s ease;
        }

        .btn-ctpf-primary:hover {
            background-color: var(--ctpf-emerald-light);
            border-color: var(--ctpf-emerald-light);
            color: var(--ctpf-gold);
        }

        .btn-ctpf-outline {
            border: 2px solid var(--ctpf-emerald);
            color: var(--ctpf-emerald);
            font-weight: 600;
            background: transparent;
            transition: all 0.2s ease;
        }

        .btn-ctpf-outline:hover {
            background-color: var(--ctpf-emerald);
            color: #ffffff;
        }

        .badge-ctpf-gold {
            background-color: var(--ctpf-gold);
            color: var(--ctpf-emerald);
            font-weight: 700;
        }
        
        .badge-ctpf-emerald {
            background-color: var(--ctpf-emerald);
            color: var(--ctpf-gold);
            font-weight: 700;
        }

        /* Alert notifications styling */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
        }
    </style>
    @livewireStyles
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg ctpf-navbar sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand ctpf-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-check text-warning me-2"></i>
                CTPF<span>WARDI GODAAM</span>
            </a>
            
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="topNav">
                @auth
                <div class="d-flex align-items-center">
                    <span class="text-white me-3 d-none d-md-inline">
                        <i class="bi bi-person-badge text-warning me-1"></i>
                        {{ Auth::user()->name }} 
                        <span class="badge badge-ctpf-gold ms-1">{{ Auth::user()->roles->first()?->name ?? 'User' }}</span>
                    </span>
                    <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-light me-2 fw-bold text-white">
                        <i class="bi bi-person-circle text-warning me-1"></i> Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning border-2 fw-bold">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 d-none d-md-block ctpf-sidebar">
                <div class="d-flex flex-column">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>

                    @if(auth()->user()->can('create-issuance') || auth()->user()->can('process-return') || auth()->user()->can('create-grn'))
                    <div class="sidebar-heading">Operations</div>
                    @can('create-issuance')
                    <a href="{{ route('issuance.create') }}" class="sidebar-link {{ Route::is('issuance.create') ? 'active' : '' }}">
                        <i class="bi bi-cart-plus"></i> New Issuance
                    </a>
                    @endcan
                    @can('process-return')
                    <a href="{{ route('returns.index') }}" class="sidebar-link {{ Route::is('returns.index') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i> Returns & Exchanges
                    </a>
                    @endcan
                    @can('create-grn')
                    <a href="{{ route('grn.index') }}" class="sidebar-link {{ Route::is('grn.index') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-arrow-down"></i> Goods Received (GRN)
                    </a>
                    @endcan
                    @endif

                    <div class="sidebar-heading">Registry</div>
                    <a href="{{ route('staff.index') }}" class="sidebar-link {{ Route::is('staff.index') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Staff & Wardens
                    </a>
                    <a href="{{ route('items.index') }}" class="sidebar-link {{ Route::is('items.index') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Inventory Items
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ Route::is('suppliers.index') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Suppliers
                    </a>

                    @can('view-audit-logs')
                    <div class="sidebar-heading">Administration</div>
                    <a href="{{ route('audits.index') }}" class="sidebar-link {{ Route::is('audits.index') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Audit Logs
                    </a>
                    @can('manage-users')
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ Route::is('users.index') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i> User Management
                    </a>
                    @endcan
                    @role('Super Admin|Admin')
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-gear"></i> System Settings
                    </a>
                    @endrole
                    @endcan
                </div>
            </div>

            <!-- Main Content Pane -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                
                <!-- Flash Alerts Handler -->
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show border-start border-4 border-success shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-start border-4 border-danger shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Dynamic Livewire Content Slot -->
                {{ $slot }}

            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @livewireScripts
</body>
</html>
