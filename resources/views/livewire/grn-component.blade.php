<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-file-earmark-arrow-down-fill me-1 text-success"></i> Goods Received Notes (GRN)
            </h1>
            <p class="text-muted small mb-0">Record delivery batches, invoice pricing, and verify logistics items intake.</p>
        </div>
        <div>
            @if($viewMode === 'list')
                @can('create-grn')
                <button wire:click="changeView('create')" class="btn btn-ctpf-primary fw-bold">
                    <i class="bi bi-plus-circle-fill me-1"></i> Create Goods Note (GRN)
                </button>
                @endcan
            @else
                <button wire:click="changeView('list')" class="btn btn-sm btn-outline-secondary fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </button>
            @endif
        </div>
    </div>

    @if($viewMode === 'list')
        <!-- LIST MODE PANEL -->
        <!-- Search Card -->
        <div class="card ctpf-card p-3 mb-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Search GRNs</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control border-secondary-subtle" placeholder="Search by GRN Number, Invoice Number...">
                    </div>
                </div>
            </div>
        </div>

        <!-- GRN History Table Card -->
        <div class="card ctpf-card">
            <div class="card-body p-0">
                @if($grnsList->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-file-earmark-arrow-down fs-1 mb-3 d-block text-secondary"></i>
                        <span class="fw-semibold small">No Goods Received Notes registered yet. Click 'Create Goods Note (GRN)' to log your first shipment!</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 border-0">GRN Number</th>
                                    <th class="border-0">Supplier / Vendor</th>
                                    <th class="border-0">Invoice #</th>
                                    <th class="border-0">Received Date</th>
                                    <th class="border-0">Received By</th>
                                    <th class="border-0 text-center">Total Value</th>
                                    <th class="border-0 text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grnsList as $grn)
                                    <tr>
                                        <td class="ps-3 fw-bold text-success-emphasis">{{ $grn->grn_number }}</td>
                                        <td><span class="fw-semibold text-dark">{{ $grn->supplier->company_name }}</span></td>
                                        <td class="font-monospace text-secondary">{{ $grn->invoice_number ?? 'N/A' }}</td>
                                        <td class="text-secondary small">{{ $grn->received_date->format('d M, Y') }}</td>
                                        <td class="text-secondary small">{{ $grn->receiver->name }}</td>
                                        <td class="text-center fw-bold text-success-emphasis">Rs. {{ number_format($grn->total_amount, 2) }}</td>
                                        <td class="text-end pe-3">
                                            <button wire:click="viewDetails({{ $grn->id }})" class="btn btn-sm btn-light border" title="View Details">
                                                <i class="bi bi-eye-fill text-primary"></i> View Note
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="p-3 border-top bg-light">
                        {{ $grnsList->links() }}
                    </div>
                @endif
            </div>
        </div>
    @elseif($viewMode === 'create')
        <!-- CREATE MODE PANEL -->
        <div class="row">
            <!-- LEFT COLUMN: Master Form & Dynamic Items Adder -->
            <div class="col-lg-7">
                <!-- Master Details Card -->
                <div class="card ctpf-card mb-4">
                    <div class="ctpf-card-header"><i class="bi bi-file-earmark-text me-1"></i> Delivery Master Specifications</div>
                    <div class="card-body">
                        <form>
                            <div class="row g-3">
                                <!-- Supplier selection -->
                                <div class="col-md-12">
                                    <label for="supplier_id" class="form-label fw-semibold small text-muted">Supplier / Delivering Agency</label>
                                    <select wire:model="supplier_id" id="supplier_id" class="form-select border-secondary-subtle @error('supplier_id') is-invalid @enderror" required>
                                        <option value="">-- Choose Vendor --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->company_name }} ({{ $s->contact_person }})</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Invoice Number -->
                                <div class="col-md-6">
                                    <label for="invoice_number" class="form-label fw-semibold small text-muted">Invoice / DC Number</label>
                                    <input wire:model="invoice_number" type="text" id="invoice_number" class="form-control border-secondary-subtle" placeholder="e.g. INV-9854" required>
                                </div>

                                <!-- Received Date -->
                                <div class="col-md-6">
                                    <label for="received_date" class="form-label fw-semibold small text-muted">Intake Date</label>
                                    <input wire:model="received_date" type="date" id="received_date" class="form-control border-secondary-subtle @error('received_date') is-invalid @enderror" required>
                                    @error('received_date') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Nested Dynamic Item Intake Form -->
                <div class="card ctpf-card mb-4">
                    <div class="ctpf-card-header"><i class="bi bi-box-seam me-1"></i> Item Intake & Sizing specifications</div>
                    <div class="card-body">
                        <form wire:submit.prevent="addIntakeItem">
                            <div class="row g-3">
                                <!-- Select Catalog Item -->
                                <div class="col-md-6">
                                    <label for="selectedItemId" class="form-label fw-semibold small text-muted">Catalog SKU Item</label>
                                    <select wire:model="selectedItemId" id="selectedItemId" class="form-select border-secondary-subtle @error('selectedItemId') is-invalid @enderror" required>
                                        <option value="">-- Choose Item --</option>
                                        @foreach($items as $i)
                                            <option value="{{ $i->id }}">{{ $i->name }} @if($i->size_attribute)(Size: {{ $i->size_attribute }})@endif [{{ $i->sku }}]</option>
                                        @endforeach
                                    </select>
                                    @error('selectedItemId') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Batch Number -->
                                <div class="col-md-6">
                                    <label for="batch_number" class="form-label fw-semibold small text-muted">Batch Number (For FIFO Queue)</label>
                                    <input wire:model="batch_number" type="text" id="batch_number" class="form-control border-secondary-subtle @error('batch_number') is-invalid @enderror" placeholder="e.g. BATCH-2026-SUM" required>
                                    @error('batch_number') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Unit Purchase Cost -->
                                <div class="col-md-6">
                                    <label for="unit_purchase_cost" class="form-label fw-semibold small text-muted">Unit Purchase Cost (PKR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-secondary border-secondary-subtle">Rs.</span>
                                        <input wire:model="unit_purchase_cost" type="number" step="0.01" id="unit_purchase_cost" class="form-control border-secondary-subtle @error('unit_purchase_cost') is-invalid @enderror" required>
                                    </div>
                                    @error('unit_purchase_cost') <div class="text-danger fw-semibold small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label fw-semibold small text-muted">Intake Quantity</label>
                                    <input wire:model="quantity" type="number" min="1" id="quantity" class="form-control border-secondary-subtle @error('quantity') is-invalid @enderror" required>
                                    @error('quantity') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Expiry Date (Optional) -->
                                <div class="col-md-6">
                                    <label for="expiry_date" class="form-label fw-semibold small text-muted">Expiry Date [Optional]</label>
                                    <input wire:model="expiry_date" type="date" id="expiry_date" class="form-control border-secondary-subtle @error('expiry_date') is-invalid @enderror">
                                    @error('expiry_date') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Add Item to list button -->
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-ctpf-primary fw-bold w-100 py-2">
                                        <i class="bi bi-plus-lg me-1"></i> Add Item to Intake List
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Grand Total and Save -->
            <div class="col-lg-5">
                <div class="card ctpf-card">
                    <div class="ctpf-card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-cart-check me-1"></i> Received Items Intake List</span>
                        <span class="badge badge-ctpf-gold py-1 px-2 rounded">{{ count($intakeItems) }} Items</span>
                    </div>
                    <div class="card-body p-0">
                        @if(empty($intakeItems))
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-journal-x fs-1 mb-3 d-block text-secondary"></i>
                                <span class="fw-semibold small">Intake list is currently empty. Fill out specifications on the left to add items.</span>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 border-0">SKU / Item</th>
                                            <th class="border-0">Batch No</th>
                                            <th class="border-0 text-center">Cost</th>
                                            <th class="border-0 text-center">Qty</th>
                                            <th class="border-0 text-end pe-3">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($intakeItems as $key => $item)
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                                                    <small class="text-muted small">Size: {{ $item['size'] }} • SKU: {{ $item['sku'] }}</small>
                                                </td>
                                                <td class="text-secondary small font-monospace">{{ $item['batch_number'] }}</td>
                                                <td class="text-center font-monospace small">Rs.{{ number_format($item['unit_purchase_cost'], 2) }}</td>
                                                <td class="text-center fw-bold">{{ $item['quantity'] }}</td>
                                                <td class="text-end pe-3">
                                                    <button wire:click="removeIntakeItem('{{ $key }}')" type="button" class="btn btn-sm btn-outline-danger py-0 border-0" title="Delete">
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
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-secondary">Total Value:</span>
                                    <span class="fs-5 fw-bold text-success-emphasis">Rs. {{ number_format($grandTotal, 2) }}</span>
                                </div>

                                <div class="mb-3">
                                    <label for="remarks" class="form-label fw-bold small text-muted">Purchase Notes / Remarks</label>
                                    <textarea wire:model="remarks" id="remarks" rows="2" class="form-control border-secondary-subtle" placeholder="Additional notes about this delivery..."></textarea>
                                </div>

                                <button wire:click="saveGRN" type="button" class="btn btn-success fw-bold w-100 py-2 text-uppercase rounded-3 shadow-sm border-0" style="background-color: var(--ctpf-emerald);">
                                    <i class="bi bi-check2-square me-1 text-warning"></i> Save & Approve GRN Note
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif($viewMode === 'detail')
        <!-- DETAIL MODE PANEL (Printable Government Note) -->
        <div class="card ctpf-card p-4 shadow-sm" id="printableGrn">
            <!-- Government Department Header styling -->
            <div class="text-center mb-4 pb-3 border-bottom border-3 border-dark">
                <h4 class="fw-bold mb-0 text-uppercase">City Traffic Police Faisalabad</h4>
                <h5 class="fw-semibold text-secondary mb-1">Logistics & Supply Branch</h5>
                <h6 class="badge bg-dark px-3 py-2 text-uppercase font-monospace mt-2" style="font-size: 0.9rem;">Goods Received Note (GRN)</h6>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <small class="text-muted d-block text-uppercase font-monospace small">Delivering Agency / Supplier</small>
                    <h5 class="fw-bold text-dark mb-1">{{ $activeGrn->supplier->company_name }}</h5>
                    <small class="text-secondary d-block">Contact: {{ $activeGrn->supplier->contact_person }} ({{ $activeGrn->supplier->phone }})</small>
                    @if($activeGrn->supplier->address)
                        <small class="text-secondary d-block">Address: {{ $activeGrn->supplier->address }}</small>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block text-start">
                        <small class="text-muted d-block text-uppercase font-monospace small">GRN slip Number</small>
                        <h5 class="fw-bold text-success-emphasis font-monospace mb-1">{{ $activeGrn->grn_number }}</h5>
                        <small class="text-secondary d-block">Received Date: {{ $activeGrn->received_date->format('d F, Y') }}</small>
                        @if($activeGrn->invoice_number)
                            <small class="text-secondary d-block">Invoice # / DC Number: <strong class="text-dark">{{ $activeGrn->invoice_number }}</strong></small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Total Valuation summary banner -->
            <div class="alert alert-light border border-secondary-subtle p-3 rounded mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted d-block text-uppercase font-monospace mb-1">Total valuation</span>
                    <h4 class="fw-bold text-success-emphasis mb-0">Rs. {{ number_format($activeGrn->total_amount, 2) }}</h4>
                </div>
                <div class="text-end">
                    <span class="small text-muted d-block text-uppercase font-monospace mb-1">Received by officer</span>
                    <strong class="text-dark">{{ $activeGrn->receiver->name }}</strong>
                </div>
            </div>

            <!-- Notes Section -->
            @if($activeGrn->remarks)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark"><i class="bi bi-chat-right-text me-1"></i> Delivery Remarks:</h6>
                    <p class="bg-light p-3 rounded border text-secondary small">{{ $activeGrn->remarks }}</p>
                </div>
            @endif

            <!-- Bottom printable signatures -->
            <div class="row mt-5 pt-4 text-center">
                <div class="col-4">
                    <div class="border-top border-dark pt-2 small font-monospace fw-bold">Store Clerk Signature</div>
                </div>
                <div class="col-4">
                    <div class="border-top border-dark pt-2 small font-monospace fw-bold">Supplier Representative</div>
                </div>
                <div class="col-4">
                    <div class="border-top border-dark pt-2 small font-monospace fw-bold">Store Incharge Signature</div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4">
            <button onclick="window.print()" class="btn btn-outline-dark fw-bold">
                <i class="bi bi-printer me-1"></i> Print Note
            </button>
            <button wire:click="changeView('list')" class="btn btn-ctpf-primary fw-bold">
                <i class="bi bi-check-circle me-1"></i> Done
            </button>
        </div>
    @endif
</div>
