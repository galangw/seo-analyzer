<!DOCTYPE html>
<html>

<head>
    <title>{{ config('app.name', 'SEO Tools') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: "Lexend", sans-serif;
        }

        .wrapper {
            display: flex;
            width: 100%;
        }

        /* Sidebar Style */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 999;
            background: #4a90e2;
            color: #fff;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 15px 25px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background: #357abd;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Style */
        .content {
            width: calc(100% - 250px);
            margin-left: 250px;
            padding: 20px;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #4a90e2;
        }

        /* History Table */
        .history-table {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #4a90e2;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }

        .btn-primary {
            background-color: #4a90e2;
            border-color: #4a90e2;
        }

        .btn-primary:hover {
            background-color: #357abd;
            border-color: #357abd;
            transform: translateY(-2px);
        }

        /* Quill Editor */
        .ql-toolbar {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .ql-container {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
            border-color: #dee2e6;
            font-family: inherit;
            min-height: 400px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .content {
                width: 100%;
                margin-left: 0;
            }

            .content.active {
                margin-left: 250px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h4>SEO Tools</h4>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('contents.create') }}"
                        class="nav-link {{ request()->routeIs('contents.create') ? 'active' : '' }}">
                        <i class="fas fa-search"></i>
                        SEO Meter
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('contents.index') }}"
                        class="nav-link {{ request()->routeIs('contents.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        My Content
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('seo-results.index') }}"
                        class="nav-link {{ request()->routeIs('seo-results.index') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        History
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
                @if (Auth::check())
                    <li class="nav-item mt-auto">
                        <a href="#" class="nav-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                @endif
            </ul>
        </nav>

        <!-- Content -->
        <div class="content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    @yield('scripts')
</body>

</html>
