@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Input Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Content Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="seoAnalysisForm">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Page Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title') }}" placeholder="Enter page title" required>
                                <div class="form-text">Optimal length: 30-60 characters</div>
                                <div id="title-counter" class="form-text">0 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                    placeholder="Enter meta description" required>{{ old('meta_description') }}</textarea>
                                <div class="form-text">Optimal length: 120-160 characters</div>
                                <div id="meta-counter" class="form-text">0 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="target_keyword" class="form-label fw-bold">Target Keyword</label>
                                <input type="text" class="form-control" id="target_keyword" name="target_keyword"
                                    value="{{ old('target_keyword') }}" placeholder="Enter target keyword" required>
                            </div>

                            <div class="mb-3">
                                <label for="editor-container" class="form-label fw-bold">Content</label>
                                <div id="editor-container" style="height: 400px;">{{ old('content') }}</div>
                                <input type="hidden" name="content" id="content">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('contents.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                                <button type="button" class="btn btn-primary" id="analyze-button">
                                    <i class="fas fa-calculator me-1"></i> Analyze Content
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Results Section -->
                <div id="results">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>SEO Score Preview</h5>
                        </div>
                        <div class="card-body text-center p-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <p>Fill out the form and click "Analyze Content" to see your SEO score.</p>
                        </div>
                    </div>
                </div>

                <!-- Error Alert Section -->
                <div id="error-container" class="mt-3" style="display: none;">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="error-message">An error occurred.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Quill editor
            const quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'direction': 'rtl'
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'font': []
                        }],
                        [{
                            'align': []
                        }],
                        ['clean'],
                        ['link', 'image']
                    ]
                }
            });

            // DOM elements
            const titleInput = document.getElementById('title');
            const titleCounter = document.getElementById('title-counter');
            const metaDescription = document.getElementById('meta_description');
            const metaCounter = document.getElementById('meta-counter');
            const contentInput = document.getElementById('content');
            const analyzeButton = document.getElementById('analyze-button');
            const form = document.getElementById('seoAnalysisForm');
            const errorContainer = document.getElementById('error-container');
            const errorMessage = document.getElementById('error-message');


            // Character counters
            titleInput.addEventListener('input', function() {
                const length = this.value.length;
                titleCounter.textContent = length + ' characters';

                if (length < 30 || length > 60) {
                    titleCounter.classList.add('text-danger');
                    titleCounter.classList.remove('text-success');
                } else {
                    titleCounter.classList.add('text-success');
                    titleCounter.classList.remove('text-danger');
                }
            });

            metaDescription.addEventListener('input', function() {
                const length = this.value.length;
                metaCounter.textContent = length + ' characters';

                if (length < 120 || length > 160) {
                    metaCounter.classList.add('text-danger');
                    metaCounter.classList.remove('text-success');
                } else {
                    metaCounter.classList.add('text-success');
                    metaCounter.classList.remove('text-danger');
                }
            });

            // Handle Quill content changes
            quill.on('text-change', function() {
                contentInput.value = quill.root.innerHTML;
            });

            // Initialize counters
            titleInput.dispatchEvent(new Event('input'));
            metaDescription.dispatchEvent(new Event('input'));

            // Set initial content
            contentInput.value = quill.root.innerHTML;

            // Helper function to show errors
            function showError(message) {
                errorMessage.textContent = message;
                errorContainer.style.display = 'block';

                // Scroll to error
                errorContainer.scrollIntoView({
                    behavior: 'smooth'
                });
            }

            // Helper function to hide errors
            function hideError() {
                errorContainer.style.display = 'none';
            }

            // Real-time analysis on button click
            analyzeButton.addEventListener('click', function() {
                // Hide any previous errors
                hideError();

                // Check if form is valid
                if (!validateForm()) {
                    showError('Please fill in all required fields.');
                    return;
                }

                // Get content from Quill and set it to hidden input
                contentInput.value = quill.root.innerHTML;

                // Show loading state
                document.getElementById('results').innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>SEO Score Preview</h5>
                    </div>
                    <div class="card-body text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Analyzing your content...</p>
                    </div>
                </div>
                `;

                // Collect form data
                const formData = new FormData(form);

                // Make AJAX request to analyze endpoint
                fetch('{{ route('api.analyze-content') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Server returned ' + response.status + ' ' + response
                                .statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Display results
                        displayResults(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('results').innerHTML = `
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                                </div>
                                <div class="card-body text-center p-5">
                                    <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                                    <p>An error occurred while analyzing your content.</p>
                                </div>
                            </div>
                        `;
                        showError('Error: ' + error.message);
                    });
            });

            // Form validation
            function validateForm() {
                if (!titleInput.value.trim()) return false;
                if (!metaDescription.value.trim()) return false;
                if (!document.getElementById('target_keyword').value.trim()) return false;

                const content = quill.root.textContent || '';
                if (!content.trim()) return false;

                return true;
            }

            // Function to display analysis results
            function displayResults(data) {
                // Calculate classes based on scores
                const overallScoreClass = data.score >= 80 ? 'success' : (data.score >= 50 ? 'warning' : 'danger');
                const titleScoreClass = data.title_score >= 80 ? 'success' : (data.title_score >= 50 ? 'warning' :
                    'danger');
                const metaScoreClass = data.meta_score >= 80 ? 'success' : (data.meta_score >= 50 ? 'warning' :
                    'danger');
                const contentScoreClass = data.content_score >= 80 ? 'success' : (data.content_score >= 50 ?
                    'warning' : 'danger');

                // Create results HTML
                const resultsHtml = `
                <div class="card score-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>SEO Score Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="score-value">${data.score.toFixed(1)}%</div>
                            <div class="progress">
                                <div class="progress-bar bg-${overallScoreClass}" role="progressbar" style="width: ${data.score}%"></div>
                            </div>
                        </div>

                        <!-- Title Section -->
                        <div class="mb-4">
                            <h6 class="d-flex justify-content-between">
                                Page Title <span class="badge bg-${titleScoreClass}">${data.title_score.toFixed(1)}%</span>
                            </h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-${titleScoreClass}" role="progressbar" style="width: ${data.title_score}%"></div>
                            </div>
                            <div class="criteria-item">
                                <small class="text-muted d-block">${data.title_feedback}</small>
                            </div>
                        </div>

                        <!-- Meta Description Section -->
                        <div class="mb-4">
                            <h6 class="d-flex justify-content-between">
                                Meta Description <span class="badge bg-${metaScoreClass}">${data.meta_score.toFixed(1)}%</span>
                            </h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-${metaScoreClass}" role="progressbar" style="width: ${data.meta_score}%"></div>
                            </div>
                            <div class="criteria-item">
                                <small class="text-muted d-block">${data.meta_feedback}</small>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="mb-4">
                            <h6 class="d-flex justify-content-between">
                                Content <span class="badge bg-${contentScoreClass}">${data.content_score.toFixed(1)}%</span>
                            </h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-${contentScoreClass}" role="progressbar" style="width: ${data.content_score}%"></div>
                            </div>
                            <div class="criteria-item">
                                <small class="text-muted d-block">${data.content_feedback}</small>
                            </div>
                        </div>

                        <button type="button" id="save-content" class="btn btn-success w-100" onclick="saveContentToDatabase()">
                            <i class="fas fa-save me-2"></i>Save Content
                        </button>
                    </div>
                </div>
            `;

                // Update results div
                document.getElementById('results').innerHTML = resultsHtml;
            }

            // Add saveContentToDatabase function to window
            window.saveContentToDatabase = function() {
                // Hide any previous errors
                hideError();

                // Update hidden content field with Quill content
                document.getElementById('content').value = quill.root.innerHTML;

                // Submit the form to store endpoint
                const formData = new FormData(form);

                // Show loading state
                const saveButton = document.getElementById('save-content');
                const originalText = saveButton.innerHTML;
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
                saveButton.disabled = true;

                fetch('{{ route('contents.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Server returned ' + response.status + ' ' + response
                                .statusText);
                        }

                        if (response.redirected) {
                            window.location.href = response.url;
                            return null;
                        } else {
                            return response.json();
                        }
                    })
                    .then(data => {
                        if (data && data.redirect) {
                            window.location.href = data.redirect;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        saveButton.innerHTML = originalText;
                        saveButton.disabled = false;
                        showError('Error saving content: ' + error.message);
                    });
            };
        });
    </script>
@endsection
