<div class="auth-card">
    <div class="auth-logo">
        <i class="bi bi-shield-lock-fill"></i>
        <div class="auth-title text-white">City Traffic Police</div>
        <div class="auth-subtitle text-warning">FAISALABAD • WARDI GODAAM</div>
    </div>

    <form wire:submit.prevent="authenticate">
        <!-- Email Input -->
        <div class="mb-4">
            <label for="email" class="form-label-ctpf">
                <i class="bi bi-envelope-fill me-1 text-warning"></i> Departmental Email
            </label>
            <input 
                wire:model="email" 
                type="email" 
                id="email" 
                class="form-control form-control-ctpf @error('email') is-invalid @enderror" 
                placeholder="e.g. name@ctpf.gov.pk" 
                required 
                autocomplete="username"
                autofocus
            >
            @error('email')
                <div class="invalid-feedback text-warning fw-bold mt-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password Input -->
        <div class="mb-4">
            <label for="password" class="form-label-ctpf">
                <i class="bi bi-key-fill me-1 text-warning"></i> System Password
            </label>
            <input 
                wire:model="password" 
                type="password" 
                id="password" 
                class="form-control form-control-ctpf @error('password') is-invalid @enderror" 
                placeholder="••••••••" 
                required 
                autocomplete="current-password"
            >
            @error('password')
                <div class="invalid-feedback text-warning fw-bold mt-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me Checkbox -->
        <div class="mb-4 form-check">
            <input 
                wire:model="remember" 
                type="checkbox" 
                class="form-check-input bg-dark border-secondary" 
                id="remember"
            >
            <label class="form-check-label form-label-ctpf" for="remember">
                Keep me logged in
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-ctpf-login py-2 fw-bold w-100">
            <span wire:loading.remove>
                <i class="bi bi-box-arrow-in-right me-1"></i> Authenticate & Enter
            </span>
            <span wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </button>
    </form>

    <div class="auth-footer text-center">
        &copy; {{ date('Y') }} City Traffic Police Faisalabad. <br>
        Developed & Managed by IT Division. Secure Access Only.
    </div>
</div>
