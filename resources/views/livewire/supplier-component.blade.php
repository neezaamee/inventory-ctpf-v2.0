<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-truck-flatbed me-1 text-success"></i> Suppliers & Logistics Depots
            </h1>
            <p class="text-muted small mb-0">Manage corporate vendors, Punjab Police central depots, NTN numbers, and contract statuses.</p>
        </div>
        @if(auth()->user()->can('manage-suppliers') || auth()->user()->hasRole('Store Incharge'))
        <button wire:click="openForm" class="btn btn-ctpf-primary fw-bold">
            <i class="bi bi-plus-circle-fill me-1"></i> Register New Supplier
        </button>
        @endif
    </div>

    <!-- Filters & Search Bar Card -->
    <div class="card ctpf-card p-3 mb-4">
        <div class="row g-3">
            <!-- Search Bar -->
            <div class="col-md-7">
                <label class="form-label fw-bold small text-muted">Search Registry</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control border-secondary-subtle" placeholder="Search by Company, Contact Person, or NTN Number...">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">Status</label>
                <select wire:model.live="filterStatus" class="form-select border-secondary-subtle">
                    <option value="">-- All Statuses --</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button wire:click="$set('search', ''); $set('filterStatus', '');" class="btn btn-outline-secondary w-100" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Suppliers Table Card -->
    <div class="card ctpf-card">
        <div class="card-body p-0">
            @if($suppliersList->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-truck fs-1 mb-3 d-block text-secondary"></i>
                    <span class="fw-semibold small">No registered suppliers match the criteria. Click 'Register New Supplier' to insert a record.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 border-0">Company / Vendor</th>
                                <th class="border-0">Contact Representative</th>
                                <th class="border-0">Phone</th>
                                <th class="border-0">Email</th>
                                <th class="border-0 font-monospace">NTN Number</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliersList as $supplier)
                                <tr>
                                    <td class="ps-3 fw-bold text-success-emphasis">
                                        <div class="text-dark">{{ $supplier->company_name }}</div>
                                        @if($supplier->address)
                                            <small class="text-muted small text-truncate d-inline-block" style="max-width: 200px;">
                                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $supplier->address }}
                                            </small>
                                        @endif
                                    </td>
                                    <td><span class="fw-semibold text-dark">{{ $supplier->contact_person }}</span></td>
                                    <td class="text-secondary small font-monospace"><i class="bi bi-telephone-fill me-1"></i>{{ $supplier->phone }}</td>
                                    <td class="text-secondary small">{{ $supplier->email ?? 'N/A' }}</td>
                                    <td class="font-monospace text-secondary">{{ $supplier->ntn_number ?? 'N/A' }}</td>
                                    <td>
                                        @if($supplier->status === 'active')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">Active</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 small">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if(auth()->user()->can('manage-suppliers') || auth()->user()->hasRole('Store Incharge'))
                                        <button wire:click="openForm({{ $supplier->id }})" class="btn btn-sm btn-light border me-1" title="Edit Supplier">
                                            <i class="bi bi-pencil-fill text-primary"></i>
                                        </button>
                                        <button wire:click="confirmDeletion({{ $supplier->id }})" class="btn btn-sm btn-light border" title="Delete Supplier">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </button>
                                        @else
                                        <span class="text-muted small">Read-Only</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Footer -->
                <div class="p-3 border-top bg-light">
                    {{ $suppliersList->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Supplier Registration / Edit Modal Form -->
    @if($isFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-truck-flatbed me-1"></i>
                            {{ $supplierId ? 'Update Supplier Profile' : 'Register New Vendor' }}
                        </h5>
                        <button wire:click="closeForm" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    
                    <form wire:submit.prevent="saveSupplier">
                        <div class="modal-body py-4">
                            <div class="row g-3">
                                <!-- Company Name -->
                                <div class="col-12">
                                    <label for="company_name" class="form-label fw-semibold small text-muted">Company / Depot Name</label>
                                    <input wire:model="company_name" type="text" id="company_name" class="form-control border-secondary-subtle @error('company_name') is-invalid @enderror" placeholder="e.g. National Uniform Depot" required>
                                    @error('company_name') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Contact Person -->
                                <div class="col-md-6">
                                    <label for="contact_person" class="form-label fw-semibold small text-muted">Contact Representative</label>
                                    <input wire:model="contact_person" type="text" id="contact_person" class="form-control border-secondary-subtle @error('contact_person') is-invalid @enderror" placeholder="e.g. Yasir Iqbal" required>
                                    @error('contact_person') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold small text-muted">Phone Number</label>
                                    <input wire:model="phone" type="text" id="phone" class="form-control border-secondary-subtle @error('phone') is-invalid @enderror" placeholder="e.g. 042-1234567" required>
                                    @error('phone') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold small text-muted">Email Address [Optional]</label>
                                    <input wire:model="email" type="email" id="email" class="form-control border-secondary-subtle @error('email') is-invalid @enderror" placeholder="e.g. vendor@domain.com">
                                    @error('email') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- NTN Number -->
                                <div class="col-md-6">
                                    <label for="ntn_number" class="form-label fw-semibold small text-muted">NTN / Registration [Optional]</label>
                                    <input wire:model="ntn_number" type="text" id="ntn_number" class="form-control border-secondary-subtle @error('ntn_number') is-invalid @enderror" placeholder="e.g. 1234567-9">
                                    @error('ntn_number') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Active Status -->
                                <div class="col-12">
                                    <label for="status" class="form-label fw-semibold small text-muted">Status</label>
                                    <select wire:model="status" id="status" class="form-select border-secondary-subtle @error('status') is-invalid @enderror" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold small text-muted">Physical Address</label>
                                    <textarea wire:model="address" id="address" rows="2" class="form-control border-secondary-subtle" placeholder="e.g. Plot #54, Industrial Area, Faisalabad"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button wire:click="closeForm" type="button" class="btn btn-outline-secondary fw-semibold">Cancel</button>
                            <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold">
                                <i class="bi bi-check-circle-fill me-1 text-warning"></i> Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Deletion Confirmation Dialog Overlay -->
    @if($confirmingDeletionId)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 1070;">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash3-fill text-danger fs-1 mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark mb-2">Delete Supplier Profile?</h5>
                        <p class="text-muted small mb-0 px-2">Are you sure you want to delete this supplier profile? Sizing history and related records will be preserved.</p>
                    </div>
                    <div class="modal-footer bg-light justify-content-center border-0 py-3">
                        <button wire:click="cancelDeletion" class="btn btn-secondary btn-sm px-3 fw-bold me-2">Abort</button>
                        <button wire:click="deleteSupplier" class="btn btn-danger btn-sm px-3 fw-bold">Confirm Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
