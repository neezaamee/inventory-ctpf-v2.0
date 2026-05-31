<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-person-badge-fill me-1 text-success"></i> My Profile & Account Settings
            </h1>
            <p class="text-muted small mb-0">Update your profile parameters, verify your assigned system role, and change your password.</p>
        </div>
        <div>
            <span class="badge badge-ctpf-emerald py-2 px-3 shadow-sm rounded-pill">
                <i class="bi bi-shield-lock me-1 text-warning"></i> Role: {{ auth()->user()->roles->first()?->name ?? 'User' }}
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Edit Profile Details Card -->
        <div class="col-lg-6 mb-4">
            <div class="card ctpf-card shadow-sm h-100 mb-0">
                <div class="ctpf-card-header"><i class="bi bi-person-fill-gear me-1"></i> Personal Profile Information</div>
                <div class="card-body py-4">
                    @if(auth()->user()->staff)
                        <div class="alert alert-light border border-success-subtle p-3 rounded mb-4 d-flex align-items-center" style="background-color: #f6faf7;">
                            <div class="p-2 bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: var(--ctpf-emerald) !important;">
                                <i class="bi bi-person-vcard fs-4"></i>
                            </div>
                            <div>
                                <small class="text-uppercase text-muted fw-bold font-monospace d-block" style="font-size: 0.7rem;">Official Warden Credentials</small>
                                <h6 class="fw-bold text-success-emphasis mb-0">{{ auth()->user()->staff->rank }} (Belt #{{ auth()->user()->staff->belt_no }})</h6>
                                <small class="text-secondary d-block mt-0.5" style="font-size: 0.8rem;">
                                    <strong>Posting Location:</strong> {{ auth()->user()->staff->current_posting }} <br>
                                    <strong>CNIC:</strong> {{ auth()->user()->staff->cnic }} • <strong>Phone:</strong> {{ auth()->user()->staff->phone_no }}
                                </small>
                            </div>
                        </div>
                    @endif
                    <form wire:submit.prevent="updateProfile">
                        <div class="mb-3">
                            <label for="profile_name" class="form-label fw-semibold small text-muted">Administrator Name</label>
                            <input wire:model="name" type="text" id="profile_name" class="form-control border-secondary-subtle @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="profile_email" class="form-label fw-semibold small text-muted">Email Address (Login Username)</label>
                            <input wire:model="email" type="email" id="profile_email" class="form-control border-secondary-subtle @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-save2-fill me-1 text-warning"></i> Save Personal Details
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Secure Password Update Card -->
        <div class="col-lg-6 mb-4">
            <div class="card ctpf-card shadow-sm h-100 mb-0">
                <div class="ctpf-card-header"><i class="bi bi-key-fill me-1"></i> Security & Password Management</div>
                <div class="card-body py-4">
                    <form wire:submit.prevent="updatePassword">
                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold small text-muted">Current Login Password</label>
                            <input wire:model="current_password" type="password" id="current_password" class="form-control border-secondary-subtle @error('current_password') is-invalid @enderror" placeholder="Enter your current password" required>
                            @error('current_password') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-semibold small text-muted">New Secure Password</label>
                            <input wire:model="new_password" type="password" id="new_password" class="form-control border-secondary-subtle @error('new_password') is-invalid @enderror" placeholder="Minimum 6 characters" required>
                            @error('new_password') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-semibold small text-muted">Confirm New Password</label>
                            <input wire:model="new_password_confirmation" type="password" id="new_password_confirmation" class="form-control border-secondary-subtle" placeholder="Re-type new password" required>
                        </div>

                        <button type="submit" class="btn btn-danger fw-bold px-4 shadow-sm border-0" style="background-color: #dc3545;">
                            <i class="bi bi-shield-check me-1 text-warning"></i> Update Password Credentials
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
