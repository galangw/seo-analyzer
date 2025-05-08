@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Dashboard') }}</h5>
                    <a href="{{ route('contents.create') }}" class="btn btn-primary">New Analysis</a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <h4>Welcome to SEO Analyzer</h4>
                        <p>Optimize your content and improve your search engine rankings.</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="dashboard-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-file-alt fa-2x" style="color: var(--primary-color);"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Total Content</h6>
                                        <div class="stat-value">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dashboard-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-chart-line fa-2x" style="color: var(--secondary-color);"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Avg. SEO Score</h6>
                                        <div class="stat-value">0%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dashboard-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-search fa-2x" style="color: var(--accent-color);"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Recent Analyses</h6>
                                        <div class="stat-value">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('contents.create') }}" class="btn btn-primary w-100">
                                                <i class="fas fa-file-medical me-2"></i> New Analysis
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('contents.index') }}" class="btn btn-primary w-100">
                                                <i class="fas fa-list me-2"></i> My Content
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('seo-results.index') }}" class="btn btn-primary w-100">
                                                <i class="fas fa-history me-2"></i> View History
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
