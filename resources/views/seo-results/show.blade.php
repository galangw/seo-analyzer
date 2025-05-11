@extends('layouts.app')

@section('content')
    <style>
        /* Score display styles */
        .score-container {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .score-value-display {
            margin-bottom: 15px;
            position: relative;
        }
        
        .score-number {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1;
            transition: all 0.5s ease;
        }
        
        .score-danger {
            background: linear-gradient(90deg, #dc3545, #f86032);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .score-warning {
            background: linear-gradient(90deg, #ffc107, #fd7e14);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .score-success {
            background: linear-gradient(90deg, #20c997, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .score-number.active {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 0.8; }
            50% { opacity: 1; }
            100% { opacity: 0.8; }
        }
        
        .score-percent {
            font-size: 1.5rem;
            font-weight: 600;
            color: #6c757d;
            margin-left: 5px;
        }
        
        .score-progress {
            height: 12px;
            border-radius: 10px;
            background-color: #e9ecef;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }
        
        .score-progress .progress-bar {
            border-radius: 10px;
            position: relative;
            transition: width 1.5s cubic-bezier(0.09, 0.41, 0.41, 0.95);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .score-progress .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0.1) 100%);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
    <div class="container">
        @if(!isset($seoResult->detail_score) || !is_array($seoResult->detail_score))
            <div class="alert alert-danger">
                <strong>Error:</strong> Invalid SEO result data structure. Please reanalyze this content.
                <form action="{{ route('seo-results.reanalyze', $content->id) }}" method="POST" class="d-inline mt-2">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">Reanalyze Now</button>
                </form>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-body d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>SEO Analysis Results</h5>
                        <div>
                            <a href="{{ route('contents.edit', $content->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-1"></i> Edit Content
                            </a>
                            <!-- <form action="{{ route('seo-results.reanalyze', $content->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-repeat me-1"></i> Reanalyze
                                </button>
                            </form> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="mb-3">{{ $content->title }}</h5>
                                <p class="text-muted mb-2"><i class="bi bi-tag me-1"></i> Target Keyword: <strong>{{ $content->target_keyword }}</strong></p>
                                <p class="mb-0"><i class="bi bi-calendar3 me-1"></i> Analysis Date: {{ $seoResult->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column align-items-center">
                                    <h6 class="mb-2 fw-bold text-center">Overall SEO Score</h6>
                                    @php
                                        $score = $seoResult->overall_score;
                                        $ratingClass = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                        $ratingText = $score >= 80 ? 'Good' : ($score >= 50 ? 'Need Improvement' : 'Poor');
                                    @endphp
                                    <div class="score-container">
                                        <div class="score-value-display">
                                            <span class="score-number score-{{ $ratingClass }}">{{ round($score) }}</span>
                                            <span class="score-percent">%</span>
                                        </div>
                                        <div class="progress score-progress">
                                            <div class="progress-bar bg-{{ $ratingClass }}" role="progressbar" 
                                                style="width: {{ round($score) }}%" 
                                                aria-valuenow="{{ round($score) }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <p class="mt-2 fw-bold">
                                            <span class="badge bg-{{ $ratingClass }}">{{ $ratingText }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-body">
                        <h6 class="mb-0 fw-bold">Score Summary</h6>
                                </div>
                                <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                    @php
                                        $titleScore = $seoResult->page_title_score;
                                    $titleClass = $titleScore >= 80 ? 'success' : ($titleScore >= 50 ? 'warning' : 'danger');
                                    $titleWeight = 20;
                                    @endphp
                                <div class="card border-0 h-100 bg-body">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Page Title</h6>
                                            <span class="badge bg-primary text-dark">{{ $titleWeight }}%</span>
                                        </div>
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $titleClass }}" role="progressbar"
                                            style="width: {{ $titleScore }}%" aria-valuenow="{{ $titleScore }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                        <h5 class="text-{{ $titleClass }}">{{ number_format($titleScore, 1) }}%</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                    @php
                                        $metaScore = $seoResult->meta_description_score;
                                    $metaClass = $metaScore >= 80 ? 'success' : ($metaScore >= 50 ? 'warning' : 'danger');
                                    $metaWeight = 5;
                                    @endphp
                                <div class="card border-0 h-100 bg-body">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Meta Description</h6>
                                            <span class="badge bg-primary text-dark">{{ $metaWeight }}%</span>
                                        </div>
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $metaClass }}" role="progressbar"
                                            style="width: {{ $metaScore }}%" aria-valuenow="{{ $metaScore }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                        <h5 class="text-{{ $metaClass }}">{{ number_format($metaScore, 1) }}%</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                    @php
                                        $contentScore = $seoResult->content_score;
                                    $contentClass = $contentScore >= 80 ? 'success' : ($contentScore >= 50 ? 'warning' : 'danger');
                                    $contentWeight = 75;
                                    @endphp
                                <div class="card border-0 h-100 bg-body">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Content</h6>
                                            <span class="badge bg-primary text-dark">{{ $contentWeight }}%</span>
                                        </div>
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $contentClass }}" role="progressbar"
                                            style="width: {{ $contentScore }}%" aria-valuenow="{{ $contentScore }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                        <h5 class="text-{{ $contentClass }}">{{ number_format($contentScore, 1) }}%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-body">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-type-h1 me-2"></i>Page Title Analysis (20%)</h6>
                    </div>
                    <div class="card-body">
                        <h6>Title:</h6>
                        <p class="border p-2 rounded bg-body">{{ $content->title }}</p>
                        <p class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            <span class="{{ strlen($content->title) >= 75 && strlen($content->title) <= 95 ? 'text-success' :
                                          (strlen($content->title) >= 40 && strlen($content->title) <= 120 ? 'text-warning' : 'text-danger') }}">
                                {{ strlen($content->title) }} characters
                            </span>
                            (optimal: 75-95 characters)
                        </p>

                        <h6 class="mt-4">Title Score Breakdown:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Weight</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $titleCriteriaDisplayed = [];
                                    @endphp

                                    @if(isset($seoResult->detail_score['page_title']['details']) && is_array($seoResult->detail_score['page_title']['details']))
                                        @foreach ($seoResult->detail_score['page_title']['details'] as $key => $detail)
                                            @if(is_array($detail) && isset($detail['score']))
                                                @php
                                                    // Determine criteria name correctly
                                                    $criteriaKey = '';
                                                    if ($key == 'keyword_in_title' || $key == 'keyword_exists') {
                                                        $criteriaKey = 'keyword';
                                                        $criteriaName = 'Keyword in Title';
                                                    } else {
                                                        $criteriaKey = 'length';
                                                        $criteriaName = 'Title Length';
                                                    }

                                                    // Skip if we already displayed this type of criteria
                                                    if (in_array($criteriaKey, $titleCriteriaDisplayed)) {
                                                        continue;
                                                    }
                                                    $titleCriteriaDisplayed[] = $criteriaKey;

                                                    $detailClass = $detail['score'] >= 0.8 ? 'success' : ($detail['score'] >= 0.5 ? 'warning' : 'danger');
                                                    $criteriaWeight = isset($detail['weight']) ? ($detail['weight'] * 100) : ($criteriaKey == 'keyword' ? 70 : 30);
                                                    $criteriaStatus = $detail['score'] >= 0.8 ?
                                                        '<i class="bi bi-check-circle-fill text-success"></i> Good' :
                                                        ($detail['score'] >= 0.5 ?
                                                            '<i class="bi bi-exclamation-triangle-fill text-warning"></i> Needs Improvement' :
                                                            '<i class="bi bi-x-circle-fill text-danger"></i> Poor');
                                                @endphp
                                                <tr>
                                                    <td>{{ $criteriaName }}</td>
                                                    <td>{{ number_format($criteriaWeight, 0) }}%</td>
                                                    <td>
                                                        <div class="progress" style="height: 8px; width: 80px;">
                                                            <div class="progress-bar bg-{{ $detailClass }}" role="progressbar"
                                                                style="width: {{ $detail['score'] * 100 }}%"
                                                                aria-valuenow="{{ $detail['score'] * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </td>
                                                    <td>{!! $criteriaStatus !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">No detailed criteria available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-body">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-card-text me-2"></i>Meta Description Analysis (5%)</h6>
                    </div>
                    <div class="card-body">
                        <h6>Meta Description:</h6>
                        <p class="border p-2 rounded bg-body">{{ $content->meta_description }}</p>
                        <p class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            <span class="{{ strlen($content->meta_description) >= 146 && strlen($content->meta_description) <= 160 ? 'text-success' :
                                          (strlen($content->meta_description) >= 100 && strlen($content->meta_description) < 146 ? 'text-warning' : 'text-danger') }}">
                                {{ strlen($content->meta_description) }} characters
                            </span>
                            (optimal: 146-160 characters)
                        </p>

                        <h6 class="mt-4">Meta Description Score Breakdown:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Weight</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $metaCriteriaDisplayed = [];
                                    @endphp

                                    @if(isset($seoResult->detail_score['meta_description']['details']) && is_array($seoResult->detail_score['meta_description']['details']))
                                        @foreach ($seoResult->detail_score['meta_description']['details'] as $key => $detail)
                                            @if(is_array($detail) && isset($detail['score']))
                                                @php
                                                    // Determine criteria name correctly
                                                    $criteriaKey = '';
                                                    if ($key == 'keyword_in_meta' || $key == 'keyword_exists') {
                                                        $criteriaKey = 'keyword';
                                                        $criteriaName = 'Keyword in Meta Description';
                                                    } else {
                                                        $criteriaKey = 'length';
                                                        $criteriaName = 'Meta Description Length';
                                                    }

                                                    // Skip if we already displayed this type of criteria
                                                    if (in_array($criteriaKey, $metaCriteriaDisplayed)) {
                                                        continue;
                                                    }
                                                    $metaCriteriaDisplayed[] = $criteriaKey;

                                                    $detailClass = $detail['score'] >= 0.8 ? 'success' : ($detail['score'] >= 0.5 ? 'warning' : 'danger');
                                                    $criteriaWeight = isset($detail['weight']) ? ($detail['weight'] * 100) : ($criteriaKey == 'keyword' ? 50 : 50);
                                                    $criteriaStatus = $detail['score'] >= 0.8 ?
                                                        '<i class="bi bi-check-circle-fill text-success"></i> Good' :
                                                        ($detail['score'] >= 0.5 ?
                                                            '<i class="bi bi-exclamation-triangle-fill text-warning"></i> Needs Improvement' :
                                                            '<i class="bi bi-x-circle-fill text-danger"></i> Poor');
                                                @endphp
                                                <tr>
                                                    <td>{{ $criteriaName }}</td>
                                                    <td>{{ number_format($criteriaWeight, 0) }}%</td>
                                                    <td>
                                                        <div class="progress" style="height: 8px; width: 80px;">
                                                            <div class="progress-bar bg-{{ $detailClass }}" role="progressbar"
                                                                style="width: {{ $detail['score'] * 100 }}%"
                                                                aria-valuenow="{{ $detail['score'] * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </td>
                                                    <td>{!! $criteriaStatus !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">No detailed criteria available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-body">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-file-text me-2"></i>Content Analysis (75%)</h6>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Content Score Breakdown:</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Description</th>
                                        <th style="width: 15%;">Weight</th>
                                        <th style="width: 15%;">Score</th>
                                        <th style="width: 20%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                            @if(isset($seoResult->detail_score['content']['details']) && is_array($seoResult->detail_score['content']['details']))
                                                @foreach ($seoResult->detail_score['content']['details'] as $key => $detail)
                                                    @if(is_array($detail) && isset($detail['score']))
                                                        @php
                                                    $detailClass = $detail['score'] >= 0.8 ? 'success' : ($detail['score'] >= 0.5 ? 'warning' : 'danger');
                                                    $criteriaNames = [
                                                        'word_count' => 'Word Count',
                                                        'keyword_first_paragraph' => 'Keyword in First Paragraph',
                                                        'keyword_last_paragraph' => 'Keyword in Last Paragraph',
                                                        'keyword_in_img_alt' => 'Keyword in Image Alt',
                                                        'internal_links' => 'Internal Links',
                                                        'keyword_density' => 'Keyword Density'
                                                    ];
                                                    $criteriaName = $criteriaNames[$key] ?? ucfirst(str_replace('_', ' ', $key));

                                                    // Default weights for content analysis criteria if not provided
                                                    $defaultWeights = [
                                                        'word_count' => 30,
                                                        'keyword_first_paragraph' => 15,
                                                        'keyword_last_paragraph' => 15,
                                                        'keyword_in_img_alt' => 10,
                                                        'internal_links' => 15,
                                                        'keyword_density' => 15
                                                    ];

                                                    $criteriaWeight = isset($detail['weight']) ? ($detail['weight'] * 100) : ($defaultWeights[$key] ?? 10);
                                                    $criteriaStatus = $detail['score'] >= 0.8 ?
                                                        '<span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Good</span>' :
                                                        ($detail['score'] >= 0.5 ?
                                                            '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i> Needs Improvement</span>' :
                                                            '<span class="badge bg-danger"><i class="bi bi-x-lg me-1"></i> Poor</span>');
                                                        @endphp
                                                <tr>
                                                    <td><strong>{{ $criteriaName }}</strong></td>
                                                    <td>
                                                        @if(isset($detail['description']))
                                                            {{ $detail['description'] }}
                                                        @elseif(isset($detail['actual']) && isset($detail['recommended']))
                                                            Current: {{ $detail['actual'] }}<br>
                                                            Recommended: {{ $detail['recommended'] }}
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($criteriaWeight, 0) }}%</td>
                                                    <td>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-{{ $detailClass }}" role="progressbar"
                                                                style="width: {{ $detail['score'] * 100 }}%"
                                                                aria-valuenow="{{ $detail['score'] * 100 }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="d-block text-center mt-1">{{ number_format($detail['score'] * 100, 0) }}%</small>
                                                    </td>
                                                    <td class="text-center">{!! $criteriaStatus !!}</td>
                                                </tr>
                                                    @endif
                                                @endforeach
                                            @else
                                        <tr>
                                            <td colspan="5">No detailed criteria available</td>
                                        </tr>
                                            @endif
                                </tbody>
                            </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-body d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb me-2"></i>Recommendations to Improve Your Score</h6>
                                </div>
                                <div class="card-body">
                                    @if (isset($seoResult->detail_score['recommendations']) && is_array($seoResult->detail_score['recommendations']) && count($seoResult->detail_score['recommendations']) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Section</th>
                                                        <th>Criteria</th>
                                                        <th>Recommendation</th>
                                                        <th>Current vs Recommended</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($seoResult->detail_score['recommendations'] as $recommendation)
                                                        @if(is_array($recommendation))
                                                        <tr>
                                                <td>
                                                    <span class="badge bg-{{
                                                        $recommendation['section'] == 'Page Title' ? 'primary text-dark' :
                                                        ($recommendation['section'] == 'Meta Description' ? 'info' : 'secondary')
                                                    }}">
                                                        {{ $recommendation['section'] ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                            <td>{{ $recommendation['criteria'] ?? 'Unknown' }}</td>
                                                            <td>{{ $recommendation['description'] ?? 'No description available' }}</td>
                                                            <td>
                                                                @if (!empty($recommendation['actual']))
                                                        <small class="d-block text-muted">{{ $recommendation['actual'] }}</small>
                                                                @endif
                                                                @if (!empty($recommendation['recommended']))
                                                        <small class="d-block text-success">{{ $recommendation['recommended'] }}</small>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-success">
                                <i class="bi bi-trophy me-2"></i> Congratulations! Your content doesn't have any major SEO issues.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                                </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate the progress bars
    setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            // Start with width 0
            bar.style.width = '0%';
            // Force reflow
            void bar.offsetWidth;
            // Animate to actual value
            bar.style.width = bar.getAttribute('aria-valuenow') + '%';
        });
        
        // Add active class to score number for animation
        const scoreNumbers = document.querySelectorAll('.score-number');
        scoreNumbers.forEach(number => {
            // Add active class to trigger animation
            number.classList.add('active');
        });
    }, 100);
});
</script>
                @endsection
