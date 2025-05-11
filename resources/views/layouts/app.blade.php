<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SEO Tools') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <link href="/assets/style.css" rel="stylesheet">
    <style>
        /* Progress Circle Styles */
        .progress-circle {
            position: relative;
            height: 120px;
            width: 120px;
            border-radius: 50%;
            display: inline-block;
            background-color: #f0f0f0;
        }
        .progress-circle .progress-circle-left,
        .progress-circle .progress-circle-right {
            border-radius: 50%;
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            overflow: hidden;
        }
        .progress-circle .progress-circle-left { left: 0; }
        .progress-circle .progress-circle-right { right: 0; }
        .progress-circle .progress-circle-value {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bs-body-bg);
            border: 8px solid #f0f0f0;
        }
        .progress-circle .progress-circle-value div {
            font-size: 26px;
            font-weight: bold;
        }
        .progress-circle .progress-circle-value div span {
            font-size: 16px;
        }
        .progress-circle .progress-circle-bar {
            width: 100%;
            height: 100%;
            position: absolute;
            transform: rotate(0deg);
        }
        .progress-circle .progress-circle-left .progress-circle-bar {
            left: 100%;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            transform-origin: center left;
        }
        .progress-circle .progress-circle-right .progress-circle-bar {
            left: -100%;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            transform-origin: center right;
        }
        .progress-success .progress-circle-bar { background-color: var(--bs-success); }
        .progress-warning .progress-circle-bar { background-color: var(--bs-warning); }
        .progress-danger .progress-circle-bar { background-color: var(--bs-danger); }
    </style>
   @yield('head')
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        @if(Auth::check())
        <nav id="sidebar" class="sidebar py-3">
            <div class="px-3 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="m-0 text-primary fw-bold">SEO Tools</h4>
                    <div class="theme-controls">
                        <button id="navbarToggleBtn" class="navbar-toggle-btn" type="button" aria-label="Toggle Navigation">
                            <i class="bi bi-layout-sidebar"></i>
                        </button>
                        <div class="theme-toggle">
                            <i class="bi bi-sun-fill theme-icon" id="lightThemeIcon"></i>
                            <i class="bi bi-moon-fill theme-icon d-none" id="darkThemeIcon"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="nav flex-column">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Dashboard
                </a>
                <a href="{{ route('contents.create') }}" class="nav-link {{ request()->routeIs('contents.create') ? 'active' : '' }}">
                    <i class="bi bi-search"></i> SEO Meter
                </a>
                <a href="{{ route('contents.index') }}" class="nav-link {{ request()->routeIs('contents.index') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> My Content
                </a>
                <!-- <a href="{{ route('seo-results.index') }}" class="nav-link {{ request()->routeIs('seo-results.index') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> History
                </a> -->
                <!-- <a href="#" class="nav-link">
                    <i class="bi bi-gear"></i> Settings
                </a> -->
            </div>
            
            @if (Auth::check())
            <div class="sidebar-user mt-auto">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; cursor: pointer;" onclick="window.location.href='{{ route('profile.edit') }}'">
                        <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="ms-3">
                        <a href="{{ route('profile.edit') }}" class="text-primary text-decoration-none">
                            <p class="m-0 fw-semibold">{{ Auth::user()->name }}</p>
                        </a>
                        <a href="#" class="text-muted small text-decoration-none" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </nav>
        @endif
        <!-- Main Content -->
        <main id="content" class="content {{ !Auth::check() ? 'content-full' : '' }}">
            <!-- Fixed position toggle button that appears when sidebar is collapsed -->
            @if(Auth::check())
            <button id="fixedNavbarToggleBtn" class="fixed-toggle-btn" type="button" aria-label="Show Navigation">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
            @endif
            
            <!-- Toast Container for Notifications -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
                <div id="notificationToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            @if (session('success'))
                                {{ session('success') }}
                            @endif
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            @yield('content')
            
            
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Quill Editor -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://unpkg.com/quill-html-edit-button@2.2.7/dist/quill.htmlEditButton.min.js"></script>

    
    @yield('scripts')
    <script>
        // Pass Laravel variables to JavaScript
        window.appConfig = {
            userLoggedIn: {{ Auth::check() ? 'true' : 'false' }},
            hasSuccessMessage: {{ session('success') ? 'true' : 'false' }},
            successMessage: "{{ session('success') ?? '' }}"
        };
    </script>
    <script src="/assets/script.js"></script>
    
</body>

</html>
