@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Profile Settings</h1>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('verification_code'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        Your verification code for {{ session('code_type') }} is: <strong>{{ session('verification_code') }}</strong>
        <p class="mb-0 small">In a real application, this would be sent via WhatsApp.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 32px;">
                            <span>{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <h5 class="mt-3">{{ $user->name }}</h5>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        @if($user->phone_number)
                            <p class="text-muted mb-0">{{ $user->phone_number }}</p>
                        @else
                            <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle"></i> No phone number registered</p>
                        @endif
                    </div>
                    
                    <div class="list-group">
                        <a href="#name-section" class="list-group-item list-group-item-action">
                            <i class="bi bi-person"></i> Change Name
                        </a>
                        <a href="#email-section" class="list-group-item list-group-item-action">
                            <i class="bi bi-envelope"></i> Change Email
                        </a>
                        <a href="#phone-section" class="list-group-item list-group-item-action">
                            <i class="bi bi-phone"></i> Change Phone Number
                        </a>
                        <a href="#password-section" class="list-group-item list-group-item-action">
                            <i class="bi bi-lock"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- Change Name Section -->
            <div class="card mb-4" id="name-section">
                <div class="card-header">
                    <h5 class="mb-0">Change Name</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update.name') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update Name</button>
                    </form>
                </div>
            </div>
            
            <!-- Change Email Section -->
            <div class="card mb-4" id="email-section">
                <div class="card-header">
                    <h5 class="mb-0">Change Email</h5>
                </div>
                <div class="card-body">
                    @if(!$user->phone_number)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> You need to add a phone number before you can update your email with WhatsApp verification.
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.update.email') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">New Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="verification_code" class="form-label">WhatsApp Verification Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('verification_code') is-invalid @enderror" id="verification_code" name="verification_code" required>
                                <button type="button" class="btn btn-outline-primary" onclick="requestVerificationCode('email')" {{ !$user->phone_number ? 'disabled' : '' }}>
                                    Request Code
                                </button>
                            </div>
                            @error('verification_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">We'll send a verification code to your WhatsApp number.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" {{ !$user->phone_number ? 'disabled' : '' }}>Update Email</button>
                    </form>
                </div>
            </div>
            
            <!-- Change Phone Section -->
            <div class="card mb-4" id="phone-section">
                <div class="card-header">
                    <h5 class="mb-0">Change Phone Number</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update.phone') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">New Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter without country code (e.g., 62812345678)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="verification_code_phone" class="form-label">WhatsApp Verification Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('verification_code') is-invalid @enderror" id="verification_code_phone" name="verification_code" required>
                                <button type="button" class="btn btn-outline-primary" onclick="requestVerificationCode('phone')" {{ !$user->phone_number ? 'disabled' : '' }}>
                                    Request Code
                                </button>
                            </div>
                            @error('verification_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                @if($user->phone_number)
                                    We'll send a verification code to your current WhatsApp number ({{ $user->phone_number }}).
                                @else
                                    You'll need to add a phone number first to receive verification codes.
                                @endif
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" {{ !$user->phone_number && !old('phone_number') ? 'disabled' : '' }}>Update Phone Number</button>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Section -->
            <div class="card mb-4" id="password-section">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    @if(!$user->phone_number)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> You need to add a phone number before you can update your password with WhatsApp verification.
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.update.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="verification_code_password" class="form-label">WhatsApp Verification Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('verification_code') is-invalid @enderror" id="verification_code_password" name="verification_code" required>
                                <button type="button" class="btn btn-outline-primary" onclick="requestVerificationCode('password')" {{ !$user->phone_number ? 'disabled' : '' }}>
                                    Request Code
                                </button>
                            </div>
                            @error('verification_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">We'll send a verification code to your WhatsApp number.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" {{ !$user->phone_number ? 'disabled' : '' }}>Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function requestVerificationCode(type) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('profile.verification.request') }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add type field
        const typeField = document.createElement('input');
        typeField.type = 'hidden';
        typeField.name = 'type';
        typeField.value = type;
        form.appendChild(typeField);
        
        // Show loading state on button
        const button = document.querySelector(`button[onclick="requestVerificationCode('${type}')"]`);
        if (button) {
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            
            // Reset button after 5 seconds if no response
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 5000);
        }
        
        // Append form to body and submit
        document.body.appendChild(form);
        form.submit();
    }
    
    // Scroll to section if hash exists in URL
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash) {
            const section = document.querySelector(window.location.hash);
            if (section) {
                section.scrollIntoView();
            }
        }
    });
</script>
@endsection 