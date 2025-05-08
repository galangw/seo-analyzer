@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i>SEO Analysis History</h5>
                        <a href="{{ route('contents.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> New Analysis
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($seoResults->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Content Title</th>
                                            <th style="width: 20%">Overall Score</th>
                                            <th>Title</th>
                                            <th>Meta</th>
                                            <th>Content</th>
                                            <th>Analyzed On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($seoResults as $seoResult)
                                            <tr>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;">
                                                        {{ $seoResult->content->title }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $score = $seoResult->overall_score;
                                                        $scoreClass = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                                        $scoreIcon = $score >= 80 ? 'bi-emoji-smile' : ($score >= 50 ? 'bi-emoji-neutral' : 'bi-emoji-frown');
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                            <div class="progress-bar bg-{{ $scoreClass }}" role="progressbar" 
                                                                style="width: {{ $score }}%" aria-valuenow="{{ $score }}" 
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="badge bg-{{ $scoreClass }} d-flex align-items-center">
                                                            <i class="bi {{ $scoreIcon }} me-1"></i>
                                                            {{ number_format($score, 1) }}%
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $titleScore = $seoResult->page_title_score;
                                                        $titleClass = $titleScore >= 80 ? 'success' : ($titleScore >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <span class="badge bg-{{ $titleClass }}">{{ number_format($titleScore, 1) }}%</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $metaScore = $seoResult->meta_description_score;
                                                        $metaClass = $metaScore >= 80 ? 'success' : ($metaScore >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <span class="badge bg-{{ $metaClass }}">{{ number_format($metaScore, 1) }}%</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $contentScore = $seoResult->content_score;
                                                        $contentClass = $contentScore >= 80 ? 'success' : ($contentScore >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <span class="badge bg-{{ $contentClass }}">{{ number_format($contentScore, 1) }}%</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $seoResult->created_at->format('M d, Y H:i') }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('seo-results.show', $seoResult->id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i> View
                                                        </a>
                                                        <a href="{{ route('contents.edit', $seoResult->content->id) }}" 
                                                           class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $seoResults->links() }}
                            </div>
                        @else
                            <div class="text-center p-5">
                                <i class="bi bi-search display-4 text-muted mb-3"></i>
                                <p class="mb-4">No SEO analysis results found.</p>
                                <a href="{{ route('contents.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Analyze New Content
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
