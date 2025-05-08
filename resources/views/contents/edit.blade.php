@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Input Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Edit Content
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="seoAnalysisForm" action="{{ route('contents.update', $content->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Page Title</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title', $content->title) }}" required>
                                    <button class="btn btn-outline-secondary" type="button" id="ai-title-suggestion">
                                        <i class="fas fa-magic"></i> AI Suggestion
                                    </button>
                                </div>
                                <div class="form-text">Optimal length: 30-60 characters</div>
                                <div id="title-counter" class="form-text">0 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                                <div class="input-group">
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3" required>{{ old('meta_description', $content->meta_description) }}</textarea>
                                    <button class="btn btn-outline-secondary" type="button" id="ai-meta-suggestion">
                                        <i class="fas fa-magic"></i> AI Suggestion
                                    </button>
                                </div>
                                <div class="form-text">Optimal length: 120-160 characters</div>
                                <div id="meta-counter" class="form-text">0 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="target_keyword" class="form-label fw-bold">Target Keyword</label>
                                <input type="text" class="form-control" id="target_keyword" name="target_keyword"
                                    value="{{ old('target_keyword', $content->target_keyword) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="editor-container" class="form-label fw-bold">Content</label>
                                <div id="editor-container" style="height: 400px;">{!! old('content', $content->content) !!}</div>
                                <input type="hidden" name="content" id="content">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('contents.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                                <div>
                                 
                                    <button type="button" class="btn btn-success" id="update-button">
                                        <i class="fas fa-save me-1"></i> Update & Reanalyze
                                    </button>
                                </div>
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
                            <p>Click "Preview Analysis" to see your SEO score or "Update & Reanalyze" to save changes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure Quill is available before initializing
            if (typeof Quill === 'undefined') {
                console.error('Quill library not loaded. Check network connections or add a direct script include.');
                return;
            }

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
            const updateButton = document.getElementById('update-button');
            const form = document.getElementById('seoAnalysisForm');

            // Immediately set the initial content value
            contentInput.value = quill.root.innerHTML;
            console.log('Initial content set:', contentInput.value.length, 'characters');

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
            if (titleInput) titleInput.dispatchEvent(new Event('input'));
            if (metaDescription) metaDescription.dispatchEvent(new Event('input'));

            // Form validation function
            function validateForm() {
                if (!titleInput.value.trim()) return false;
                if (!metaDescription.value.trim()) return false;
                if (!document.getElementById('target_keyword').value.trim()) return false;
                if (!contentInput.value.trim() || contentInput.value === '<p><br></p>') return false;
                return true;
            }

            // AI Title Suggestion functionality
            document.getElementById('ai-title-suggestion').addEventListener('click', function() {
                // Get content from Quill
                const quillContent = quill.root.innerHTML;
                contentInput.value = quillContent;
                
                // Get target keyword
                const targetKeyword = document.getElementById('target_keyword').value.trim();
                
                // Validate required fields
                if (!targetKeyword) {
                    alert('Target keyword is required for AI title suggestion');
                    return;
                }
                
                if (!quillContent || quillContent === '<p><br></p>') {
                    alert('Content is required for AI title suggestion');
                    return;
                }
                
                // Show loading state
                const originalButtonText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
                this.disabled = true;
                
                // Create a new FormData instance
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('target_keyword', targetKeyword);
                formData.append('content', quillContent);
                
                // Get the CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Make AJAX request to title suggestion endpoint
                fetch('{{ route('api.suggest-title') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw { status: response.status, data: errorData };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Reset button state
                    this.innerHTML = originalButtonText;
                    this.disabled = false;
                    
                    if (data.success && data.title) {
                        // Update title input with suggestion
                        titleInput.value = data.title;
                        titleInput.dispatchEvent(new Event('input')); // Trigger counter update
                    } else {
                        let errorMessage = data.message || 'Failed to generate title suggestion';
                        console.error('AI Title Suggestion Error:', data);
                        alert(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.innerHTML = originalButtonText;
                    this.disabled = false;
                    
                    let errorMsg = 'An error occurred while generating title suggestion';
                    
                    if (error.status === 422 && error.data && error.data.message) {
                        // Format validation errors
                        let validationErrors = [];
                        for (let field in error.data.message) {
                            error.data.message[field].forEach(msg => {
                                validationErrors.push(msg);
                            });
                        }
                        errorMsg = validationErrors.join('\n');
                    } else if (error.data && error.data.message) {
                        errorMsg = error.data.message;
                        
                        // Special handling for API key errors
                        if (error.data.error_code === 'API_KEY_MISSING' || error.data.error_code === 'API_ERROR') {
                            errorMsg += '\n\nPlease contact the administrator to check the Gemini API configuration.';
                        }
                    } else if (error.message) {
                        errorMsg += ': ' + error.message;
                    }
                    
                    alert(errorMsg);
                });
            });

            // AI Meta Description Suggestion functionality
            document.getElementById('ai-meta-suggestion').addEventListener('click', function() {
                // Get content from Quill
                const quillContent = quill.root.innerHTML;
                contentInput.value = quillContent;
                
                // Get target keyword
                const targetKeyword = document.getElementById('target_keyword').value.trim();
                
                // Validate required fields
                if (!targetKeyword) {
                    alert('Target keyword is required for AI meta description suggestion');
                    return;
                }
                
                if (!quillContent || quillContent === '<p><br></p>') {
                    alert('Content is required for AI meta description suggestion');
                    return;
                }
                
                // Show loading state
                const originalButtonText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
                this.disabled = true;
                
                // Create a new FormData instance
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('target_keyword', targetKeyword);
                formData.append('content', quillContent);
                
                // Get the CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Make AJAX request to meta description suggestion endpoint
                fetch('{{ route('api.suggest-meta-description') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw { status: response.status, data: errorData };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Reset button state
                    this.innerHTML = originalButtonText;
                    this.disabled = false;
                    
                    if (data.success && data.meta_description) {
                        // Update meta description input with suggestion
                        metaDescription.value = data.meta_description;
                        metaDescription.dispatchEvent(new Event('input')); // Trigger counter update
                    } else {
                        let errorMessage = data.message || 'Failed to generate meta description suggestion';
                        console.error('AI Meta Description Suggestion Error:', data);
                        alert(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.innerHTML = originalButtonText;
                    this.disabled = false;
                    
                    let errorMsg = 'An error occurred while generating meta description suggestion';
                    
                    if (error.status === 422 && error.data && error.data.message) {
                        // Format validation errors
                        let validationErrors = [];
                        for (let field in error.data.message) {
                            error.data.message[field].forEach(msg => {
                                validationErrors.push(msg);
                            });
                        }
                        errorMsg = validationErrors.join('\n');
                    } else if (error.data && error.data.message) {
                        errorMsg = error.data.message;
                        
                        // Special handling for API key errors
                        if (error.data.error_code === 'API_KEY_MISSING' || error.data.error_code === 'API_ERROR') {
                            errorMsg += '\n\nPlease contact the administrator to check the Gemini API configuration.';
                        }
                    } else if (error.message) {
                        errorMsg += ': ' + error.message;
                    }
                    
                    alert(errorMsg);
                });
            });

            // Update content
            updateButton.addEventListener('click', function() {
                // Check if form is valid
                if (!validateForm()) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Get content from Quill and set it to hidden input
                const latestContent = quill.root.innerHTML;
                contentInput.value = latestContent;
                
                console.log('Content before submission:', contentInput.value);
                console.log('Content length:', contentInput.value.length);

                // Double-check if content is empty and show a more specific error
                if (!contentInput.value || contentInput.value.trim() === '' || contentInput.value === '<p><br></p>') {
                    alert('Content cannot be empty. Please add some content before submitting.');
                    this.innerHTML = '<i class="fas fa-save me-1"></i> Update & Reanalyze';
                    this.disabled = false;
                    return;
                }

                // Show loading state
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...';
                this.disabled = true;

                // Collect form data
                const formData = new FormData();
                
                // Add all the form fields manually
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PUT');
                formData.append('title', titleInput.value);
                formData.append('meta_description', metaDescription.value);
                formData.append('target_keyword', document.getElementById('target_keyword').value);
                
                // Ensure content from Quill is properly added
                const finalContent = quill.root.innerHTML;
                contentInput.value = finalContent; // Update hidden field
                formData.append('content', finalContent);
                
                // Add request ID to prevent duplicate submissions
                const requestId = 'req_update_' + Date.now();
                formData.append('request_id', requestId);
                console.log('Update request ID:', requestId);
                
                // Indicate we want to reanalyze
                formData.append('reanalyze', 'true');
                
                // Make the PUT request
                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Try to get details from the error response if possible
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || `Server returned ${response.status}`);
                        }).catch(e => {
                            // If we can't parse the JSON, just throw the status
                            throw new Error(`Server returned ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Redirect to the result page
                        window.location.href = data.redirect;
                    } else {
                        throw new Error(data.message || 'An error occurred.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Show a more user-friendly error
                    let errorMessage = error.message || 'An unknown error occurred';
                    
                    // Create a detailed error message for the alert
                    alert('Error updating content: ' + errorMessage);
                    
                    // Reset button
                    this.innerHTML = '<i class="fas fa-save me-1"></i> Update & Reanalyze';
                    this.disabled = false;
                });
            });

            // Prevent the form from being submitted directly
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                return false;
            });

            // Display results function
            function displayResults(data) {
                // Check if we have valid data
                if (!data || typeof data !== 'object') {
                    console.error('Invalid data received', data);
                    document.getElementById('results').innerHTML = `
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                            </div>
                            <div class="card-body text-center p-5">
                                <p>Invalid data received from server. Please try again.</p>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                const score = data.score || 0;
                const titleScore = data.title_score || 0;
                const metaScore = data.meta_score || 0;
                const contentScore = data.content_score || 0;
                
                const scoreClass = score >= 80 ? 'success' : (score >= 50 ? 'warning' : 'danger');
                const titleScoreClass = titleScore >= 80 ? 'success' : (titleScore >= 50 ? 'warning' : 'danger');
                const metaScoreClass = metaScore >= 80 ? 'success' : (metaScore >= 50 ? 'warning' : 'danger');
                const contentScoreClass = contentScore >= 80 ? 'success' : (contentScore >= 50 ? 'warning' : 'danger');
                
                let resultsHTML = `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Overall SEO Score</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="progress-circle progress-${scoreClass}" data-value="${Math.round(score)}">
                                <span class="progress-circle-left">
                                    <span class="progress-circle-bar"></span>
                                </span>
                                <span class="progress-circle-right">
                                    <span class="progress-circle-bar"></span>
                                </span>
                                <div class="progress-circle-value">
                                    <div>
                                        ${Math.round(score)}<span>%</span>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 text-${scoreClass} fw-bold">
                                ${score >= 80 ? 'Excellent' : (score >= 50 ? 'Fair' : 'Poor')}
                            </p>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body text-center">
                                        <h6 class="fw-bold mb-2">Page Title</h6>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-${titleScoreClass}" role="progressbar" style="width: ${titleScore}%" 
                                                aria-valuenow="${titleScore}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small mb-0">${Math.round(titleScore)}%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body text-center">
                                        <h6 class="fw-bold mb-2">Meta Description</h6>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-${metaScoreClass}" role="progressbar" style="width: ${metaScore}%" 
                                                aria-valuenow="${metaScore}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small mb-0">${Math.round(data.meta_score)}%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body text-center">
                                        <h6 class="fw-bold mb-2">Content</h6>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-${contentScoreClass}" role="progressbar" style="width: ${data.content_score}%" 
                                                aria-valuenow="${data.content_score}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small mb-0">${Math.round(data.content_score)}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                
                // Title feedback
                resultsHTML += `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-type-h1 me-2"></i>Page Title Feedback</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                `;
                
                // Check if title_feedback exists and is an array
                if (Array.isArray(data.title_feedback) && data.title_feedback.length > 0) {
                    data.title_feedback.forEach(item => {
                        if (item && typeof item === 'object' && 'score' in item && 'description' in item) {
                            const iconClass = (item.score > 0.5) ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-danger';
                            resultsHTML += `
                            <li class="list-group-item bg-transparent px-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi ${iconClass} fs-4 me-2"></i>
                                    <span>${item.description}</span>
                                </div>
                            </li>
                            `;
                        }
                    });
                } else {
                    // Handle case where feedback isn't an array
                    resultsHTML += `
                    <li class="list-group-item bg-transparent px-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-info fs-4 me-2"></i>
                            <span>No detailed feedback available</span>
                        </div>
                    </li>
                    `;
                }
                
                resultsHTML += `
                        </ul>
                    </div>
                </div>
                `;
                
                // Meta feedback
                resultsHTML += `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-card-text me-2"></i>Meta Description Feedback</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                `;
                
                // Check if meta_feedback exists and is an array
                if (Array.isArray(data.meta_feedback) && data.meta_feedback.length > 0) {
                    data.meta_feedback.forEach(item => {
                        if (item && typeof item === 'object' && 'score' in item && 'description' in item) {
                            const iconClass = (item.score > 0.5) ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-danger';
                            resultsHTML += `
                            <li class="list-group-item bg-transparent px-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi ${iconClass} fs-4 me-2"></i>
                                    <span>${item.description}</span>
                                </div>
                            </li>
                            `;
                        }
                    });
                } else {
                    // Handle case where feedback isn't an array
                    resultsHTML += `
                    <li class="list-group-item bg-transparent px-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-info fs-4 me-2"></i>
                            <span>No detailed feedback available</span>
                        </div>
                    </li>
                    `;
                }
                
                resultsHTML += `
                        </ul>
                    </div>
                </div>
                `;
                
                // Content feedback
                resultsHTML += `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Content Feedback</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                `;
                
                // Check if content_feedback exists and is an array
                if (Array.isArray(data.content_feedback) && data.content_feedback.length > 0) {
                    data.content_feedback.forEach(item => {
                        if (item && typeof item === 'object' && 'score' in item && 'description' in item) {
                            const iconClass = (item.score > 0.5) ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-danger';
                            resultsHTML += `
                            <li class="list-group-item bg-transparent px-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi ${iconClass} fs-4 me-2"></i>
                                    <span>${item.description}</span>
                                </div>
                            </li>
                            `;
                        }
                    });
                } else {
                    // Handle case where feedback isn't an array
                    resultsHTML += `
                    <li class="list-group-item bg-transparent px-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-info fs-4 me-2"></i>
                            <span>No detailed feedback available</span>
                        </div>
                    </li>
                    `;
                }
                
                resultsHTML += `
                        </ul>
                    </div>
                </div>
                `;
                
                document.getElementById('results').innerHTML = resultsHTML;
                
                // Initialize progress circles
                initProgressCircles();
            }

            // Initialize progress circles function
            function initProgressCircles() {
                const circles = document.querySelectorAll('.progress-circle');
                
                circles.forEach(circle => {
                    const value = circle.getAttribute('data-value');
                    const left = circle.querySelector('.progress-circle-left .progress-circle-bar');
                    const right = circle.querySelector('.progress-circle-right .progress-circle-bar');
                    
                    if (value > 0) {
                        if (value <= 50) {
                            right.style.transform = 'rotate(' + (value / 100 * 360) + 'deg)';
                        } else {
                            right.style.transform = 'rotate(180deg)';
                            left.style.transform = 'rotate(' + ((value - 50) / 100 * 360) + 'deg)';
                        }
                    }
                });
            }
        });
    </script>
@endsection
