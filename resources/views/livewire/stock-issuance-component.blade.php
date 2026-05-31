<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-cart-plus-fill me-1 text-success"></i> New Uniform & Equipment Issuance
            </h1>
            <p class="text-muted small mb-0">Record and allocate seasonal uniforms and field items to Traffic Police personnel.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="row">
        <!-- LEFT COLUMN: Warden Lookup & Cart Entry -->
        <div class="col-lg-7">
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
                                    placeholder="Enter Belt Number (e.g. 542) or CNIC..."
                                >
                            </div>
                        </div>

                        <!-- Warden Lookup Search Results -->
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
                                        <small class="text-muted d-block text-uppercase small font-monospace">Current Circle Posting</small>
                                        <span class="fw-semibold text-dark">{{ $selectedWarden->current_posting }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add Items to Issuance Form Card -->
            @if($selectedWarden)
                <div class="card ctpf-card mb-4">
                    <div class="ctpf-card-header"><i class="bi bi-bag-plus me-1"></i> Select Inventory Item & Batch (FIFO)</div>
                    <div class="card-body">
                        <form wire:submit.prevent="addToCart">
                            <div class="row">
                                <!-- Select Item SKU -->
                                <div class="col-md-6 mb-3">
                                    <label for="selectedItem" class="form-label fw-semibold small text-muted">Item Catalog</label>
                                    <select wire:model.live="selectedItem" id="selectedItem" class="form-select border-secondary-subtle" required>
                                        <option value="">-- Choose Item --</option>
                                        @foreach($items as $i)
                                            <option value="{{ $i->id }}">
                                                {{ $i->name }} @if($i->size_attribute)(Size: {{ $i->size_attribute }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Select Batch (Populated dynamically via FIFO) -->
                                <div class="col-md-6 mb-3">
                                    <label for="selectedBatch" class="form-label fw-semibold small text-muted">Available Batch (FIFO Queue)</label>
                                    <select wire:model="selectedBatch" id="selectedBatch" class="form-select border-secondary-subtle" required {{ empty($availableBatches) ? 'disabled' : '' }}>
                                        <option value="">-- Select Active Batch --</option>
                                        @foreach($availableBatches as $b)
                                            <option value="{{ $b->id }}">
                                                {{ $b->batch_number }} (Stock: {{ $b->current_quantity }} @if($b->expiry_date)• Exp: {{ $b->expiry_date->format('Y') }}@endif)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantity Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="selectedQuantity" class="form-label fw-semibold small text-muted">Quantity to Issue</label>
                                    <input wire:model="selectedQuantity" type="number" id="selectedQuantity" min="1" class="form-control border-secondary-subtle" required>
                                </div>

                                <!-- Submit Add to Cart -->
                                <div class="col-md-6 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-ctpf-primary fw-bold w-100 py-2">
                                        <i class="bi bi-cart-plus me-1"></i> Add to Issuance Cart
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: Issuance Cart Summary & Save Action -->
        <div class="col-lg-5">
            <div class="card ctpf-card">
                <div class="ctpf-card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check me-1"></i> Allocation Cart Summary</span>
                    <span class="badge badge-ctpf-gold py-1 px-2 rounded">{{ count($cartItems) }} Items</span>
                </div>
                <div class="card-body p-0">
                    @if(empty($cartItems))
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-cart-x fs-1 mb-3 d-block text-secondary"></i>
                            <span class="fw-semibold small">Issuance Cart is empty. Select a Warden and add items on the left to prepare the allocation slip.</span>
                        </div>
                    @else
                        <!-- List of Cart Items -->
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 border-0">Item / SKU</th>
                                        <th class="border-0">Batch</th>
                                        <th class="border-0 text-center">Qty</th>
                                        <th class="border-0 text-end pe-3">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $batchId => $cart)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold text-dark">{{ $cart['name'] }}</div>
                                                <small class="text-muted small">Size: {{ $cart['size'] }} • SKU: {{ $cart['sku'] }}</small>
                                            </td>
                                            <td class="text-secondary small fw-semibold">{{ $cart['batch_number'] }}</td>
                                            <td class="text-center fw-bold">{{ $cart['quantity'] }}</td>
                                            <td class="text-end pe-3">
                                                <button wire:click="removeFromCart({{ $batchId }})" class="btn btn-sm btn-outline-danger py-0 border-0" title="Delete">
                                                    <i class="bi bi-trash-fill fs-6"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Remarks and Processing Form -->
                        <div class="p-3 bg-light border-top">
                            <div class="mb-3">
                                <label for="remarks" class="form-label fw-bold small text-muted">Internal Remarks / Notes</label>
                                <textarea wire:model="remarks" id="remarks" rows="2" class="form-control border-secondary-subtle" placeholder="Optional notes... e.g. issued replacement for Summer uniform shirt."></textarea>
                            </div>

                            <button wire:click="processIssuance" class="btn btn-success fw-bold w-100 py-2 text-uppercase rounded-3 shadow-sm border-0" style="background-color: var(--ctpf-emerald);">
                                <i class="bi bi-check2-square me-1 text-warning"></i> Confirm & Authorize Handover
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
