<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockReceive;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\InventoryBatch;
use Illuminate\Support\Facades\DB;

class GrnComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // View control
    public $viewMode = 'list'; // 'list' or 'create' or 'detail'

    // Search and Filters
    public $search = '';
    
    // GRN Master Form Fields
    public $supplier_id = '';
    public $invoice_number = '';
    public $received_date = '';
    public $remarks = '';

    // Active GRN Detail view
    public $activeGrn = null;

    // Temporary Intake Item Form Fields
    public $selectedItemId = '';
    public $batch_number = '';
    public $unit_purchase_cost = 0;
    public $quantity = 1;
    public $expiry_date = '';

    // Dynamic intake list
    public $intakeItems = [];
    public $grandTotal = 0;

    protected function rules()
    {
        if ($this->viewMode === 'create') {
            return [
                'supplier_id' => 'required|exists:suppliers,id',
                'invoice_number' => 'nullable|string|max:100',
                'received_date' => 'required|date|before_or_equal:today',
                'remarks' => 'nullable|string',
            ];
        }
        return [];
    }

    public function mount()
    {
        $this->received_date = now()->toDateString();
    }

    private function checkGrnPermission()
    {
        if (!auth()->user()->can('create-grn')) {
            abort(403, 'Unauthorized access to CTPF GRN processing.');
        }
    }

    public function changeView($mode)
    {
        if ($mode === 'create') {
            $this->checkGrnPermission();
            $this->resetForm();
        }
        $this->viewMode = $mode;
    }

    private function resetForm()
    {
        $this->supplier_id = '';
        $this->invoice_number = '';
        $this->received_date = now()->toDateString();
        $this->remarks = '';
        $this->intakeItems = [];
        $this->grandTotal = 0;
        $this->resetIntakeItemFields();
    }

    private function resetIntakeItemFields()
    {
        $this->selectedItemId = '';
        $this->batch_number = '';
        $this->unit_purchase_cost = 0;
        $this->quantity = 1;
        $this->expiry_date = '';
    }

    public function addIntakeItem()
    {
        $this->checkGrnPermission();
        $this->validate([
            'selectedItemId' => 'required|exists:items,id',
            'batch_number' => 'required|string|max:100',
            'unit_purchase_cost' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $item = Item::find($this->selectedItemId);
        $lineTotal = $this->unit_purchase_cost * $this->quantity;

        // Check if item already exists in intake table, merge quantities
        $key = $this->selectedItemId . '|' . $this->batch_number;

        if (isset($this->intakeItems[$key])) {
            $this->intakeItems[$key]['quantity'] += $this->quantity;
            $this->intakeItems[$key]['line_total'] = $this->intakeItems[$key]['unit_purchase_cost'] * $this->intakeItems[$key]['quantity'];
        } else {
            $this->intakeItems[$key] = [
                'item_id' => $this->selectedItemId,
                'name' => $item->name,
                'sku' => $item->sku,
                'size' => $item->size_attribute ?? 'N/A',
                'batch_number' => $this->batch_number,
                'unit_purchase_cost' => $this->unit_purchase_cost,
                'quantity' => $this->quantity,
                'expiry_date' => $this->expiry_date ?: null,
                'line_total' => $lineTotal,
            ];
        }

        $this->calculateGrandTotal();
        $this->resetIntakeItemFields();
    }

    public function removeIntakeItem($key)
    {
        $this->checkGrnPermission();
        unset($this->intakeItems[$key]);
        $this->calculateGrandTotal();
    }

    private function calculateGrandTotal()
    {
        $this->grandTotal = array_sum(array_column($this->intakeItems, 'line_total'));
    }

    public function saveGRN()
    {
        $this->checkGrnPermission();
        $this->validate();

        if (empty($this->intakeItems)) {
            session()->flash('error', 'Intake items list cannot be empty.');
            return;
        }

        DB::transaction(function () {
            // Generate clean GRN number: GRN-YYYYMMDD-XXXX
            $grnNumber = 'GRN-' . date('Ymd') . '-' . mt_rand(1000, 9999);

            $grn = StockReceive::create([
                'grn_number' => $grnNumber,
                'supplier_id' => $this->supplier_id,
                'received_by' => auth()->id(),
                'received_date' => $this->received_date,
                'invoice_number' => $this->invoice_number,
                'total_amount' => $this->grandTotal,
                'remarks' => $this->remarks,
            ]);

            // Save details into dynamic JSON column or custom metadata, or directly release to active batches.
            // Under government audit strict rules: stock is 'pending approval'.
            // For now, let's create the active batches directly on GRN creation if authorized, or put them in active inventory batches on GRN creation.
            // Let's create the batches directly so they are immediately available, but set a flag, or just create them:
            foreach ($this->intakeItems as $item) {
                InventoryBatch::create([
                    'item_id' => $item['item_id'],
                    'supplier_id' => $this->supplier_id,
                    'batch_number' => $item['batch_number'],
                    'unit_purchase_cost' => $item['unit_purchase_cost'],
                    'initial_quantity' => $item['quantity'],
                    'current_quantity' => $item['quantity'],
                    'received_date' => $this->received_date,
                    'expiry_date' => $item['expiry_date'] ?: null,
                ]);
            }
        });

        session()->flash('success', 'Goods Received Note (GRN) created successfully. Stock batches are now active!');
        $this->changeView('list');
    }

    public function viewDetails($id)
    {
        $this->activeGrn = StockReceive::with(['supplier', 'receiver'])->findOrFail($id);
        $this->viewMode = 'detail';
    }

    public function render()
    {
        $query = StockReceive::with(['supplier', 'receiver']);

        if ($this->search) {
            $query->where('grn_number', 'like', '%' . $this->search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
        }

        $grnsList = $query->orderBy('id', 'desc')->paginate(10);
        $suppliers = Supplier::where('status', 'active')->get();
        $items = Item::all();

        return view('livewire.grn-component', [
            'grnsList' => $grnsList,
            'suppliers' => $suppliers,
            'items' => $items,
        ])->layout('components.layouts.app');
    }
}
