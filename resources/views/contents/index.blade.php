@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Content</h5>
                        <a href="{{ route('contents.create') }}" class="btn btn-primary">Add New Content</a>
                    </div>
                    <div class="card-body">
                        @if ($contents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
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
                                        @foreach ($contents as $content)
                                            <tr>
                                                <td>{{ Str::limit($content->title, 50) }}</td>
                                                <td>{{ $content->target_keyword }}</td>
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
                                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                                <div class="progress-bar bg-{{ $scoreClass }}"
                                                                    role="progressbar" style="width: {{ $score }}%"
                                                                    aria-valuenow="{{ $score }}" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                            <span>{{ number_format($score, 1) }}%</span>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary">Not analyzed</span>
                                                    @endif
                                                </td>
                                                <td>{{ $content->created_at->diffForHumans() }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if ($content->latestSeoResult)
                                                            <a href="{{ route('seo-results.show', $content->latestSeoResult->id) }}"
                                                                class="btn btn-sm btn-info">View Results</a>
                                                        @endif
                                                        <a href="{{ route('contents.edit', $content->id) }}"
                                                            class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('contents.destroy', $content->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this content?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                        <form action="{{ route('seo-results.reanalyze', $content->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-secondary">Reanalyze</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $contents->links() }}
                            </div>
                        @else
                            <div class="text-center p-4">
                                <p>You haven't analyzed any content yet.</p>
                                <a href="{{ route('contents.create') }}" class="btn btn-primary">Analyze Your First
                                    Content</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
