@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Analysis History</h5>
                    </div>
                    <div class="card-body">
                        @if ($seoResults->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Content Title</th>
                                            <th>Overall Score</th>
                                            <th>Title Score</th>
                                            <th>Meta Description Score</th>
                                            <th>Content Score</th>
                                            <th>Analyzed On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($seoResults as $seoResult)
                                            <tr>
                                                <td>{{ Str::limit($seoResult->content->title, 40) }}</td>
                                                <td>
                                                    @php
                                                        $score = $seoResult->overall_score;
                                                        $scoreClass =
                                                            $score >= 80
                                                                ? 'success'
                                                                : ($score >= 50
                                                                    ? 'warning'
                                                                    : 'danger');
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                            <div class="progress-bar bg-{{ $scoreClass }}"
                                                                role="progressbar" style="width: {{ $score }}%"
                                                                aria-valuenow="{{ $score }}" aria-valuemin="0"
                                                                aria-valuemax="100"></div>
                                                        </div>
                                                        <span>{{ number_format($score, 1) }}%</span>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($seoResult->page_title_score, 1) }}%</td>
                                                <td>{{ number_format($seoResult->meta_description_score, 1) }}%</td>
                                                <td>{{ number_format($seoResult->content_score, 1) }}%</td>
                                                <td>{{ $seoResult->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('seo-results.show', $seoResult->id) }}"
                                                        class="btn btn-sm btn-info">View</a>
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
                            <div class="text-center p-4">
                                <p>No SEO analysis results found.</p>
                                <a href="{{ route('contents.create') }}" class="btn btn-primary">Analyze New Content</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
