<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Staff;
use App\Models\Item;
use App\Models\StockIssuance;
use App\Models\InventoryBatch;
use Illuminate\Support\Facades\DB;

class StockIssuanceComponent extends Component
{
    public $searchWarden = '';
    public $selectedWarden = null;
    
    public $cartItems = [];
    public $remarks = '';

    public $selectedItem = '';
    public $selectedBatch = '';
    public $selectedQuantity = 1;

    public $availableBatches = [];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        if (!auth()->user()->can('create-issuance')) {
            abort(403, 'Unauthorized access to CTPF stock issuance.');
        }
    }

    public function updatedSearchWarden()
    {
        if (strlen($this->searchWarden) < 3) {
            $this->selectedWarden = null;
        }
    }

    public function selectWarden($id)
    {
        $this->selectedWarden = Staff::find($id);
        $this->searchWarden = ''; // Clear search field
    }

    public function clearSelectedWarden()
    {
        $this->selectedWarden = null;
    }

    public function updatedSelectedItem()
    {
        if ($this->selectedItem) {
            $this->availableBatches = InventoryBatch::where('item_id', $this->selectedItem)
                ->where('current_quantity', '>', 0)
                ->orderBy('received_date', 'asc') // FIFO sorting
                ->get();
            $this->selectedBatch = '';
        } else {
            $this->availableBatches = [];
        }
    }

    public function addToCart()
    {
        $this->validate([
            'selectedItem' => 'required|exists:items,id',
            'selectedBatch' => 'required|exists:inventory_batches,id',
            'selectedQuantity' => 'required|integer|min:1',
        ]);

        $batch = InventoryBatch::find($this->selectedBatch);

        if ($batch->current_quantity < $this->selectedQuantity) {
            session()->flash('error', "Only {$batch->current_quantity} units available in Batch {$batch->batch_number}!");
            return;
        }

        // Check if item is already in cart, update quantity instead
        if (isset($this->cartItems[$this->selectedBatch])) {
            $newQty = $this->cartItems[$this->selectedBatch]['quantity'] + $this->selectedQuantity;
            if ($batch->current_quantity < $newQty) {
                session()->flash('error', "Cannot add more. Insufficient stock in Batch {$batch->batch_number}!");
                return;
            }
            $this->cartItems[$this->selectedBatch]['quantity'] = $newQty;
        } else {
            $this->cartItems[$this->selectedBatch] = [
                'item_id' => $this->selectedItem,
                'name' => $batch->item->name,
                'sku' => $batch->item->sku,
                'size' => $batch->item->size_attribute ?? 'N/A',
                'batch_number' => $batch->batch_number,
                'quantity' => $this->selectedQuantity,
            ];
        }

        // Reset inputs
        $this->reset(['selectedItem', 'selectedBatch', 'selectedQuantity', 'availableBatches']);
    }

    public function removeFromCart($batchId)
    {
        unset($this->cartItems[$batchId]);
    }

    public function processIssuance()
    {
        if (!$this->selectedWarden) {
            session()->flash('error', 'Warden profile must be selected first.');
            return;
        }

        if (empty($this->cartItems)) {
            session()->flash('error', 'Issuance cart cannot be empty.');
            return;
        }

        DB::transaction(function () {
            // Generate professional slip number: CTPF-IS-[timestamp]
            $slipNumber = 'CTPF-IS-' . date('Ymd') . '-' . mt_rand(1000, 9999);

            $issuance = StockIssuance::create([
                'issuance_slip_no' => $slipNumber,
                'staff_id' => $this->selectedWarden->id,
                'issued_by' => auth()->id(),
                'issuance_date' => now()->toDateString(),
                'status' => 'issued', // Default auto-completed for development, can be queued for pending
                'remarks' => $this->remarks,
            ]);

            foreach ($this->cartItems as $batchId => $cart) {
                $batch = InventoryBatch::find($batchId);

                // Lock batch for update to handle race conditions
                $batch = InventoryBatch::where('id', $batchId)->lockForUpdate()->first();

                if ($batch->current_quantity < $cart['quantity']) {
                    throw new \Exception("Stock for item '{$cart['name']}' in Batch '{$cart['batch_number']}' has depleted during checkout.");
                }

                $batch->decrement('current_quantity', $cart['quantity']);

                $issuance->items()->create([
                    'batch_id' => $batchId,
                    'quantity' => $cart['quantity'],
                    'status' => 'active',
                ]);
            }
        });

        session()->flash('success', 'Wardi/Inventory items successfully issued to the Warden!');
        
        $this->reset(['cartItems', 'selectedWarden', 'remarks']);
    }

    public function render()
    {
        $wardens = [];
        if (strlen($this->searchWarden) >= 3) {
            $wardens = Staff::where('belt_no', 'like', '%' . $this->searchWarden . '%')
                ->orWhere('first_name', 'like', '%' . $this->searchWarden . '%')
                ->orWhere('last_name', 'like', '%' . $this->searchWarden . '%')
                ->orWhere('cnic', 'like', '%' . $this->searchWarden . '%')
                ->take(5)
                ->get();
        }

        $items = Item::with(['batches' => function ($query) {
            $query->where('current_quantity', '>', 0);
        }])->get();

        return view('livewire.stock-issuance-component', [
            'wardens' => $wardens,
            'items' => $items,
        ])->layout('components.layouts.app');
    }
}
