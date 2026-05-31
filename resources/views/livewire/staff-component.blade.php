<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-people-fill me-1 text-success"></i> Warden & Officer Registry
            </h1>
            <p class="text-muted small mb-0">Manage personnel directory, postings, CNIC numbers, and structural statuses.</p>
        </div>
        @can('manage-staff')
        <div class="d-flex gap-2">
            <button wire:click="openImport" class="btn btn-ctpf-outline fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Bulk CSV Import
            </button>
            <button wire:click="openForm" class="btn btn-ctpf-primary fw-bold">
                <i class="bi bi-person-plus-fill me-1"></i> Register New Officer
            </button>
        </div>
        @endcan
    </div>

    <!-- Filters & Search Bar Card -->
    <div class="card ctpf-card p-3 mb-4">
        <div class="row g-3">
            <!-- Search Bar -->
            <div class="col-md-5">
                <label class="form-label fw-bold small text-muted">Global Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control border-secondary-subtle" placeholder="Search by Belt No, Name, or CNIC...">
                </div>
            </div>

            <!-- Rank Filter -->
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Filter by Rank</label>
                <select wire:model.live="filterRank" class="form-select border-secondary-subtle">
                    <option value="">-- All Ranks --</option>
                    @foreach($ranks as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Filter by Status</label>
                <select wire:model.live="filterStatus" class="form-select border-secondary-subtle">
                    <option value="">-- All Statuses --</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="retired">Retired</option>
                    <option value="transferred">Transferred</option>
                </select>
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button wire:click="$set('search', ''); $set('filterRank', ''); $set('filterStatus', '');" class="btn btn-outline-secondary w-100" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Staff Directory Data Table Card -->
    <div class="card ctpf-card">
        <div class="card-body p-0">
            @if($staffList->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-people fs-1 mb-3 d-block text-secondary"></i>
                    <span class="fw-semibold small">No registered CTPF officers match the search criteria. Click 'Register New Officer' to insert a record.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 border-0">Belt #</th>
                                <th class="border-0">Full Name</th>
                                <th class="border-0">CNIC</th>
                                <th class="border-0">Rank</th>
                                <th class="border-0">Posting Location</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffList as $staff)
                                <tr>
                                    <td class="ps-3 fw-bold text-success-emphasis">{{ $staff->belt_no }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $staff->full_name }}</div>
                                        <small class="text-muted text-capitalize small"><i class="bi bi-telephone-fill me-1" style="font-size: 0.75rem;"></i>{{ $staff->phone_no }} • {{ $staff->gender }}</small>
                                    </td>
                                    <td class="font-monospace text-secondary">{{ $staff->cnic }}</td>
                                    <td><span class="badge bg-light text-dark border fw-bold">{{ $staff->rank }}</span></td>
                                    <td class="text-secondary small">{{ $staff->current_posting }}</td>
                                    <td>
                                        @if($staff->status === 'active')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">Active</span>
                                        @elseif($staff->status === 'suspended')
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 small">Suspended</span>
                                        @elseif($staff->status === 'retired')
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">Retired</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1 small">Transferred</span>
                                        @endif
                                    </td>
                                     <td class="text-end pe-3">
                                         @can('manage-staff')
                                         <button wire:click="openForm({{ $staff->id }})" class="btn btn-sm btn-light border me-1" title="Edit Warden">
                                             <i class="bi bi-pencil-fill text-primary"></i>
                                         </button>
                                         <button wire:click="confirmDeletion({{ $staff->id }})" class="btn btn-sm btn-light border" title="Delete Warden">
                                             <i class="bi bi-trash-fill text-danger"></i>
                                         </button>
                                         @else
                                         <span class="text-muted small">Read-Only</span>
                                         @endcan
                                     </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Footer -->
                <div class="p-3 border-top bg-light">
                    {{ $staffList->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Registration/Edit Modal Form Overlay -->
    @if($isFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-person-fill-gear me-1"></i>
                            {{ $staffId ? 'Update Warden Profile' : 'Register New Wardi Officer' }}
                        </h5>
                        <button wire:click="closeForm" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    
                    <form wire:submit.prevent="saveStaff">
                        <div class="modal-body py-4">
                            <div class="row g-3">
                                <!-- Belt Number -->
                                <div class="col-md-6">
                                    <label for="belt_no" class="form-label fw-semibold small text-muted">Belt Number (Unique ID)</label>
                                    <input wire:model="belt_no" type="text" id="belt_no" class="form-control border-secondary-subtle @error('belt_no') is-invalid @enderror" placeholder="e.g. 542" required>
                                    @error('belt_no') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- CNIC Number -->
                                <div class="col-md-6">
                                    <label for="cnic" class="form-label fw-semibold small text-muted">CNIC (With Dashes)</label>
                                    <input wire:model="cnic" type="text" id="cnic" class="form-control border-secondary-subtle @error('cnic') is-invalid @enderror" placeholder="e.g. 33100-1234567-1" required>
                                    @error('cnic') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- First Name -->
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-semibold small text-muted">First Name</label>
                                    <input wire:model="first_name" type="text" id="first_name" class="form-control border-secondary-subtle @error('first_name') is-invalid @enderror" required>
                                    @error('first_name') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-semibold small text-muted">Last Name</label>
                                    <input wire:model="last_name" type="text" id="last_name" class="form-control border-secondary-subtle @error('last_name') is-invalid @enderror" required>
                                    @error('last_name') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Designation / Rank -->
                                <div class="col-md-6">
                                    <label for="rank" class="form-label fw-semibold small text-muted">Service Rank</label>
                                    <select wire:model="rank" id="rank" class="form-select border-secondary-subtle @error('rank') is-invalid @enderror" required>
                                        <option value="">-- Select Rank --</option>
                                        <option value="Traffic Warden">Traffic Warden</option>
                                        <option value="Senior Traffic Warden">Senior Traffic Warden</option>
                                        <option value="Traffic Inspector">Traffic Inspector</option>
                                        <option value="DSP Traffic">DSP Traffic</option>
                                        <option value="Admin Staff">Admin Staff</option>
                                    </select>
                                    @error('rank') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Gender -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Gender</label>
                                    <div class="d-flex gap-3 py-2">
                                        <div class="form-check">
                                            <input wire:model="gender" type="radio" class="form-check-input" id="gender_male" value="male">
                                            <label class="form-check-label text-dark fw-medium small" for="gender_male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input wire:model="gender" type="radio" class="form-check-input" id="gender_female" value="female">
                                            <label class="form-check-label text-dark fw-medium small" for="gender_female">Female</label>
                                        </div>
                                    </div>
                                    @error('gender') <div class="text-danger fw-semibold small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <!-- Contact Number -->
                                <div class="col-md-6">
                                    <label for="phone_no" class="form-label fw-semibold small text-muted">Phone / Contact Number</label>
                                    <input wire:model="phone_no" type="text" id="phone_no" class="form-control border-secondary-subtle @error('phone_no') is-invalid @enderror" placeholder="e.g. 0300-1234567" required>
                                    @error('phone_no') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-semibold small text-muted">Active Duty Status</label>
                                    <select wire:model="status" id="status" class="form-select border-secondary-subtle @error('status') is-invalid @enderror" required>
                                        <option value="active">Active</option>
                                        <option value="suspended">Suspended</option>
                                        <option value="retired">Retired</option>
                                        <option value="transferred">Transferred</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Posting Circle -->
                                <div class="col-12">
                                    <label for="current_posting" class="form-label fw-semibold small text-muted">Current Circle Posting / Sector</label>
                                    <input wire:model="current_posting" type="text" id="current_posting" class="form-control border-secondary-subtle @error('current_posting') is-invalid @enderror" placeholder="e.g. Kohinoor Circle, Faisalabad" required>
                                    @error('current_posting') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button wire:click="closeForm" type="button" class="btn btn-outline-secondary fw-semibold">Cancel</button>
                            <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold">
                                <i class="bi bi-check-circle-fill me-1 text-warning"></i>
                                {{ $staffId ? 'Update Record' : 'Save Warden' }}
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
                        <h5 class="fw-bold text-dark mb-2">Delete Warden Profile?</h5>
                        <p class="text-muted small mb-0 px-2">This will soft-delete the Warden record from the system. Sizing logs and historical distribution data will be archived.</p>
                    </div>
                    <div class="modal-footer bg-light justify-content-center border-0 py-3">
                        <button wire:click="cancelDeletion" class="btn btn-secondary btn-sm px-3 fw-bold me-2">Abort</button>
                        <button wire:click="deleteStaff" class="btn btn-danger btn-sm px-3 fw-bold">Confirm Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Import Modal Overlay -->
    @if($isImportOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 1065;">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>
                            Bulk Import Warden Profiles
                        </h5>
                        <button wire:click="closeImport" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    
                    <form wire:submit.prevent="importStaff">
                        <div class="modal-body py-4">
                            <div class="alert alert-info border-start border-4 border-info small mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                <strong>Required CSV Columns:</strong><br>
                                <code class="text-dark fw-bold">belt_no, cnic, first_name, last_name, rank, gender, phone_no, current_posting</code>
                            </div>

                            <div class="mb-3">
                                <label for="csvFile" class="form-label fw-bold small text-muted">Select CSV Data File</label>
                                <input wire:model="csvFile" type="file" id="csvFile" class="form-control border-secondary-subtle @error('csvFile') is-invalid @enderror" required>
                                @error('csvFile') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div wire:loading wire:target="csvFile" class="text-warning small fw-bold mb-2">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Uploading and analyzing file...
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button wire:click="closeImport" type="button" class="btn btn-outline-secondary fw-semibold">Cancel</button>
                            <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold" wire:loading.attr="disabled">
                                <i class="bi bi-upload me-1 text-warning"></i> Start Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
