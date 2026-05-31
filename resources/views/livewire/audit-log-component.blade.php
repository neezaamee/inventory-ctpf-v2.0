<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-shield-lock-fill me-1 text-success"></i> System Audit Trail & Logs
            </h1>
            <p class="text-muted small mb-0">Chorological security registry tracking all database mutations, registry edits, and store approvals.</p>
        </div>
        <span class="badge bg-dark py-2 px-3 shadow-sm rounded-pill font-monospace">
            <i class="bi bi-fingerprint me-1 text-warning"></i> Audit Engine Active
        </span>
    </div>

    <!-- Filters & Search Bar Card -->
    <div class="card ctpf-card p-3 mb-4">
        <div class="row g-3">
            <!-- Search Bar -->
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">Search Logs</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control border-secondary-subtle" placeholder="Search by User, IP Address, auditable model...">
                </div>
            </div>

            <!-- Event Filter -->
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Filter by Event</label>
                <select wire:model.live="filterEvent" class="form-select border-secondary-subtle">
                    <option value="">-- All Events --</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="deleted">Deleted</option>
                </select>
            </div>

            <!-- Model Filter -->
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Filter by Model</label>
                <select wire:model.live="filterModel" class="form-select border-secondary-subtle">
                    <option value="">-- All Components --</option>
                    @foreach($distinctModels as $m)
                        <option value="{{ $m['class'] }}">{{ $m['short'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Audits Trail Table Card -->
    <div class="card ctpf-card">
        <div class="card-body p-0">
            @if($audits->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-fingerprint fs-1 mb-3 d-block text-secondary"></i>
                    <span class="fw-semibold small">No activity logs recorded matching the criteria.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 border-0">Timestamp</th>
                                <th class="border-0">User / Officer</th>
                                <th class="border-0 text-center">Event</th>
                                <th class="border-0">Target Component</th>
                                <th class="border-0">IP Address</th>
                                <th class="border-0 text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audits as $audit)
                                <tr>
                                    <td class="ps-3 text-secondary small">{{ $audit->created_at->format('d M, Y H:i:s') }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $audit->user->name ?? 'System Command' }}</div>
                                        <small class="text-muted small">{{ $audit->user->email ?? 'Cron Job' }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($audit->event === 'created')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 text-uppercase small">Created</span>
                                        @elseif($audit->event === 'updated')
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 text-uppercase small">Updated</span>
                                        @elseif($audit->event === 'deleted')
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 text-uppercase small">Deleted</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 text-uppercase small">{{ $audit->event }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ class_basename($audit->auditable_type) }}</div>
                                        <small class="text-muted font-monospace small">ID: {{ $audit->auditable_id }}</small>
                                    </td>
                                    <td class="font-monospace text-secondary small">{{ $audit->ip_address }}</td>
                                    <td class="text-end pe-3">
                                        <button wire:click="openAuditDetails({{ $audit->id }})" class="btn btn-sm btn-light border" title="Inspect Mutations">
                                            <i class="bi bi-search text-primary"></i> Inspect
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Footer -->
                <div class="p-3 border-top bg-light">
                    {{ $audits->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- AUDIT DETAIL MUTATION COMPARISON MODAL -->
    @if($isDetailModalOpen && $activeAudit)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-shield-check me-1"></i>
                            Inspect Mutation - ID #{{ $activeAudit->id }}
                        </h5>
                        <button wire:click="closeAuditDetails" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body py-4" style="max-height: 75vh; overflow-y: auto;">
                        <!-- Master Metadata Panel -->
                        <div class="row bg-light p-3 rounded border mb-4 g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block text-uppercase font-monospace small">Operator</small>
                                <span class="fw-bold text-dark">{{ $activeAudit->user->name ?? 'System / CLI' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block text-uppercase font-monospace small">Action Event</small>
                                <span class="badge bg-dark rounded-pill px-3 text-uppercase mt-1">{{ $activeAudit->event }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block text-uppercase font-monospace small">Target Record</small>
                                <span class="fw-semibold text-dark">{{ class_basename($activeAudit->auditable_type) }} (ID: {{ $activeAudit->auditable_id }})</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block text-uppercase font-monospace small">IP Address</small>
                                <span class="font-monospace text-secondary">{{ $activeAudit->ip_address }}</span>
                            </div>
                            <div class="col-md-8">
                                <small class="text-muted d-block text-uppercase font-monospace small">User Agent</small>
                                <span class="text-secondary small">{{ $activeAudit->user_agent }}</span>
                            </div>
                        </div>

                        <!-- Mutations Property Comparison Grid -->
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-arrow-left-right me-1 text-warning"></i> Property Mutations:</h6>
                        
                        @if(empty($activeAudit->old_values) && empty($activeAudit->new_values))
                            <div class="alert alert-secondary text-center small py-3" role="alert">
                                No attribute changes logged for this transaction.
                            </div>
                        @else
                            <div class="table-responsive border rounded">
                                <table class="table table-striped align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 border-0">Attribute Property</th>
                                            <th class="border-0 text-danger">Old Value</th>
                                            <th class="border-0 text-success">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Unified properties extractor -->
                                        @php
                                            $allKeys = array_unique(array_merge(
                                                array_keys($activeAudit->old_values ?? []),
                                                array_keys($activeAudit->new_values ?? [])
                                            ));
                                        @endphp
                                        
                                        @foreach($allKeys as $key)
                                            <!-- Skip password and remember token hashes for security reasons -->
                                            @if(in_array($key, ['password', 'remember_token']))
                                                @continue
                                            @endif
                                            <tr>
                                                <td class="ps-3 fw-bold text-secondary font-monospace">{{ $key }}</td>
                                                <td class="text-danger-emphasis text-break">
                                                    {{ array_key_exists($key, $activeAudit->old_values ?? []) ? var_export($activeAudit->old_values[$key], true) : 'N/A' }}
                                                </td>
                                                <td class="text-success-emphasis text-break fw-bold">
                                                    {{ array_key_exists($key, $activeAudit->new_values ?? []) ? var_export($activeAudit->new_values[$key], true) : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light">
                        <button wire:click="closeAuditDetails" type="button" class="btn btn-ctpf-primary px-4 fw-bold">Close Inspection</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
