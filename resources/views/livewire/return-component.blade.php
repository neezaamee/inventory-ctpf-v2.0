<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-arrow-left-right me-1 text-success"></i> Returns, Exchanges & clearances
            </h1>
            <p class="text-muted small mb-0">Process worn-out returns, size exchanges, and compile official Warden Clearance Certificates.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="row">
        <!-- LEFT COLUMN: Warden Lookup & Outstanding Assets -->
        <div class="col-lg-8 mb-4">
            <!-- Warden Registry Lookup Card -->
            <div class="card ctpf-card mb-4">
                <div class="ctpf-card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-search me-1"></i> Warden / Staff Officer Selection</span>
                    @if($selectedWarden)
                        <button wire:click="clearSelectedWarden" class="btn btn-sm btn-danger py-0 fw-bold">Change</button>
                    @endif
                </div>
                <div class="card-body">
                    @if(!$selectedWarden)
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Search by Belt Number, Name or CNIC</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary border-secondary-subtle"><i class="bi bi-search"></i></span>
                                <input 
                                    wire:model.live.debounce.300ms="searchWarden" 
                                    type="text" 
                                    class="form-control border-secondary-subtle" 
                                    placeholder="Search by Belt No (e.g. 542), CNIC..."
                                >
                            </div>
                        </div>

                        <!-- Search Results -->
                        @if(!empty($wardens))
                            <div class="list-group shadow-sm border border-secondary-subtle rounded mb-3">
                                @foreach($wardens as $w)
                                    <button 
                                        wire:click="selectWarden({{ $w->id }})" 
                                        type="button" 
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3"
                                    >
                                        <div>
                                            <div class="fw-bold text-dark">{{ $w->full_name }}</div>
                                            <small class="text-muted">CNIC: {{ $w->cnic }} • Posting: {{ $w->current_posting }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge badge-ctpf-gold px-2 py-1 rounded">{{ $w->rank }}</span>
                                            <small class="d-block text-muted small mt-1 fw-bold">Belt No: {{ $w->belt_no }}</small>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @elseif(strlen($searchWarden) >= 3)
                            <div class="alert alert-warning border-start border-4 border-warning mb-0" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> No active warden matching "{{ $searchWarden }}" registered.
                            </div>
                        @endif
                    @else
                        <!-- Selected Warden Details Block -->
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                <div class="bg-success-subtle text-success rounded-circle p-3 d-inline-block shadow-sm">
                                    <i class="bi bi-person-fill-check fs-1"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block text-uppercase small font-monospace">Full Name</small>
                                        <span class="fw-bold text-dark text-uppercase fs-6">{{ $selectedWarden->full_name }}</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block text-uppercase small font-monospace">Rank & Belt #</small>
                                        <span class="fw-bold text-success-emphasis fs-6">{{ $selectedWarden->rank }} (Belt #{{ $selectedWarden->belt_no }})</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block text-uppercase small font-monospace">CNIC Identifier</small>
                                        <span class="fw-semibold text-dark">{{ $selectedWarden->cnic }}</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block text-uppercase small font-monospace">Current Posting Circle</small>
                                        <span class="fw-semibold text-dark">{{ $selectedWarden->current_posting }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Outstanding Items Table Card -->
            @if($selectedWarden)
                <div class="card ctpf-card">
                    <div class="ctpf-card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-shield-lock-fill me-1"></i> Active Outstanding Logistics Allocations</span>
                        <span class="badge badge-ctpf-emerald px-2 py-1 rounded">{{ count($outstandingItems) }} Items Outstanding</span>
                    </div>
                    <div class="card-body p-0">
                        @if($outstandingItems->isEmpty())
                            <!-- CLEARANCE CERTIFICATE MODE -->
                            <div class="p-5 text-center bg-light border-bottom border-top" id="clearanceCert">
                                <div class="text-center mb-4 pb-2 border-bottom border-2 border-dark" style="max-width: 550px; margin: 0 auto;">
                                    <h5 class="fw-bold mb-0 text-uppercase">City Traffic Police Faisalabad</h5>
                                    <h6 class="fw-semibold text-secondary mb-1">Logistics & Wardi Store Headquarters</h6>
                                    <h6 class="badge bg-success px-3 py-2 text-uppercase font-monospace mt-2 rounded" style="font-size: 0.85rem;">Wardi Clearance Certificate</h6>
                                </div>

                                <div class="my-4 py-3" style="max-width: 550px; margin: 0 auto;">
                                    <p class="text-dark fs-6" style="line-height: 1.8; text-align: justify;">
                                        Certified that <strong>{{ $selectedWarden->rank }} {{ $selectedWarden->full_name }}</strong> bearing 
                                        Belt Number <strong>{{ $selectedWarden->belt_no }}</strong> and CNIC <strong>{{ $selectedWarden->cnic }}</strong> 
                                        has successfully deposited/returned all state-issued uniform accessories, defensive gear, and electronic equipment back to the central Godaam.
                                    </p>
                                    
                                    <!-- Breathtaking stamp visualization -->
                                    <div class="d-inline-block border border-4 border-warning text-warning p-3 my-4 rounded-3 text-uppercase font-monospace fw-bold tracking-wider shadow-sm" style="font-size: 1.15rem; transform: rotate(-5deg); border-style: double !important; letter-spacing: 1.5px; background-color: rgba(212, 175, 55, 0.05);">
                                        <i class="bi bi-patch-check-fill me-1"></i> CLEARED • NO OUTSTANDING LIABILITY
                                    </div>
                                </div>

                                <div class="row pt-4 text-center mt-3" style="max-width: 550px; margin: 0 auto;">
                                    <div class="col-6">
                                        <div class="border-top border-dark pt-2 small font-monospace fw-bold">Store Clerk Signature</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border-top border-dark pt-2 small font-monospace fw-bold">Store Incharge Signature</div>
                                    </div>
                                </div>

                                <div class="text-end mt-4 pt-2">
                                    <button onclick="window.print()" class="btn btn-sm btn-outline-dark fw-bold me-2"><i class="bi bi-printer me-1"></i> Print Clearance</button>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 border-0">Item SKU</th>
                                            <th class="border-0">Catalog Name</th>
                                            <th class="border-0 text-center">Batch No</th>
                                            <th class="border-0 text-center">Issued</th>
                                            <th class="border-0 text-center">Returned</th>
                                            <th class="border-0 text-center">Outstanding</th>
                                            <th class="border-0 text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($outstandingItems as $item)
                                            <tr>
                                                <td class="ps-3 fw-bold text-success-emphasis font-monospace">{{ $item->batch->item->sku }}</td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $item->batch->item->name }}</div>
                                                    <small class="text-muted small">Size: {{ $item->batch->item->size_attribute ?? 'N/A' }} • Slip #{{ $item->issuance->issuance_slip_no }}</small>
                                                </td>
                                                <td class="text-center font-monospace small">{{ $item->batch->batch_number }}</td>
                                                <td class="text-center font-monospace">{{ $item->quantity }}</td>
                                                <td class="text-center font-monospace text-secondary">{{ $item->returned_quantity }}</td>
                                                <td class="text-center font-monospace fw-bold text-danger">{{ $item->active_quantity }}</td>
                                                <td class="text-end pe-3">
                                                    <button wire:click="openReturnForm({{ $item->id }})" class="btn btn-sm btn-outline-success fw-bold py-1">
                                                        <i class="bi bi-arrow-left-right me-1"></i> Return / Swap
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: Operational notes & statistics -->
        <div class="col-lg-4 mb-4">
            <div class="card ctpf-card">
                <div class="ctpf-card-header"><i class="bi bi-info-circle me-1"></i> Return Policy Guidelines</div>
                <div class="card-body" style="font-size: 0.85rem; line-height: 1.7;">
                    <h6 class="fw-bold text-success-emphasis mb-2">1. Size Exchanges (Unused)</h6>
                    <p class="text-muted mb-3">Items returned within 7 days due to size mismatches (unused, clean state) must be logged as <strong>Restocked</strong>. This automatically returns items to warehouse batch levels.</p>

                    <h6 class="fw-bold text-danger mb-2">2. Wear-and-Tear (Condemned)</h6>
                    <p class="text-muted mb-3">Worn uniforms or accessories returned due to damage should be logged as <strong>Condemned/Disposed</strong>. Stock levels will not increment; items are written off as scrap.</p>

                    <h6 class="fw-bold text-dark mb-2">3. Transfers / Retirements</h6>
                    <p class="text-muted mb-0">Officers being transferred out of Faisalabad or retiring must return all trackable logistics (Wireless GP338 sets, Peak caps, Leather gear) before a **Clearance Certificate** can be printed.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RETURN ACTION DIALOG MODAL -->
    @if($isReturnFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-arrow-left-right me-1"></i> Log Returned Item
                        </h5>
                        <button wire:click="closeReturnForm" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    
                    <form wire:submit.prevent="processReturn">
                        <div class="modal-body py-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Quantity to Return (Max: {{ $returnQuantity }})</label>
                                <input wire:model="returnQuantity" type="number" min="1" max="{{ $returnQuantity }}" class="form-control border-secondary-subtle" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Reason for Deposit</label>
                                <select wire:model="reason" class="form-select border-secondary-subtle" required>
                                    <option value="size_mismatch">Size Mismatch / Swap Request</option>
                                    <option value="wear_and_tear">Wear & Tear (Damaged / Worn Out)</option>
                                    <option value="transfer">Officer Transfer Out of Circle</option>
                                    <option value="retirement">Officer Retirement / Departure</option>
                                    <option value="other">Other / General Return</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Depot Action Taken</label>
                                <select wire:model="action_taken" class="form-select border-secondary-subtle" required>
                                    <option value="restocked">Restock back to Warehouse Batch (For Swap / Re-use)</option>
                                    <option value="condemned_disposed">Write-off as Scrap / Condemned (Wear & Tear)</option>
                                    <option value="sent_to_repairs">Sent to Repairs / Refurbishment</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label fw-bold small text-muted">Remarks / Details</label>
                                <textarea wire:model="remarks" id="remarks" rows="2" class="form-control border-secondary-subtle" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button wire:click="closeReturnForm" type="button" class="btn btn-outline-secondary fw-semibold">Cancel</button>
                            <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold">
                                <i class="bi bi-check-circle-fill me-1 text-warning"></i> Confirm Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
