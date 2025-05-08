@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0">My Content</h5>
                            <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0 mt-2 mt-md-0">
                                <!-- Search Form -->
                                <form action="{{ route('contents.index') }}" method="GET" class="d-flex align-items-center ms-auto me-2">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by title or keyword" value="{{ request('search') }}">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('contents.index') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                                <a href="{{ route('contents.create') }}" class="btn btn-primary">Add New Content</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(request('search'))
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-search me-2"></i>
                                <strong>{{ $contents->total() }}</strong> {{ Str::plural('result', $contents->total()) }} found for "<strong>{{ request('search') }}</strong>"
                                <a href="{{ route('contents.index') }}" class="ms-2 text-decoration-none"><i class="bi bi-x-circle"></i> Clear</a>
                            </div>
                        @endif
                        
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
                                                                class="btn btn-sm btn-outline-info ">
                                                                <i class="bi bi-graph-up me-1"></i> Results
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('contents.edit', $content->id) }}"
                                                            class="btn btn-sm btn-outline-primary ms-1">
                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                        </a>
                                                        <form action="{{ route('contents.destroy', $content->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this content?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-danger ms-1 border-radius-0">
                                                                <i class="bi bi-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                {{ $contents->appends(request()->query())->links('pagination.custom') }}
                            </div>
                        @else
                            <div class="text-center p-4">
                                @if(request('search'))
                                    <p>No results found for "{{ request('search') }}".</p>
                                    <a href="{{ route('contents.index') }}" class="btn btn-outline-secondary">Clear Search</a>
                                @else
                                    <p>You haven't analyzed any content yet.</p>
                                    <a href="{{ route('contents.create') }}" class="btn btn-primary">Analyze Your First
                                        Content</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
   
</script>
@endsection
