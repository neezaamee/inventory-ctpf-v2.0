<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-shield-lock-fill me-1 text-success"></i> System User Management
            </h1>
            <p class="text-muted small mb-0">Control dashboard access, reset administrator credentials, and configure role assignments.</p>
        </div>
        <button wire:click="openForm" class="btn btn-ctpf-primary fw-bold">
            <i class="bi bi-person-plus-fill me-1"></i> Register New System User
        </button>
    </div>

    <!-- Search Card -->
    <div class="card ctpf-card p-3 mb-4">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold small text-muted">Search System Users</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control border-secondary-subtle" placeholder="Search by name or email address...">
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card ctpf-card">
        <div class="card-body p-0">
            @if($usersList->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-shield-slash fs-1 mb-3 d-block text-secondary"></i>
                    <span class="fw-semibold small">No system users match the search parameters.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 border-0">Administrator Name</th>
                                <th class="border-0">Email Address</th>
                                <th class="border-0">Assigned System Role</th>
                                <th class="border-0 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usersList as $user)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-success-subtle text-success fw-bold rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <span>{{ $user->name }}</span>
                                                @if($user->staff)
                                                    <small class="text-muted d-block small" style="font-size: 0.75rem; font-weight: 500;">
                                                        <i class="bi bi-person-badge text-success me-1"></i> Linked Profile: Belt {{ $user->staff->belt_no }} ({{ $user->staff->rank }})
                                                    </small>
                                                @endif
                                                @if(auth()->id() == $user->id)
                                                    <span class="badge bg-success rounded-pill ms-1 small" style="font-size: 0.7rem;">Active Session</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-secondary small font-monospace">{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge {{ $role->name === 'Super Admin' || $role->name === 'Admin' ? 'badge-ctpf-emerald' : 'badge-ctpf-gold' }} rounded-pill px-2.5 py-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="text-end pe-3">
                                        <button wire:click="openForm({{ $user->id }})" class="btn btn-sm btn-light border me-1" title="Edit Credentials">
                                            <i class="bi bi-pencil-fill text-primary"></i> Edit
                                        </button>
                                        @if(auth()->id() != $user->id)
                                            <button wire:click="confirmDeletion({{ $user->id }})" class="btn btn-sm btn-light border" title="Remove User">
                                                <i class="bi bi-trash-fill text-danger"></i> Remove
                                            </button>
                                        @else
                                            <span class="text-muted small pe-2">Locked</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Footer -->
                <div class="p-3 border-top bg-light">
                    {{ $usersList->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Registration/Edit Modal Form Overlay -->
    @if($isFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-person-fill-gear me-1"></i>
                            {{ $userId ? 'Modify Account Details' : 'Register New System Administrator' }}
                        </h5>
                        <button wire:click="closeForm" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    
                    <form wire:submit.prevent="saveUser">
                        <div class="modal-body py-4">
                            <div class="row g-3">
                                <!-- User Name -->
                                <div class="col-12">
                                    <label for="name" class="form-label fw-semibold small text-muted">Full Name</label>
                                    <input wire:model="name" type="text" id="name" class="form-control border-secondary-subtle @error('name') is-invalid @enderror" placeholder="e.g. Sub-Inspector Yasir" required>
                                    @error('name') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-12">
                                    <label for="email" class="form-label fw-semibold small text-muted">Email Address (Login Username)</label>
                                    <input wire:model="email" type="email" id="email" class="form-control border-secondary-subtle @error('email') is-invalid @enderror" placeholder="username@ctpf.gov.pk" required>
                                    @error('email') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-12">
                                    <label for="password" class="form-label fw-semibold small text-muted">
                                        Password {{ $userId ? '(Leave empty to keep existing password)' : '' }}
                                    </label>
                                    <input wire:model="password" type="password" id="password" class="form-control border-secondary-subtle @error('password') is-invalid @enderror" placeholder="Minimum 6 characters" {{ $userId ? '' : 'required' }}>
                                    @error('password') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Spatie Role Assign -->
                                <div class="col-12">
                                    <label for="selectedRole" class="form-label fw-semibold small text-muted">Assigned System Role</label>
                                    <select wire:model="selectedRole" id="selectedRole" class="form-select border-secondary-subtle @error('selectedRole') is-invalid @enderror" required>
                                        <option value="">-- Assign Role --</option>
                                        @foreach($rolesList as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedRole') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Link to Warden Staff Profile -->
                                <div class="col-12">
                                    <label for="staff_id" class="form-label fw-semibold small text-muted">Link to Warden Staff Profile (Optional)</label>
                                    <select wire:model="staff_id" id="staff_id" class="form-select border-secondary-subtle @error('staff_id') is-invalid @enderror">
                                        <option value="">-- No Link (Pure Administration / Auditor) --</option>
                                        @foreach($availableStaff as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->full_name }} (Belt {{ $staff->belt_no }} • {{ $staff->rank }})</option>
                                        @endforeach
                                    </select>
                                    @error('staff_id') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button wire:click="closeForm" type="button" class="btn btn-outline-secondary fw-semibold">Cancel</button>
                            <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold">
                                <i class="bi bi-check-circle-fill me-1 text-warning"></i> Save User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Deletion Confirmation Dialog -->
    @if($confirmingDeletionId)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 1070;">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash3-fill text-danger fs-1 mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark mb-2">Delete System Account?</h5>
                        <p class="text-muted small mb-0 px-2">Are you sure you want to remove this active system account? They will lose all access to the CTPF portal instantly.</p>
                    </div>
                    <div class="modal-footer bg-light justify-content-center border-0 py-3">
                        <button wire:click="cancelDeletion" class="btn btn-secondary btn-sm px-3 fw-bold me-2">Abort</button>
                        <button wire:click="deleteUser" class="btn btn-danger btn-sm px-3 fw-bold">Confirm Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
