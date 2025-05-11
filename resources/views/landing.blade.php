<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEO Tools - Optimize Your Content</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <!-- Outfit Font -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- AOS Animation Library -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/landing.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#" data-aos="fade-right" data-aos-duration="800">
        <span><i class="fas fa-chart-line"></i></span> SEO Tools
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item" data-aos="fade-down" data-aos-duration="600" data-aos-delay="100">
            <a class="nav-link" href="#features">Features</a>
          </li>
          <li class="nav-item" data-aos="fade-down" data-aos-duration="600" data-aos-delay="200">
            <a class="nav-link" href="#dashboard">Dashboard</a>
          </li>
          <li class="nav-item" data-aos="fade-down" data-aos-duration="600" data-aos-delay="300">
            <a class="nav-link" href="#how-it-works">How It Works</a>
          </li>
          <li class="nav-item ms-lg-3" data-aos="fade-left" data-aos-duration="800" data-aos-delay="400">
            <a class="btn-outline" href="{{ route('login') }}">
              <span class="d-inline-block me-2"><i class="fas fa-sign-in-alt"></i></span>
              Log In
            </a>
          </li>
          <li class="nav-item ms-2" data-aos="fade-left" data-aos-duration="800" data-aos-delay="500">
            <a class="btn btn-primary" href="{{ route('register') }}">
              <span class="d-inline-block me-2"><i class="fas fa-rocket"></i></span>
              Try Free
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
          <div class="position-relative">
            <span class="position-absolute" style="left: -30px; top: -30px; font-size: 5rem; opacity: 0.1; color: var(--primary);">
              <i class="fas fa-chart-line"></i>
            </span>
            <h1 class="mb-4">Boost Your Content's SEO <span class="text-gradient">100% Free</span></h1>
            <p>All-in-one SEO solution with AI-powered content optimization, structure suggestions, intelligent title and meta description generation, and performance tracking.</p>
            <div class="mt-5" data-aos="fade-up" data-aos-delay="300">
              <a href="#get-started" class="btn btn-primary me-3">
                <span class="d-inline-block me-2"><i class="fas fa-rocket"></i></span>
                Start For Free
              </a>
              <!-- <a href="#demo" class="btn-outline">
                <span class="d-inline-block me-2"><i class="fas fa-play"></i></span>
                See Demo
              </a> -->
            </div>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
          <div class="hero-img floating position-relative">
            <!-- Small decorative elements -->
            <div class="position-absolute" style="top: -15px; right: 30px; z-index: 3; animation: floating 4s ease-in-out 1s infinite;">
              <div style="width: 60px; height: 60px; background: linear-gradient(45deg, var(--gradient-start), var(--gradient-mid)); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
                <i class="fas fa-search text-light fa-lg"></i>
              </div>
            </div>
            <div class="position-absolute" style="bottom: 10px; left: -20px; z-index: 3; animation: floating 4s ease-in-out 0.5s infinite;">
              <div style="width: 70px; height: 70px; background: linear-gradient(45deg, var(--gradient-mid), var(--gradient-end)); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
                <i class="fas fa-chart-bar text-light fa-lg"></i>
              </div>
            </div>
            <img src="{{ asset('images/dashboard.jpg') }}" alt="SEO Dashboard" class="img-fluid">
          </div>
        </div>
      </div>
    </div>

    <!-- Animated shapes -->
    <div class="position-absolute" style="bottom: 10%; right: 5%; z-index: 0; width: 150px; height: 150px; border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; background: linear-gradient(45deg, rgba(0, 230, 181, 0.05), rgba(147, 51, 234, 0.05)); animation: morph 15s linear infinite alternate;">
    </div>
    <div class="position-absolute" style="top: 15%; left: 5%; z-index: 0; width: 180px; height: 180px; border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; background: linear-gradient(45deg, rgba(147, 51, 234, 0.05), rgba(255, 61, 113, 0.05)); animation: morph 18s linear 1s infinite alternate;">
    </div>
  </section>

  <style>
    .text-gradient {
      background: linear-gradient(90deg, var(--gradient-start), var(--gradient-mid));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    @keyframes morph {
      0% {
        border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
      }
      50% {
        border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%;
      }
      100% {
        border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
      }
    }
  </style>

  <div class="gradient-line"></div>

  <!-- Features Section -->
  <section class="features" id="features">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>Powerful Features</h2>
        <p>Our comprehensive SEO toolkit helps optimize your content with AI-powered analysis and recommendations.</p>
      </div>

      <div class="row">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="feature-card ">
            <div class="feature-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <h3>Advanced SEO Analysis</h3>
            <p>Get detailed SEO scoring and analysis of your content with actionable recommendations to improve rankings.</p>
          </div>
        </div>

        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-keyboard"></i>
            </div>
            <h3>AI Title Suggestions</h3>
            <p>Generate SEO-optimized titles for your content with our advanced AI technology to increase click-through rates.</p>
          </div>
        </div>

        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-file-alt"></i>
            </div>
            <h3>Content Structure Suggestions</h3>
            <p>Receive smart recommendations to improve your content's structure, readability, and user engagement.</p>
          </div>
        </div>

        <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Content Production Tracking</h3>
            <p>Monitor your content creation progress over time with detailed analytics and performance metrics.</p>
          </div>
        </div>

        <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-tags"></i>
            </div>
            <h3>AI Meta Description Generator</h3>
            <p>Create compelling meta descriptions with AI assistance to improve your search result appearance and CTR.</p>
          </div>
        </div>

        <div class="col-md-4" data-aos="fade-up" data-aos-delay="600">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-trophy"></i>
            </div>
            <h3>Keyword Optimization</h3>
            <p>Analyze and optimize your target keywords for better content relevance and search engine visibility.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Dashboard Preview Section -->
  <section class="dashboard-preview" id="dashboard">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>Intuitive Dashboard</h2>
        <p>Our user-friendly dashboard helps you track all your content performance and SEO metrics in one place.</p>
      </div>

      <div class="row align-items-center">
        <div class="col-lg-7" data-aos="fade-right" data-aos-duration="1000">
          <div class="dashboard-image">
            <!-- Animated dashboard elements -->
            <div class="position-absolute" style="top: -25px; left: 50%; transform: translateX(-50%); z-index: 2;">
              <div style="padding: 10px 20px; background: rgba(0, 0, 0, 0.8); border-radius: 30px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); display: flex; align-items: center; gap: 10px; animation: bounce 3s ease-in-out infinite;">
                <i class="fas fa-bell text-warning"></i>
                <span style="color: white; font-size: 0.9rem; font-weight: 500;">SEO score updated!</span>
              </div>
            </div>

            <img src="{{ asset('images/dashboard.jpg') }}" alt="SEO Dashboard" class="img-fluid">

            <!-- Cursor pointer animation -->
            <div class="position-absolute" style="bottom: 30%; right: 20%; z-index: 2; animation: cursorMove 8s ease-in-out infinite;">
              <i class="fas fa-mouse-pointer" style="color: white; font-size: 1.5rem; text-shadow: 0 0 10px rgba(0, 0, 0, 0.7);"></i>
            </div>

            <!-- Highlight circle -->
            <div class="position-absolute" style="bottom: 25%; right: 18%; width: 50px; height: 50px; border: 2px dashed var(--primary); border-radius: 50%; z-index: 1; opacity: 0.7; animation: pulse 2s ease-in-out infinite;"></div>
          </div>
        </div>

        <div class="col-lg-5" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
          <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
            <div class="d-flex align-items-center">
              <div class="me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(0, 230, 181, 0.1), rgba(147, 51, 234, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-search-plus" style="color: var(--primary);"></i>
              </div>
              <div>
                <h3 class="mb-0">100%</h3>
                <p>Increase in Search Visibility</p>
              </div>
            </div>
            <!-- Animated progress bar -->
            <div class="mt-3 position-relative" style="height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 3px; overflow: hidden;">
              <div class="position-absolute h-100" style="width: 100%; background: linear-gradient(90deg, var(--gradient-start), var(--gradient-mid)); border-radius: 3px; animation: progressLoad 3s ease-out;">
              </div>
            </div>
          </div>

          <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
            <div class="d-flex align-items-center">
              <div class="me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(0, 230, 181, 0.1), rgba(147, 51, 234, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="color: var(--primary);"></i>
              </div>
              <div>
                <h3 class="mb-0">50+</h3>
                <p>SEO Metrics Tracked</p>
              </div>
            </div>
            <!-- Animated metric icons -->
            <div class="mt-3 d-flex justify-content-between align-items-center">
              <div style="animation: popup 2s ease-in-out 0.5s infinite;">
                <i class="fas fa-link" style="color: var(--text-gray);"></i>
              </div>
              <div style="animation: popup 2s ease-in-out 1s infinite;">
                <i class="fas fa-globe" style="color: var(--text-gray);"></i>
              </div>
              <div style="animation: popup 2s ease-in-out 1.5s infinite;">
                <i class="fas fa-tachometer-alt" style="color: var(--text-gray);"></i>
              </div>
              <div style="animation: popup 2s ease-in-out 2s infinite;">
                <i class="fas fa-mobile-alt" style="color: var(--text-gray);"></i>
              </div>
              <div style="animation: popup 2s ease-in-out 2.5s infinite;">
                <i class="fas fa-users" style="color: var(--text-gray);"></i>
              </div>
            </div>
          </div>

          <div class="stat-card" data-aos="fade-up" data-aos-delay="500">
            <div class="d-flex align-items-center">
              <div class="me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(0, 230, 181, 0.1), rgba(147, 51, 234, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: var(--primary);"></i>
              </div>
              <div>
                <h3 class="mb-0">24/7</h3>
                <p>Real-time Performance Monitoring</p>
              </div>
            </div>
            <!-- Live update animation -->
            <div class="mt-3 d-flex align-items-center">
              <div class="me-2" style="width: 8px; height: 8px; background-color: #4CAF50; border-radius: 50%; animation: blink 1.5s ease infinite;"></div>
              <span style="color: var(--text-gray); font-size: 0.9rem;">Live monitoring active</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <style>
    @keyframes progressLoad {
      0% { width: 0; }
      100% { width: 100%; }
    }

    @keyframes blink {
      0% { opacity: 0.4; }
      50% { opacity: 1; }
      100% { opacity: 0.4; }
    }

    @keyframes popup {
      0% { transform: scale(1); }
      50% { transform: scale(1.3); }
      100% { transform: scale(1); }
    }

    @keyframes bounce {
      0% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0); }
    }

    @keyframes cursorMove {
      0% { transform: translate(0, 0); }
      25% { transform: translate(-50px, 30px); }
      50% { transform: translate(30px, -20px); }
      75% { transform: translate(-20px, -40px); }
      100% { transform: translate(0, 0); }
    }
  </style>

  <div class="gradient-line"></div>

  <!-- How It Works Section -->
  <section class="how-it-works" id="how-it-works">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>How It Works</h2>
        <p>Optimize your content in just a few simple steps with our AI-powered SEO tools.</p>
      </div>

      <div class="row">
        <div class="col-lg-6 offset-lg-3">
          <div class="step" data-aos="fade-up" data-aos-delay="100">
            <div class="step-number">1</div>
            <div class="step-content">
              <h3>Add Your Content</h3>
              <p>Input your content or provide a URL to analyze your content's SEO performance.</p>
            </div>
          </div>

          <div class="step" data-aos="fade-up" data-aos-delay="200">
            <div class="step-number">2</div>
            <div class="step-content">
              <h3>Get AI-Powered Analysis</h3>
              <p>Receive comprehensive SEO analysis, content structure recommendations, and AI-generated title and meta description suggestions.</p>
            </div>
          </div>

          <div class="step" data-aos="fade-up" data-aos-delay="300">
            <div class="step-number">3</div>
            <div class="step-content">
              <h3>Implement Recommendations</h3>
              <p>Apply the AI-suggested improvements to enhance your content's SEO performance and readability.</p>
            </div>
          </div>

          <div class="step" data-aos="fade-up" data-aos-delay="400">
            <div class="step-number">4</div>
            <div class="step-content">
              <h3>Track Your Progress</h3>
              <p>Monitor your content creation and optimization performance over time with our detailed analytics dashboard.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="features" id="pricing">
    <div class="container">
      <div class="section-title" data-aos="fade-up">
        <h2>100% Free - No Hidden Costs</h2>
        <p>All our powerful SEO tools are available to you completely free of charge.</p>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="feature-card text-center" style="transform: scale(1.05); border: 2px solid var(--primary);">
            <h3>Complete Access</h3>
            <div class="my-4">
              <span style="font-size: 3rem; font-weight: 700;">$0</span>
              <p class="text-gray">Always Free</p>
            </div>
            <ul class="list-unstyled text-start mb-4">
              <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Advanced SEO Analysis</li>
              <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> AI Title Suggestions</li>
              <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Content Structure Recommendations</li>
              <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> AI Meta Description Generator</li>
              <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Content Production Tracking</li>
              <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Unlimited Content Analysis</li>
            </ul>
            <a href="{{ route('register') }}" class="btn btn-primary w-100">Get Started</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta" id="get-started" data-aos="fade-up">
    <div class="container">
      <h2>Start Optimizing Your Content Today - 100% Free</h2>
      <p>Get access to all our AI-powered SEO tools and start improving your content's performance right away.</p>
      <a href="{{ route('register') }}" class="btn btn-primary">Get Started Free</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="footer-logo"><span><i class="fas fa-chart-line"></i></span> SEO Tools</div>
          <p class="footer-text">Free AI-powered SEO tools to optimize your content and improve your search rankings.</p>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 mt-4 mt-lg-0">
          <div class="footer-links">
            <h4>Features</h4>
            <ul>
              <li><a href="#features">SEO Analysis</a></li>
              <li><a href="#features">AI Title Generator</a></li>
              <li><a href="#features">Meta Description AI</a></li>
              <li><a href="#features">Content Structure</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 mt-4 mt-lg-0">
          <div class="footer-links">
            <h4>Resources</h4>
            <ul>
              <li><a href="#">Blog</a></li>
              <li><a href="#">SEO Guides</a></li>
              <li><a href="#">Help Center</a></li>
              <li><a href="#">Contact</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-4 col-md-4 mt-4 mt-lg-0">
          <div class="footer-links">
            <h4>Get Started Now - 100% Free</h4>
            <p class="footer-text">Create an account today and start optimizing your content with our AI-powered tools.</p>
            <div class="mt-3">
              <a href="{{ route('register') }}" class="btn btn-primary">Sign Up Free</a>
            </div>
          </div>
        </div>
      </div>

      <div class="copyright">
        <p>&copy; {{ date('Y') }} SEO Tools. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <!-- AOS Animation Library -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    // Initialize AOS
    AOS.init({
      duration: 800,
      once: true
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  </script>
</body>
</html>