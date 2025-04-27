@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">SEO Analysis Results</h5>
                        <div>
                            <a href="{{ route('contents.edit', $content->id) }}" class="btn btn-primary">Edit Content</a>
                            <form action="{{ route('seo-results.reanalyze', $content->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Reanalyze</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5>{{ $content->title }}</h5>
                                <p class="text-muted">Target Keyword: <strong>{{ $content->target_keyword }}</strong></p>
                                <p class="mb-0">Analysis Date: {{ $seoResult->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <h6 class="mb-2">Overall SEO Score</h6>
                                    @php
                                        $score = $seoResult->overall_score;
                                        $ratingClass = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                        $ratingText =
                                            $score >= 80 ? 'Good' : ($score >= 50 ? 'Need Improvement' : 'Poor');
                                    @endphp
                                    <div class="progress mb-2" style="height: 20px; width: 100%;">
                                        <div class="progress-bar bg-{{ $ratingClass }}" role="progressbar"
                                            style="width: {{ $score }}%" aria-valuenow="{{ $score }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($score, 1) }}%</div>
                                    </div>
                                    <span class="badge bg-{{ $ratingClass }}">{{ $ratingText }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Page Title Score</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $titleScore = $seoResult->page_title_score;
                                        $titleClass =
                                            $titleScore >= 80 ? 'success' : ($titleScore >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $titleClass }}" role="progressbar"
                                            style="width: {{ $titleScore }}%" aria-valuenow="{{ $titleScore }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h5 class="text-center">{{ number_format($titleScore, 1) }}%</h5>

                                    <div class="mt-3">
                                        <h6>Title: </h6>
                                        <p class="border p-2 rounded">{{ $content->title }}</p>
                                        <p class="text-muted small">{{ strlen($content->title) }} characters (recommended:
                                            30-60)</p>

                                        <h6>Sub-criteria:</h6>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($seoResult->detail_score['page_title']['details'] as $key => $detail)
                                                @php
                                                    $detailClass =
                                                        $detail['score'] >= 0.8
                                                            ? 'success'
                                                            : ($detail['score'] >= 0.5
                                                                ? 'warning'
                                                                : 'danger');
                                                @endphp
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ trans('seo.' . $key) }}
                                                    <span
                                                        class="badge bg-{{ $detailClass }} rounded-pill">{{ number_format($detail['score'] * 100, 0) }}%</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Meta Description Score</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $metaScore = $seoResult->meta_description_score;
                                        $metaClass =
                                            $metaScore >= 80 ? 'success' : ($metaScore >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $metaClass }}" role="progressbar"
                                            style="width: {{ $metaScore }}%" aria-valuenow="{{ $metaScore }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h5 class="text-center">{{ number_format($metaScore, 1) }}%</h5>

                                    <div class="mt-3">
                                        <h6>Meta Description: </h6>
                                        <p class="border p-2 rounded">{{ $content->meta_description }}</p>
                                        <p class="text-muted small">{{ strlen($content->meta_description) }} characters
                                            (recommended: 120-160)</p>

                                        <h6>Sub-criteria:</h6>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($seoResult->detail_score['meta_description']['details'] as $key => $detail)
                                                @php
                                                    $detailClass =
                                                        $detail['score'] >= 0.8
                                                            ? 'success'
                                                            : ($detail['score'] >= 0.5
                                                                ? 'warning'
                                                                : 'danger');
                                                @endphp
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ trans('seo.' . $key) }}
                                                    <span
                                                        class="badge bg-{{ $detailClass }} rounded-pill">{{ number_format($detail['score'] * 100, 0) }}%</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Content Score</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $contentScore = $seoResult->content_score;
                                        $contentClass =
                                            $contentScore >= 80
                                                ? 'success'
                                                : ($contentScore >= 50
                                                    ? 'warning'
                                                    : 'danger');
                                    @endphp
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $contentClass }}" role="progressbar"
                                            style="width: {{ $contentScore }}%" aria-valuenow="{{ $contentScore }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h5 class="text-center">{{ number_format($contentScore, 1) }}%</h5>

                                    <div class="mt-3">
                                        <h6>Sub-criteria:</h6>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($seoResult->detail_score['content']['details'] as $key => $detail)
                                                @php
                                                    $detailClass =
                                                        $detail['score'] >= 0.8
                                                            ? 'success'
                                                            : ($detail['score'] >= 0.5
                                                                ? 'warning'
                                                                : 'danger');
                                                @endphp
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ trans('seo.' . $key) }}
                                                    <span
                                                        class="badge bg-{{ $detailClass }} rounded-pill">{{ number_format($detail['score'] * 100, 0) }}%</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Recommendations</h6>
                                </div>
                                <div class="card-body">
                                    @if (count($seoResult->detail_score['recommendations']) > 0)
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
                                                        <tr>
                                                            <td>{{ $recommendation['section'] }}</td>
                                                            <td>{{ $recommendation['criteria'] }}</td>
                                                            <td>{{ $recommendation['description'] }}</td>
                                                            <td>
                                                                @if (!empty($recommendation['actual']))
                                                                    <p class="mb-0">{{ $recommendation['actual'] }}</p>
                                                                @endif
                                                                @if (!empty($recommendation['recommended']))
                                                                    <p class="text-success mb-0">
                                                                        {{ $recommendation['recommended'] }}</p>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-success">
                                            <strong>Great job!</strong> Your content meets all SEO best practices.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Content Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="border rounded p-3">
                                        {!! $content->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endsection
