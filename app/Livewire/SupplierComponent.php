<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class SupplierComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search and Filters
    public $search = '';
    public $filterStatus = '';

    // Supplier Form Fields
    public $supplierId = null;
    public $company_name = '';
    public $contact_person = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $ntn_number = '';
    public $status = 'active';

    // Modal Control Flags
    public $isFormOpen = false;
    public $confirmingDeletionId = null;

    protected function rules()
    {
        return [
            'company_name' => 'required|string|max:150',
            'contact_person' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100|unique:suppliers,email,' . $this->supplierId,
            'address' => 'nullable|string',
            'ntn_number' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    private function checkSupplierPermission()
    {
        if (!auth()->user()->can('manage-suppliers') && !auth()->user()->hasRole('Store Incharge')) {
            abort(403, 'Unauthorized access to CTPF supplier management.');
        }
    }

    public function openForm($id = null)
    {
        $this->checkSupplierPermission();
        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $this->supplierId = $id;
            $supplier = Supplier::findOrFail($id);
            $this->company_name = $supplier->company_name;
            $this->contact_person = $supplier->contact_person;
            $this->phone = $supplier->phone;
            $this->email = $supplier->email;
            $this->address = $supplier->address;
            $this->ntn_number = $supplier->ntn_number;
            $this->status = $supplier->status;
        }

        $this->isFormOpen = true;
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->supplierId = null;
        $this->company_name = '';
        $this->contact_person = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->ntn_number = '';
        $this->status = 'active';
    }

    public function saveSupplier()
    {
        $this->checkSupplierPermission();
        $this->validate();

        Supplier::updateOrCreate(
            ['id' => $this->supplierId],
            [
                'company_name' => $this->company_name,
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'ntn_number' => $this->ntn_number,
                'status' => $this->status,
            ]
        );

        session()->flash('success', $this->supplierId ? 'Supplier profile updated successfully.' : 'New Supplier registered successfully.');
        
        $this->closeForm();
    }

    public function confirmDeletion($id)
    {
        $this->checkSupplierPermission();
        $this->confirmingDeletionId = $id;
    }

    public function cancelDeletion()
    {
        $this->confirmingDeletionId = null;
    }

    public function deleteSupplier()
    {
        $this->checkSupplierPermission();
        if ($this->confirmingDeletionId) {
            $supplier = Supplier::findOrFail($this->confirmingDeletionId);
            
            // Check if supplier is linked to active inventory stock batches
            if ($supplier->batches()->count() > 0) {
                session()->flash('error', 'Cannot delete Supplier: They are linked to active warehouse stock batches.');
            } else {
                $supplier->delete();
                session()->flash('success', 'Supplier profile deleted successfully.');
            }
            $this->confirmingDeletionId = null;
        }
    }

    public function render()
    {
        $query = Supplier::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                  ->orWhere('ntn_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $suppliersList = $query->orderBy('company_name', 'asc')->paginate(10);

        return view('livewire.supplier-component', [
            'suppliersList' => $suppliersList,
        ])->layout('components.layouts.app');
    }
}
