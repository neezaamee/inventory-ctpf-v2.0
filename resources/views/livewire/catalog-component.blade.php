<div>
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-tags-fill me-1 text-success"></i> Inventory Catalog & SKU Directory
            </h1>
            <p class="text-muted small mb-0">Register uniform codes, accessories, sizing grids, and stock re-order thresholds.</p>
        </div>
        @if(auth()->user()->can('configure-inventory') || auth()->user()->hasRole('Store Incharge'))
        <div>
            @if($activeTab === 'items')
                <button wire:click="openItemForm" class="btn btn-ctpf-primary fw-bold">
                    <i class="bi bi-tag-fill me-1"></i> Register New SKU Item
                </button>
            @else
                <button wire:click="openCategoryForm" class="btn btn-ctpf-primary fw-bold">
                    <i class="bi bi-folder-plus me-1"></i> Add Category
                </button>
            @endif
        </div>
        @endif
    </div>

    <!-- Dual Navigation Tabs -->
    <div class="mb-4">
        <ul class="nav nav-pills" style="font-size: 0.95rem;">
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'items')" class="nav-link fw-bold px-4 py-2 me-2 {{ $activeTab === 'items' ? 'active bg-success text-white' : 'text-secondary bg-white border' }}" style="background-color: {{ $activeTab === 'items' ? 'var(--ctpf-emerald) !important' : '' }};">
                    <i class="bi bi-box-seam me-1"></i> Catalog Items (SKUs)
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'categories')" class="nav-link fw-bold px-4 py-2 {{ $activeTab === 'categories' ? 'active bg-success text-white' : 'text-secondary bg-white border' }}" style="background-color: {{ $activeTab === 'categories' ? 'var(--ctpf-emerald) !important' : '' }};">
                    <i class="bi bi-folder me-1"></i> Stock Categories
                </button>
            </li>
        </ul>
    </div>

    @if($activeTab === 'items')
        <!-- ITEMS TAB PANEL -->
        <!-- Search & Filter Card -->
        <div class="card ctpf-card p-3 mb-4">
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-bold small text-muted">Search Catalog</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary-subtle text-secondary"><i class="bi bi-search"></i></span>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control border-secondary-subtle" placeholder="Search by Item name, SKU code or size attribute...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Category Filter</label>
                    <select wire:model.live="filterCategory" class="form-select border-secondary-subtle">
                        <option value="">-- All Categories --</option>
                        @foreach($categoriesList as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button wire:click="$set('search', ''); $set('filterCategory', '');" class="btn btn-outline-secondary w-100" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Items Grid Table -->
        <div class="card ctpf-card">
            <div class="card-body p-0">
                @if($itemsList->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-tag-fill fs-1 mb-3 d-block text-secondary"></i>
                        <span class="fw-semibold small">No items defined in the catalog. Click 'Register New SKU Item' to add items!</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 border-0">SKU Code</th>
                                    <th class="border-0">Item Catalog Name</th>
                                    <th class="border-0">Category</th>
                                    <th class="border-0 text-center">Size</th>
                                    <th class="border-0 text-center">Available Stock</th>
                                    <th class="border-0 text-center">Min Level</th>
                                    <th class="border-0 text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itemsList as $item)
                                    <tr>
                                        <td class="ps-3 fw-bold text-success-emphasis font-monospace">{{ $item->sku }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                            @if($item->description)
                                                <small class="text-muted small text-truncate d-inline-block" style="max-width: 250px;">{{ $item->description }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $item->category->name }}</span></td>
                                        <td class="text-center font-monospace fw-bold text-secondary">{{ $item->size_attribute ?? 'N/A' }}</td>
                                        <td class="text-center fw-bold">
                                            <span class="badge {{ $item->needs_restock ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} px-3 py-1 rounded-pill">
                                                {{ $item->available_stock }} {{ $item->unit_of_measure }}
                                            </span>
                                        </td>
                                        <td class="text-center font-monospace text-muted">{{ $item->min_quantity }}</td>
                                        <td class="text-end pe-3">
                                            @if(auth()->user()->can('configure-inventory') || auth()->user()->hasRole('Store Incharge'))
                                            <button wire:click="openItemForm({{ $item->id }})" class="btn btn-sm btn-light border me-1" title="Edit Item SKU">
                                                <i class="bi bi-pencil-fill text-primary"></i>
                                            </button>
                                            <button wire:click="confirmDeletion({{ $item->id }}, 'item')" class="btn btn-sm btn-light border" title="Delete SKU Item">
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
                        {{ $itemsList->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- CATEGORIES TAB PANEL -->
        <div class="card ctpf-card">
            <div class="card-body p-0">
                @if($categoriesList->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-folder-x fs-1 mb-3 d-block text-secondary"></i>
                        <span class="fw-semibold small">No stock categories defined. Click 'Add Category' above to create one!</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 border-0">Category Name</th>
                                    <th class="border-0">Description</th>
                                    <th class="border-0 text-center">Items count</th>
                                    <th class="border-0 text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoriesList as $cat)
                                    <tr>
                                        <td class="ps-3 fw-bold text-success-emphasis">{{ $cat->name }}</td>
                                        <td class="text-secondary small">{{ $cat->description ?? 'No description provided.' }}</td>
                                        <td class="text-center"><span class="badge badge-ctpf-gold px-2 py-1 rounded">{{ $cat->items_count }} SKUs</span></td>
                                        <td class="text-end pe-3">
                                            @if(auth()->user()->can('configure-inventory') || auth()->user()->hasRole('Store Incharge'))
                                            <button wire:click="openCategoryForm({{ $cat->id }})" class="btn btn-sm btn-light border me-1" title="Edit Category">
                                                <i class="bi bi-pencil-fill text-primary"></i>
                                            </button>
                                            <button wire:click="confirmDeletion({{ $cat->id }}, 'category')" class="btn btn-sm btn-light border" title="Delete Category" {{ $cat->items_count > 0 ? 'disabled' : '' }}>
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
                @endif
            </div>
        </div>
    @endif

    <!-- CATEGORY REGISTRATION MODAL -->
    @if($isCategoryFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-folder-plus me-1"></i>
                            {{ $categoryId ? 'Modify Category' : 'Create Stock Category' }}
                        </h5>
                        <button wire:click="closeCategoryForm" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveCategory">
                        <div class="modal-body py-4">
                            <div class="mb-3">
                                <label for="cat_name" class="form-label fw-semibold small text-muted">Category Title</label>
                                <input wire:model="cat_name" type="text" id="cat_name" class="form-control border-secondary-subtle @error('cat_name') is-invalid @enderror" placeholder="e.g. Tactical Helmets" required>
                                @error('cat_name') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="cat_description" class="form-label fw-semibold small text-muted">Description</label>
                                <textarea wire:model="cat_description" id="cat_description" rows="3" class="form-control border-secondary-subtle" placeholder="Brief details about items grouped in this category..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button wire:click="closeCategoryForm" type="button" class="btn btn-outline-secondary fw-semibold">Cancel</button>
                            <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold">
                                <i class="bi bi-check-circle-fill me-1 text-warning"></i> Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- ITEM SKU REGISTRATION MODAL -->
    @if($isItemFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3" style="border-top: 5px solid var(--ctpf-gold) !important;">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-success-emphasis">
                            <i class="bi bi-tag-fill me-1"></i>
                            {{ $itemId ? 'Update SKU Item details' : 'Register New SKU Item' }}
                        </h5>
                        <button wire:click="closeItemForm" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveItem">
                        <div class="modal-body py-4">
                            <div class="row g-3">
                                <!-- Category Selection -->
                                <div class="col-md-6">
                                    <label for="item_category_id" class="form-label fw-semibold small text-muted">Stock Category</label>
                                    <select wire:model="item_category_id" id="item_category_id" class="form-select border-secondary-subtle @error('item_category_id') is-invalid @enderror" required>
                                        <option value="">-- Choose Category --</option>
                                        @foreach($categoriesList as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('item_category_id') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Item Name -->
                                <div class="col-md-6">
                                    <label for="item_name" class="form-label fw-semibold small text-muted">Item Catalog Title</label>
                                    <input wire:model.live="item_name" type="text" id="item_name" class="form-control border-secondary-subtle @error('item_name') is-invalid @enderror" placeholder="e.g. Peak Cap CTPF" required>
                                    @error('item_name') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Size Attribute -->
                                <div class="col-md-6">
                                    <label for="item_size_attribute" class="form-label fw-semibold small text-muted">Size / Variant (e.g. 7.5, M, 42) [Optional]</label>
                                    <input wire:model.live="item_size_attribute" type="text" id="item_size_attribute" class="form-control border-secondary-subtle @error('item_size_attribute') is-invalid @enderror" placeholder="Leave empty if item has no size split">
                                    @error('item_size_attribute') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Auto-Generated SKU Preview -->
                                <div class="col-md-6">
                                    <label for="item_sku" class="form-label fw-semibold small text-muted">SKU Code (Auto-Generated or Manual)</label>
                                    <input wire:model="item_sku" type="text" id="item_sku" class="form-control font-monospace border-secondary-subtle @error('item_sku') is-invalid @enderror" placeholder="Auto-generated based on title/size" required>
                                    @error('item_sku') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Min Quantity Alert Level -->
                                <div class="col-md-6">
                                    <label for="item_min_quantity" class="form-label fw-semibold small text-muted">Low Stock Alert Threshold Limit</label>
                                    <input wire:model="item_min_quantity" type="number" id="item_min_quantity" min="0" class="form-control border-secondary-subtle @error('item_min_quantity') is-invalid @enderror" required>
                                    @error('item_min_quantity') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Unit of measure -->
                                <div class="col-md-6">
                                    <label for="item_unit_of_measure" class="form-label fw-semibold small text-muted">Unit of Measure</label>
                                    <select wire:model="item_unit_of_measure" id="item_unit_of_measure" class="form-select border-secondary-subtle @error('item_unit_of_measure') is-invalid @enderror" required>
                                        <option value="pcs">pcs (Pieces)</option>
                                        <option value="pairs">pairs (Shoes/Socks)</option>
                                        <option value="meters">meters (Cloth/Fabric)</option>
                                        <option value="sets">sets (Wireless / Safety Kits)</option>
                                    </select>
                                    @error('item_unit_of_measure') <div class="invalid-feedback fw-semibold small text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- Trackability Toggle -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Trackability Mode</label>
                                    <div class="d-flex gap-3 py-2">
                                        <div class="form-check">
                                            <input wire:model="item_is_trackable" type="radio" class="form-check-input" id="track_true" value="1">
                                            <label class="form-check-label text-dark fw-medium small" for="track_true">Track Returns (Uniforms, Equipment)</label>
                                        </div>
                                        <div class="form-check">
                                            <input wire:model="item_is_trackable" type="radio" class="form-check-input" id="track_false" value="0">
                                            <label class="form-check-label text-dark fw-medium small" for="track_false">Consumable (Stationery, disposable gear)</label>
                                        </div>
                                    </div>
                                    @error('item_is_trackable') <div class="text-danger fw-semibold small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label for="item_description" class="form-label fw-semibold small text-muted">Item Description</label>
                                    <textarea wire:model="item_description" id="item_description" rows="2" class="form-control border-secondary-subtle" placeholder="Additional specifications..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button wire:click="closeItemForm" type="button" class="btn btn-outline-secondary fw-semibold">Cancel</button>
                            <button type="submit" class="btn btn-ctpf-primary px-4 fw-bold">
                                <i class="bi bi-check-circle-fill me-1 text-warning"></i> Save SKU Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Deletion Confirmation Modal Overlay -->
    @if($confirmingDeletionId)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 1070;">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash3-fill text-danger fs-1 mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark mb-2">Confirm Delete Action?</h5>
                        <p class="text-muted small mb-0 px-2">Are you sure you want to delete this {{ $confirmingDeletionType }} from the system catalog? This action is irreversible.</p>
                    </div>
                    <div class="modal-footer bg-light justify-content-center border-0 py-3">
                        <button wire:click="cancelDeletion" class="btn btn-secondary btn-sm px-3 fw-bold me-2">Abort</button>
                        <button wire:click="deleteCatalogRecord" class="btn btn-danger btn-sm px-3 fw-bold">Confirm Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
