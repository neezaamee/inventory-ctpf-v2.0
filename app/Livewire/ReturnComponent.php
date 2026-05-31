<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Staff;
use App\Models\StockIssuanceItem;
use App\Models\StockReturn;
use App\Models\InventoryBatch;
use Illuminate\Support\Facades\DB;

class ReturnComponent extends Component
{
    public $searchWarden = '';
    public $selectedWarden = null;
    public $outstandingItems = [];

    // Return Form Fields
    public $activeItemId = null; // stock_issuance_items ID
    public $returnQuantity = 1;
    public $reason = 'size_mismatch';
    public $action_taken = 'restocked';
    public $remarks = '';

    // Modal Control Flags
    public $isReturnFormOpen = false;

    public function mount()
    {
        if (!auth()->user()->can('process-return')) {
            abort(403, 'Unauthorized access to CTPF returns management.');
        }
    }

    public function updatedSearchWarden()
    {
        if (strlen($this->searchWarden) < 3) {
            $this->selectedWarden = null;
            $this->outstandingItems = [];
        }
    }

    public function selectWarden($id)
    {
        $this->selectedWarden = Staff::find($id);
        $this->searchWarden = ''; // Clear search field
        $this->loadOutstandingItems();
    }

    public function clearSelectedWarden()
    {
        $this->selectedWarden = null;
        $this->outstandingItems = [];
    }

    private function loadOutstandingItems()
    {
        if ($this->selectedWarden) {
            // Find all active issuance items for this warden where active quantity > 0
            $this->outstandingItems = StockIssuanceItem::with(['issuance', 'batch.item'])
                ->whereHas('issuance', function ($query) {
                    $query->where('staff_id', $this->selectedWarden->id)
                          ->where('status', 'issued');
                })
                ->whereRaw('quantity > returned_quantity')
                ->get();
        } else {
            $this->outstandingItems = [];
        }
    }

    public function openReturnForm($id)
    {
        $this->resetValidation();
        $this->activeItemId = $id;
        $lineItem = StockIssuanceItem::findOrFail($id);
        
        $this->returnQuantity = $lineItem->quantity - $lineItem->returned_quantity;
        $this->reason = 'size_mismatch';
        $this->action_taken = 'restocked';
        $this->remarks = '';
        
        $this->isReturnFormOpen = true;
    }

    public function closeReturnForm()
    {
        $this->isReturnFormOpen = false;
        $this->activeItemId = null;
    }

    public function processReturn()
    {
        $lineItem = StockIssuanceItem::findOrFail($this->activeItemId);
        $maxReturnable = $lineItem->quantity - $lineItem->returned_quantity;

        $this->validate([
            'returnQuantity' => 'required|integer|min:1|max:' . $maxReturnable,
            'reason' => 'required|in:size_mismatch,wear_and_tear,transfer,retirement,other',
            'action_taken' => 'required|in:restocked,condemned_disposed,sent_to_repairs',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($lineItem) {
            // 1. Generate return slip number: CTPF-RT-[timestamp]
            $slipNumber = 'CTPF-RT-' . date('Ymd') . '-' . mt_rand(1000, 9999);

            StockReturn::create([
                'return_slip_no' => $slipNumber,
                'staff_id' => $this->selectedWarden->id,
                'received_by' => auth()->id(),
                'return_date' => now()->toDateString(),
                'reason' => $this->reason,
                'action_taken' => $this->action_taken,
                'remarks' => $this->remarks,
            ]);

            // 2. Update returned quantity on the stock issuance line item
            $lineItem->increment('returned_quantity', $this->returnQuantity);

            // Update status if fully returned
            if ($lineItem->returned_quantity >= $lineItem->quantity) {
                $lineItem->update(['status' => 'returned']);
            }

            // 3. If action is restocked, increment active inventory batches
            if ($this->action_taken === 'restocked') {
                $batch = InventoryBatch::where('id', $lineItem->batch_id)->lockForUpdate()->first();
                $batch->increment('current_quantity', $this->returnQuantity);
            }
        });

        session()->flash('success', 'Warden return transaction processed successfully!');
        
        $this->closeReturnForm();
        $this->loadOutstandingItems();
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

        return view('livewire.return-component', [
            'wardens' => $wardens,
        ])->layout('components.layouts.app');
    }
}
