@extends('layouts.app')
@section('title', 'SEO Meter')
@section('head')
<style>
     .bi.spinning {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .quill-editor .ql-container {
            min-height: 300px;
        }

        /* Progress Circle (Pie Chart) Styles */
        .progress-circle {
            position: relative;
            margin: 0 auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: var(--bs-secondary-bg);
        }

        .progress-circle-bar {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            clip: rect(0px, 75px, 150px, 0px);
            background-color: var(--bs-secondary-bg);
        }

        .progress-circle-left .progress-circle-bar {
            transform-origin: center right;
            transform: rotate(0deg);
        }

        .progress-circle-right {
            transform: rotate(180deg);
        }

        .progress-circle-right .progress-circle-bar {
            transform-origin: center left;
            transform: rotate(0deg);
        }

        /* Progress color variants */
        .progress-success .progress-circle-bar {
            background-color: var(--bs-success);
        }

        .progress-warning .progress-circle-bar {
            background-color: var(--bs-warning);
        }

        .progress-danger .progress-circle-bar {
            background-color: var(--bs-danger);
        }

        .progress-circle-value {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 28px;
            font-weight: bold;
            background-color: var(--bs-body-bg);
            transform: scale(0.87);
        }

        .progress-circle-value span {
            font-size: 16px;
        }
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-pencil-square me-2"></i>Content Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="seoAnalysisForm">
                            @csrf
                            <div class="mb-4">
                                <label for="title" class="form-label">Page Title</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title') }}" placeholder="Enter page title" required>
                                    <button class="btn btn-outline-secondary" type="button" id="ai-title-suggestion">
                                        <i class="bi bi-magic"></i> AI Suggestion
                                    </button>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-body-secondary">Optimal length: 75-95 characters (40-120 acceptable)</small>
                                    <small id="title-counter" class="badge rounded-pill bg-secondary">0 characters</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <div class="input-group">
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                        placeholder="Enter meta description" required>{{ old('meta_description') }}</textarea>
                                    <button class="btn btn-outline-secondary" type="button" id="ai-meta-suggestion">
                                        <i class="bi bi-magic"></i> AI Suggestion
                                    </button>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-body-secondary">Optimal length: 146-160 characters (100-160 acceptable)</small>
                                    <small id="meta-counter" class="badge rounded-pill bg-secondary">0 characters</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="target_keyword" class="form-label">Target Keyword</label>
                                <div class="input-group">
                                    <span class="input-group-text "><i class="bi bi-key"></i></span>
                                    <input type="text" class="form-control" id="target_keyword" name="target_keyword"
                                        value="{{ old('target_keyword') }}" placeholder="Enter target keyword" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="editor-container" class="form-label">Content</label>
                                <div class="quill-editor">
                                    <div id="editor-container">{{ old('content') }}</div>
                                </div>
                                <textarea name="content" id="content" style="display: none;"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('contents.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </a>
                                <button type="button" class="btn btn-primary" id="analyze-button">
                                    <i class="bi bi-clipboard-data me-1"></i> Analyze Content
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Results Section -->
                <div id="results">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="bi bi-graph-up me-2"></i>SEO Score Preview
                            </h5>
                        </div>
                        <div class="card-body text-center p-5">
                            <i class="bi bi-search display-1 text-body-secondary mb-3"></i>
                            <p>Fill out the form and click "Analyze Content" to see your SEO score.</p>
                        </div>
                    </div>
                </div>

                <!-- Error Alert Section -->
                <div id="error-container" class="mt-3" style="display: none;">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                        <div id="error-message">An error occurred.</div>
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

            // Register the HTML edit button module with Quill
            Quill.register('modules/htmlEditButton', htmlEditButton);

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
                        ['link', 'image'],
                        ['htmlEditButton']
                    ],
                    htmlEditButton: {
                        debug: false,
                        msg: "Edit kode HTML",
                        okText: "Simpan",
                        cancelText: "Batal",
                        buttonHTML: "<i class='bi bi-code-slash'></i>",
                        buttonTitle: "Edit HTML",
                        syntax: false,
                        prependSelector: 'div#editor-container',
                        editorModules: {
                            syntax: false
                        }
                    }
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
            const aiTitleSuggestion = document.getElementById('ai-title-suggestion');
            const aiMetaSuggestion = document.getElementById('ai-meta-suggestion');

            // Handle Quill content changes
            quill.on('text-change', function() {
                // Get content directly from the Quill editor's root element
                const editorContent = quill.root.innerHTML;

                // Update the hidden textarea value
                contentInput.value = editorContent;

                // Debug logging
                console.log("Content updated:", contentInput.value);
            });

            // Character counters
            titleInput.addEventListener('input', function() {
                const length = this.value.length;
                titleCounter.textContent = length + ' characters';

                if (length < 30 || length > 60) {
                    titleCounter.className = 'badge rounded-pill bg-danger';
                } else {
                    titleCounter.className = 'badge rounded-pill bg-success';
                }
            });

            metaDescription.addEventListener('input', function() {
                const length = this.value.length;
                metaCounter.textContent = length + ' characters';

                if (length < 120 || length > 160) {
                    metaCounter.className = 'badge rounded-pill bg-danger';
                } else {
                    metaCounter.className = 'badge rounded-pill bg-success';
                }
            });

            // Initialize counters
            if (titleInput) titleInput.dispatchEvent(new Event('input'));
            if (metaDescription) metaDescription.dispatchEvent(new Event('input'));

            // Set initial content
            const quillContent = quill.root.innerHTML;
            contentInput.value = quillContent;

            // Helper function to show errors
            function showError(message) {
                errorMessage.innerHTML = message;
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

            // Form validation function
            function validateForm() {
                let isValid = true;
                let errors = [];

                // Validate title
                if (!titleInput.value.trim()) {
                    errors.push('Page title is required');
                    isValid = false;
                }

                // Validate meta description
                if (!metaDescription.value.trim()) {
                    errors.push('Meta description is required');
                    isValid = false;
                }

                // Validate target keyword
                if (!document.getElementById('target_keyword').value.trim()) {
                    errors.push('Target keyword is required');
                    isValid = false;
                }

                // Validate content
                const contentValue = quill.root.innerHTML;
                if (!contentValue.trim() || contentValue === '<p><br></p>') {
                    errors.push('Content is required');
                    isValid = false;
                }

                // If validation failed, show error messages
                if (!isValid) {
                    showError(errors.join('<br>'));
                }

                return isValid;
            }

            // AI Title Suggestion functionality
            aiTitleSuggestion.addEventListener('click', function() {
                // Hide any previous errors
                hideError();

                // Get content from Quill
                const quillContent = quill.root.innerHTML;
                contentInput.value = quillContent;

                // Get target keyword
                const targetKeyword = document.getElementById('target_keyword').value.trim();

                // Validate required fields
                if (!targetKeyword) {
                    showError('Target keyword is required for AI title suggestion');
                    return;
                }

                if (!quillContent || quillContent === '<p><br></p>') {
                    showError('Content is required for AI title suggestion');
                    return;
                }

                // Show loading state
                const originalButtonText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-repeat spinning"></i> Generating...';
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
                        showError(errorMessage);
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
                        errorMsg = validationErrors.join('<br>');
                    } else if (error.data && error.data.message) {
                        errorMsg = error.data.message;

                        // Special handling for API key errors
                        if (error.data.error_code === 'API_KEY_MISSING' || error.data.error_code === 'API_ERROR') {
                            errorMsg += '<br><br>Please contact the administrator to check the Gemini API configuration.';
                        }
                    } else if (error.message) {
                        errorMsg += ': ' + error.message;
                    }

                    showError(errorMsg);
                });
            });

            // AI Meta Description Suggestion functionality
            aiMetaSuggestion.addEventListener('click', function() {
                // Hide any previous errors
                hideError();

                // Get content from Quill
                const quillContent = quill.root.innerHTML;
                contentInput.value = quillContent;

                // Get target keyword
                const targetKeyword = document.getElementById('target_keyword').value.trim();

                // Validate required fields
                if (!targetKeyword) {
                    showError('Target keyword is required for AI meta description suggestion');
                    return;
                }

                if (!quillContent || quillContent === '<p><br></p>') {
                    showError('Content is required for AI meta description suggestion');
                    return;
                }

                // Show loading state
                const originalButtonText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-repeat spinning"></i> Generating...';
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
                        showError(errorMessage);
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
                        errorMsg = validationErrors.join('<br>');
                    } else if (error.data && error.data.message) {
                        errorMsg = error.data.message;

                        // Special handling for API key errors
                        if (error.data.error_code === 'API_KEY_MISSING' || error.data.error_code === 'API_ERROR') {
                            errorMsg += '<br><br>Please contact the administrator to check the Gemini API configuration.';
                        }
                    } else if (error.message) {
                        errorMsg += ': ' + error.message;
                    }

                    showError(errorMsg);
                });
            });

            // Real-time analysis on button click
            analyzeButton.addEventListener('click', function() {
                // Hide any previous errors
                hideError();

                // Check if form is valid
                if (!validateForm()) {
                    return;
                }

                // Get content from Quill and set it to hidden input
                const quillContent = quill.root.innerHTML;
                contentInput.value = quillContent;

                // Debug content
                console.log("Content being sent:", contentInput.value);

                // Show loading state
                document.getElementById('results').innerHTML = `
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-graph-up me-2"></i>SEO Score Preview
                        </h5>
                    </div>
                    <div class="card-body text-center p-5">
                        <div class="spinner-border text-primary my-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Analyzing your content...</p>
                    </div>
                </div>
                `;

                // Create a new FormData instance directly from the form
                const formData = new FormData();

                // Add form fields manually to ensure proper encoding
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('title', titleInput.value);
                formData.append('meta_description', metaDescription.value);
                formData.append('target_keyword', document.getElementById('target_keyword').value);

                // Ensure content from Quill is properly added
                const finalContent = quill.root.innerHTML;
                contentInput.value = finalContent; // Update hidden field for consistency
                formData.append('content', finalContent);

                console.log("Final content being sent:", finalContent);

                // Get the CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Make AJAX request to analyze endpoint
                fetch('{{ route('api.analyze-content') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
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
                    // Log full response data
                    console.log("Success response:", data);

                    // Make sure we have the required data properties before proceeding
                    if (!data.success ||
                        data.score === undefined ||
                        data.title_score === undefined ||
                        data.meta_score === undefined ||
                        data.content_score === undefined ||
                        !data.title_feedback ||
                        !data.meta_feedback ||
                        !data.content_feedback) {

                        console.error("Invalid data format received:", data);
                        showError("Server returned an invalid data format. Please try again.");

                        document.getElementById('results').innerHTML = `
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <i class="bi bi-graph-up me-2"></i>SEO Score Preview
                                </h5>
                            </div>
                            <div class="card-body text-center p-5">
                                <i class="bi bi-exclamation-triangle display-1 text-danger mb-3"></i>
                                <p>Invalid response format. Please try again.</p>
                            </div>
                        </div>
                        `;
                        return;
                    }

                    // Display results
                    displayResults(data);
                })
                .catch(error => {
                    console.error('Error:', error);

                    let errorMsg = 'An error occurred while analyzing your content';

                    // Handle validation errors (422)
                    if (error.status === 422 && error.data && error.data.message) {
                        // Format validation errors
                        let validationErrors = [];
                        for (let field in error.data.message) {
                            error.data.message[field].forEach(msg => {
                                validationErrors.push(msg);
                            });
                        }
                        errorMsg = validationErrors.join('<br>');
                    } else if (error.message) {
                        errorMsg += ': ' + error.message;
                    } else if (error.status) {
                        errorMsg += ': Server returned ' + error.status;
                    }

                    showError(errorMsg);
                    document.getElementById('results').innerHTML = `
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="bi bi-graph-up me-2"></i>SEO Score Preview
                            </h5>
                        </div>
                        <div class="card-body text-center p-5">
                            <i class="bi bi-exclamation-triangle display-1 text-danger mb-3"></i>
                            <p>An error occurred while analyzing your content. Please try again.</p>
                        </div>
                    </div>
                    `;
                });
            });

            // Display results function
            function displayResults(data) {
                console.log("Response data:", data);

                // Convert scores to numbers to ensure proper handling
                const overallScore = Number(data.score);
                const titleScore = Number(data.title_score);
                const metaScore = Number(data.meta_score);
                const contentScore = Number(data.content_score);

                const scoreClass = overallScore >= 80 ? 'success' : (overallScore >= 50 ? 'warning' : 'danger');

                const titleScoreClass = titleScore >= 80 ? 'success' : (titleScore >= 50 ? 'warning' : 'danger');
                const metaScoreClass = metaScore >= 80 ? 'success' : (metaScore >= 50 ? 'warning' : 'danger');
                const contentScoreClass = contentScore >= 80 ? 'success' : (contentScore >= 50 ? 'warning' : 'danger');

                let resultsHTML = `
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-body">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-graph-up me-2"></i>Overall SEO Score
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="progress-circle progress-${scoreClass}" data-value="${Math.round(overallScore)}">
                                <span class="progress-circle-left">
                                    <span class="progress-circle-bar"></span>
                                </span>
                                <span class="progress-circle-right">
                                    <span class="progress-circle-bar"></span>
                                </span>
                                <div class="progress-circle-value">
                                    <div>
                                        ${Math.round(overallScore)}<span>%</span>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 fw-bold">
                                <span class="badge bg-${scoreClass}">
                                    ${overallScore >= 80 ? 'Excellent' : (overallScore >= 50 ? 'Fair' : 'Poor')}
                                </span>
                            </p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border-0 bg-body h-100">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Page Title</h6>
                                            <span class="badge text-bg-secondary">20%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-${titleScoreClass}" role="progressbar" style="width: ${Math.round(titleScore)}%"
                                                aria-valuenow="${Math.round(titleScore)}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small mb-0 text-${titleScoreClass} fw-bold">${Math.round(titleScore)}%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-body h-100">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Meta Description</h6>
                                            <span class="badge text-bg-secondary">5%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-${metaScoreClass}" role="progressbar" style="width: ${Math.round(metaScore)}%"
                                                aria-valuenow="${Math.round(metaScore)}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small mb-0 text-${metaScoreClass} fw-bold">${Math.round(metaScore)}%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-body h-100">
                                    <div class="card-body text-center">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Content</h6>
                                            <span class="badge text-bg-secondary">75%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-${contentScoreClass}" role="progressbar" style="width: ${Math.round(contentScore)}%"
                                                aria-valuenow="${Math.round(contentScore)}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small mb-0 text-${contentScoreClass} fw-bold">${Math.round(contentScore)}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;

                // Title feedback
                resultsHTML += `
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-body">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-type-h1 me-2"></i>Page Title Feedback
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                `;

                // Handle title feedback (array format)
                if (Array.isArray(data.title_feedback)) {
                    data.title_feedback.forEach(item => {
                        const iconClass = item.score >= 0.8 ? 'bi-check-circle-fill text-success' :
                                        (item.score >= 0.5 ? 'bi-exclamation-triangle-fill text-warning' : 'bi-x-circle-fill text-danger');

                        const criteriaName = item.criteria.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

                        resultsHTML += `
                        <li class="list-group-item bg-transparent px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi ${iconClass} fs-4 me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>${criteriaName}:</strong> ${item.description}
                                    ${item.actual ? `<div class="text-muted small">Current: ${item.actual}</div>` : ''}
                                    ${item.recommended ? `<div class="text-success small">Recommended: ${item.recommended}</div>` : ''}
                                </div>
                            </div>
                        </li>
                        `;
                    });
                } else if (typeof data.title_feedback === 'string') {
                    // For compatibility with older format (string)
                    const feedbackItems = data.title_feedback.split('.').filter(item => item.trim());

                    feedbackItems.forEach(feedbackText => {
                        const isPositive = feedbackText.toLowerCase().includes('great') ||
                                            feedbackText.toLowerCase().includes('good') ||
                                            feedbackText.toLowerCase().includes('perfect');
                        const iconClass = isPositive ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning';

                        resultsHTML += `
                        <li class="list-group-item bg-transparent px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi ${iconClass} fs-4 me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <span>${feedbackText.trim()}</span>
                                </div>
                            </div>
                        </li>
                        `;
                    });
                } else {
                    resultsHTML += `
                    <li class="list-group-item bg-transparent px-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-info fs-4 me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span>No detailed feedback available for title</span>
                            </div>
                        </div>
                    </li>
                    `;
                }

                resultsHTML += `
                        </ul>
                    </div>
                </div>
                `;

                // Meta description feedback
                resultsHTML += `
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-body">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-card-text me-2"></i>Meta Description Feedback
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                `;

                // Handle meta description feedback (array format)
                if (Array.isArray(data.meta_feedback)) {
                    data.meta_feedback.forEach(item => {
                        const iconClass = item.score >= 0.8 ? 'bi-check-circle-fill text-success' :
                                        (item.score >= 0.5 ? 'bi-exclamation-triangle-fill text-warning' : 'bi-x-circle-fill text-danger');

                        const criteriaName = item.criteria.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

                        resultsHTML += `
                        <li class="list-group-item bg-transparent px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi ${iconClass} fs-4 me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>${criteriaName}:</strong> ${item.description}
                                    ${item.actual ? `<div class="text-muted small">Current: ${item.actual}</div>` : ''}
                                    ${item.recommended ? `<div class="text-success small">Recommended: ${item.recommended}</div>` : ''}
                                </div>
                            </div>
                        </li>
                        `;
                    });
                } else if (typeof data.meta_feedback === 'string') {
                    // For compatibility with older format (string)
                    const feedbackItems = data.meta_feedback.split('.').filter(item => item.trim());

                    feedbackItems.forEach(feedbackText => {
                        const isPositive = feedbackText.toLowerCase().includes('great') ||
                                            feedbackText.toLowerCase().includes('good') ||
                                            feedbackText.toLowerCase().includes('perfect');
                        const iconClass = isPositive ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning';

                        resultsHTML += `
                        <li class="list-group-item bg-transparent px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi ${iconClass} fs-4 me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <span>${feedbackText.trim()}</span>
                                </div>
                            </div>
                        </li>
                        `;
                    });
                } else {
                    resultsHTML += `
                    <li class="list-group-item bg-transparent px-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-info fs-4 me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span>No detailed feedback available for meta description</span>
                            </div>
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
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-body">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-file-text me-2"></i>Content Feedback
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                `;

                // Handle content feedback (array format)
                if (Array.isArray(data.content_feedback)) {
                    data.content_feedback.forEach(item => {
                        const iconClass = item.score >= 0.8 ? 'bi-check-circle-fill text-success' :
                                        (item.score >= 0.5 ? 'bi-exclamation-triangle-fill text-warning' : 'bi-x-circle-fill text-danger');

                        const criteriaName = item.criteria.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

                        resultsHTML += `
                        <li class="list-group-item bg-transparent px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi ${iconClass} fs-4 me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>${criteriaName}:</strong> ${item.description}
                                    ${item.actual ? `<div class="text-muted small">Current: ${item.actual}</div>` : ''}
                                    ${item.recommended ? `<div class="text-success small">Recommended: ${item.recommended}</div>` : ''}
                                </div>
                            </div>
                        </li>
                        `;
                    });
                } else if (typeof data.content_feedback === 'string') {
                    // For compatibility with older format (string)
                    const feedbackItems = data.content_feedback.split('.').filter(item => item.trim());

                    feedbackItems.forEach(feedbackText => {
                        const isPositive = feedbackText.toLowerCase().includes('great') ||
                                            feedbackText.toLowerCase().includes('good');
                        const iconClass = isPositive ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning';

                        resultsHTML += `
                        <li class="list-group-item bg-transparent px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi ${iconClass} fs-4 me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <span>${feedbackText.trim()}</span>
                                </div>
                            </div>
                        </li>
                        `;
                    });
                } else {
                    resultsHTML += `
                    <li class="list-group-item bg-transparent px-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-info fs-4 me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span>No detailed feedback available for content</span>
                            </div>
                        </div>
                    </li>
                    `;
                }

                resultsHTML += `
                        </ul>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success" id="save-button">
                        <i class="bi bi-check-lg me-1"></i> Save Analysis
                    </button>
                </div>
                `;

                document.getElementById('results').innerHTML = resultsHTML;

                // Initialize progress circles with improved implementation
                document.querySelectorAll('.progress-circle').forEach(function(el) {
                    const value = parseInt(el.getAttribute('data-value'));

                    // Get circle bars
                    const leftBar = el.querySelector('.progress-circle-left .progress-circle-bar');
                    const rightBar = el.querySelector('.progress-circle-right .progress-circle-bar');

                    // Reset transform values first
                    leftBar.style.transform = 'rotate(0deg)';
                    rightBar.style.transform = 'rotate(0deg)';

                    // Apply rotation based on percentage value
                    setTimeout(() => {
                        if (value <= 50) {
                            // Only right side rotates for values <= 50%
                            rightBar.style.transform = 'rotate(' + (value * 3.6) + 'deg)';
                        } else {
                            // Right side rotates fully, left side fills remaining
                            rightBar.style.transform = 'rotate(180deg)';
                            setTimeout(() => {
                                leftBar.style.transform = 'rotate(' + ((value - 50) * 3.6) + 'deg)';
                            }, 100);
                        }
                    }, 50);
                });

                // Add a clean event listener to the save button (without duplicates)
                const saveButton = document.getElementById('save-button');
                if (saveButton) {
                    // Remove all existing event listeners by cloning the button
                    const newSaveButton = saveButton.cloneNode(true);
                    saveButton.parentNode.replaceChild(newSaveButton, saveButton);

                    // Add a single event listener
                    newSaveButton.addEventListener('click', function() {
                        saveContentToDB();
                    });
                }
            }
        });

        // Prevent any form submission to avoid duplicates
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('seoAnalysisForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Preventing form submission');
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
            }
        });

        // Function to save content to the database
        function saveContentToDB() {
            console.log('saveContentToDB called');

            // Get content from Quill editor and update hidden field
            const quillContent = document.querySelector('.ql-editor').innerHTML;
            document.getElementById('content').value = quillContent;

            // Check if content is empty
            if (!quillContent || quillContent === '<p><br></p>') {
                showError('Content cannot be empty');
                return;
            }

            // Show loading state
            const saveButton = document.getElementById('save-button');
            if (!saveButton) {
                console.error('Save button not found');
                return;
            }

            // Prevent multiple submissions
            if (saveButton.disabled) {
                console.log('Button already disabled, preventing duplicate submission');
                return;
            }

            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Saving...';
            saveButton.disabled = true;

            // Create form data
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('title', document.getElementById('title').value);
            formData.append('meta_description', document.getElementById('meta_description').value);
            formData.append('target_keyword', document.getElementById('target_keyword').value);
            formData.append('content', quillContent);

            // Log what's being sent
            console.log('Saving to database with content length:', quillContent.length);

            // Add a unique identifier to track this specific save request
            const requestId = Date.now().toString();
            formData.append('request_id', requestId);
            console.log('Request ID:', requestId);

            // Submit to server
            fetch('{{ route('contents.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                if (data.success && data.redirect) {
                    // Redirect to results page
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Error saving content:', error);
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;

                let errorMsg = 'Error saving content';
                if (error.data && error.data.message) {
                    errorMsg = error.data.message;
                } else if (error.message) {
                    errorMsg = error.message;
                }

                showError(errorMsg);
            });
        }
    </script>
    <style>

    </style>
@endsection

