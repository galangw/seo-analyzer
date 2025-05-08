@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </h5>
                        <a href="{{ route('contents.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Analyze New Content
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- 1. Total Content per User -->
                            <div class="col-md-6 ">
                                <div class="p-4 rounded text-center border " >
                                    <i class="bi bi-file-earmark-text display-4 text-primary mb-3"></i>
                                    <h3 class="fs-4">{{ $totalContent }}</h3>
                                    <p class="text-body-secondary">Total Content</p>
                                </div>
                            </div>
                            
                            <!-- 2. Average Overall Score -->
                            <div class="col-md-6">
                                <div class="p-4 rounded text-center border">
                                    <i class="bi bi-graph-up display-4 text-primary mb-3"></i>
                                    @php
                                        $scoreClass = 
                                            $averageScore >= 80 
                                                ? 'success' 
                                                : ($averageScore >= 50 
                                                    ? 'warning' 
                                                    : 'danger');
                                    @endphp
                                    <h3 class="fs-4 text-{{ $scoreClass }}">{{ $averageScore }}%</h3>
                                    <p class="text-body-secondary">Average SEO Score</p>
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 0. Graph/Line Diagram Section -->
        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-graph-up me-2"></i>Content Activity (Last 7 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="contentActivityChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two-column layout for keywords and recent content -->
        <div class="row g-4 mt-1">
            <!-- 3. Top Target Keywords Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-tags me-2"></i>Top Target Keywords
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($topKeywords) > 0)
                            <div class="d-flex flex-column gap-2">
                                @foreach($topKeywords as $keyword)
                                    <div class="p-2 border rounded">
                                        <a class="text-decoration-none " href="/contents?search={{ $keyword['word'] }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-medium"> {{ $keyword['word'] }}</span>
                                            <span class="badge bg-primary rounded-pill text-dark">{{ $keyword['count'] }}</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            @php
                                                $maxCount = $topKeywords[0]['count'];
                                                $percentage = ($keyword['count'] / $maxCount) * 100;
                                            @endphp
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: {{ $percentage }}%" 
                                                aria-valuenow="{{ $percentage }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100"></div>
                                        </div></a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-4">
                                <i class="bi bi-tag display-4 text-body-secondary mb-3"></i>
                                <p>No keywords data available yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Content Table -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-clock-history me-2"></i>Recent Content
                        </h5>
                        <a href="{{ route('contents.index') }}" class="btn btn-outline-primary btn-sm">
                            View All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($recentContents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Target Keyword</th>
                                            <th>SEO Score</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentContents as $content)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                                        <span>{{ Str::limit($content->title, 50) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge text-primary border ">
                                                        {{ Str::limit($content->target_keyword, 10) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($content->latestSeoResult)
                                                        @php
                                                            $score = $content->latestSeoResult->overall_score;
                                                            $scoreClass =
                                                                $score >= 80
                                                                    ? 'success'
                                                                    : ($score >= 50
                                                                        ? 'warning'
                                                                        : 'danger');
                                                        @endphp
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                <div class="progress-bar bg-{{ $scoreClass }}"
                                                                    role="progressbar" style="width: {{ $score }}%"
                                                                    aria-valuenow="{{ $score }}" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                            <span class="badge bg-{{ $scoreClass }}">
                                                                {{ number_format($score, 1) }}%
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary">Not analyzed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-body-secondary">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ $content->created_at->diffForHumans() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if ($content->latestSeoResult)
                                                            <a href="{{ route('seo-results.show', $content->latestSeoResult->id) }}"
                                                                class="btn btn-sm btn-outline-info">
                                                                <i class="bi bi-graph-up me-1"></i> Results
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('contents.edit', $content->id) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center p-5">
                                <i class="bi bi-file-earmark-plus display-1 text-body-secondary mb-3"></i>
                                <p>You haven't analyzed any content yet.</p>
                                <a href="{{ route('contents.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-lg me-1"></i> Analyze Your First Content
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

   
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup chart theme based on current theme
            let chartTextColor = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8f9fa' : '#212529';
            let chartGridColor = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            
            // Set up chart data
            const contentData = @json($dailyContentStats);
            
            const dates = contentData.map(item => item.date);
            const contentCounts = contentData.map(item => item.content_count);
            const analyzedCounts = contentData.map(item => item.analyzed_count);
            
            // Initialize chart
            const ctx = document.getElementById('contentActivityChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Contents Created',
                            data: contentCounts,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Contents Analyzed',
                            data: analyzedCounts,
                            borderColor: '#20c997',
                            backgroundColor: 'rgba(32, 201, 151, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: chartTextColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: chartTextColor
                            },
                            grid: {
                                color: chartGridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: chartTextColor
                            },
                            grid: {
                                color: chartGridColor
                            }
                        }
                    }
                }
            });
            
            // Update chart colors when theme changes
            document.querySelector('.theme-toggle').addEventListener('click', function() {
                setTimeout(() => {
                    const chart = Chart.getChart(ctx);
                    let newTextColor = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8f9fa' : '#212529';
                    let newGridColor = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                    
                    chart.options.plugins.legend.labels.color = newTextColor;
                    chart.options.scales.y.ticks.color = newTextColor;
                    chart.options.scales.x.ticks.color = newTextColor;
                    chart.options.scales.y.grid.color = newGridColor;
                    chart.options.scales.x.grid.color = newGridColor;
                    
                    chart.update();
                }, 100);
            });
        });
    </script>
    @endsection