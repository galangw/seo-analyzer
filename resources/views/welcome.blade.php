<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'SEO Analyzer') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0ff4c6;         /* Neon teal */
            --secondary-color: #ff00a0;       /* Neon magenta */
            --accent-color: #7b00ff;          /* Purple */
            --dark-bg: #121212;               /* Nearly black */
            --dark-surface: #1e1e2f;          /* Dark blue-gray */
            --dark-card: #252538;             /* Slightly lighter blue-gray */
            --text-primary: #e0e0ff;          /* Light blue-white */
            --text-secondary: #a09fb1;        /* Muted lavender-gray */
            --grid-color: rgba(15, 244, 198, 0.1); /* Grid line color */
            --hover-glow: 0 0 8px var(--primary-color); /* Glow effect on hover */
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--dark-bg);
            font-family: 'Chakra Petch', sans-serif;
            color: var(--text-primary);
            background-image: 
                linear-gradient(rgba(15, 244, 198, 0.03) 1px, transparent 1px), 
                linear-gradient(90deg, rgba(15, 244, 198, 0.03) 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(123, 0, 255, 0.15), transparent 70%);
            pointer-events: none;
            z-index: -1;
        }

        .hero {
            padding: 120px 0 80px;
            position: relative;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary-color), var(--secondary-color), transparent);
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 10px rgba(15, 244, 198, 0.5);
            letter-spacing: 2px;
        }

        .hero p {
            font-size: 1.5rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 4px;
            padding: 12px 28px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: var(--dark-bg);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(15, 244, 198, 0.3);
            margin: 10px;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(15, 244, 198, 0.4);
            background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
            color: var(--dark-bg);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 4px;
            padding: 10px 26px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            margin: 10px;
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: var(--dark-bg);
            box-shadow: 0 0 15px var(--primary-color);
        }

        .features {
            padding: 80px 0;
            position: relative;
        }

        .feature-card {
            background: var(--dark-card);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid rgba(15, 244, 198, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3), var(--hover-glow);
        }

        .feature-card i {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .feature-card p {
            color: var(--text-secondary);
            margin-bottom: 0;
        }

        footer {
            background: var(--dark-surface);
            padding: 40px 0;
            border-top: 1px solid rgba(15, 244, 198, 0.2);
            text-align: center;
            position: relative;
        }

        footer p {
            color: var(--text-secondary);
            margin-bottom: 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 5px var(--primary-color);
            text-decoration: none;
            transition: all 0.3s;
        }

        .logo:hover {
            color: var(--secondary-color);
            text-shadow: 0 0 8px var(--secondary-color);
        }

        .nav-link {
            color: var(--text-primary);
            margin: 0 15px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .animated-gradient {
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle, var(--accent-color) 0%, transparent 70%);
            filter: blur(60px);
            opacity: 0.3;
            animation: pulse 6s infinite alternate;
            z-index: -1;
        }

        @keyframes pulse {
            0% {
                opacity: 0.2;
                transform: scale(0.8);
            }
            100% {
                opacity: 0.4;
                transform: scale(1.2);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero p {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="animated-gradient"></div>
    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">
            <a href="{{ url('/') }}" class="logo">SEO Analyzer</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    style="border-color: var(--primary-color); color: var(--primary-color)">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="nav-link">Register</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero text-center">
        <div class="container">
            <h1>SEO Analyzer</h1>
            <p>Optimize your content for search engines with our powerful AI-driven SEO analysis tool. Get instant feedback and improve your rankings.</p>
            <div>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Get Started</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline">Sign Up</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-search"></i>
                        <h3>Keyword Analysis</h3>
                        <p>Analyze your target keywords and get recommendations to optimize your content for better search engine rankings.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Performance Metrics</h3>
                        <p>Track your SEO performance over time with detailed analytics and actionable insights.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-file-alt"></i>
                        <h3>Content Optimization</h3>
                        <p>Get specific recommendations to improve your content's readability, structure, and keyword usage.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} SEO Analyzer. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
