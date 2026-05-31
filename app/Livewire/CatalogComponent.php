<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Item;

class CatalogComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Toggle Tab view (categories vs items)
    public $activeTab = 'items';

    // Search and Filters
    public $search = '';
    public $filterCategory = '';

    // Category Form Fields
    public $categoryId = null;
    public $cat_name = '';
    public $cat_description = '';

    // Item Form Fields
    public $itemId = null;
    public $item_category_id = '';
    public $item_name = '';
    public $item_sku = '';
    public $item_size_attribute = '';
    public $item_description = '';
    public $item_min_quantity = 10;
    public $item_unit_of_measure = 'pcs';
    public $item_is_trackable = true;

    // Modal Control Flags
    public $isCategoryFormOpen = false;
    public $isItemFormOpen = false;
    public $confirmingDeletionId = null;
    public $confirmingDeletionType = ''; // 'category' or 'item'

    protected function rules()
    {
        if ($this->isCategoryFormOpen) {
            return [
                'cat_name' => 'required|string|max:100|unique:categories,name,' . $this->categoryId,
                'cat_description' => 'nullable|string',
            ];
        }

        return [
            'item_category_id' => 'required|exists:categories,id',
            'item_name' => 'required|string|max:150',
            'item_sku' => 'required|string|max:100|unique:items,sku,' . $this->itemId,
            'item_size_attribute' => 'nullable|string|max:50',
            'item_description' => 'nullable|string',
            'item_min_quantity' => 'required|integer|min:0',
            'item_unit_of_measure' => 'required|string|max:20',
            'item_is_trackable' => 'required|boolean',
        ];
    }

    public function updatedItemName()
    {
        // Auto-generate a clean SKU prefix if name changes
        if (!$this->itemId && $this->item_name) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->item_name), 0, 5));
            $sizePart = $this->item_size_attribute ? '-' . strtoupper($this->item_size_attribute) : '';
            $this->item_sku = 'SKU-' . $prefix . $sizePart . '-' . mt_rand(100, 999);
        }
    }

    public function updatedItemSizeAttribute()
    {
        // Auto-generate dynamic SKU on size update
        if (!$this->itemId && $this->item_name) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->item_name), 0, 5));
            $sizePart = $this->item_size_attribute ? '-' . strtoupper(preg_replace('/[^A-Za-z0-9.]/', '', $this->item_size_attribute)) : '';
            $this->item_sku = 'SKU-' . $prefix . $sizePart . '-' . mt_rand(100, 999);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function checkCatalogPermission()
    {
        if (!auth()->user()->can('configure-inventory') && !auth()->user()->hasRole('Store Incharge')) {
            abort(403, 'Unauthorized access to CTPF inventory catalog management.');
        }
    }

    // Category CRUD Helpers
    public function openCategoryForm($id = null)
    {
        $this->checkCatalogPermission();
        $this->resetValidation();
        $this->categoryId = null;
        $this->cat_name = '';
        $this->cat_description = '';

        if ($id) {
            $this->categoryId = $id;
            $cat = Category::findOrFail($id);
            $this->cat_name = $cat->name;
            $this->cat_description = $cat->description;
        }

        $this->isCategoryFormOpen = true;
    }

    public function closeCategoryForm()
    {
        $this->isCategoryFormOpen = false;
    }

    public function saveCategory()
    {
        $this->checkCatalogPermission();
        $this->validate();

        Category::updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->cat_name,
                'description' => $this->cat_description,
            ]
        );

        session()->flash('success', $this->categoryId ? 'Category updated.' : 'Category created.');
        $this->closeCategoryForm();
    }

    // Item CRUD Helpers
    public function openItemForm($id = null)
    {
        $this->checkCatalogPermission();
        $this->resetValidation();
        $this->itemId = null;
        $this->item_category_id = '';
        $this->item_name = '';
        $this->item_sku = '';
        $this->item_size_attribute = '';
        $this->item_description = '';
        $this->item_min_quantity = 10;
        $this->item_unit_of_measure = 'pcs';
        $this->item_is_trackable = true;

        if ($id) {
            $this->itemId = $id;
            $item = Item::findOrFail($id);
            $this->item_category_id = $item->category_id;
            $this->item_name = $item->name;
            $this->item_sku = $item->sku;
            $this->item_size_attribute = $item->size_attribute;
            $this->item_description = $item->description;
            $this->item_min_quantity = $item->min_quantity;
            $this->item_unit_of_measure = $item->unit_of_measure;
            $this->item_is_trackable = $item->is_trackable;
        }

        $this->isItemFormOpen = true;
    }

    public function closeItemForm()
    {
        $this->isItemFormOpen = false;
    }

    public function saveItem()
    {
        $this->checkCatalogPermission();
        $this->validate();

        Item::updateOrCreate(
            ['id' => $this->itemId],
            [
                'category_id' => $this->item_category_id,
                'name' => $this->item_name,
                'sku' => $this->item_sku,
                'size_attribute' => $this->item_size_attribute,
                'description' => $this->item_description,
                'min_quantity' => $this->item_min_quantity,
                'unit_of_measure' => $this->item_unit_of_measure,
                'is_trackable' => $this->item_is_trackable,
            ]
        );

        session()->flash('success', $this->itemId ? 'Item details updated.' : 'Item successfully registered.');
        $this->closeItemForm();
    }

    // Delete Helpers
    public function confirmDeletion($id, $type)
    {
        $this->checkCatalogPermission();
        $this->confirmingDeletionId = $id;
        $this->confirmingDeletionType = $type;
    }

    public function cancelDeletion()
    {
        $this->confirmingDeletionId = null;
        $this->confirmingDeletionType = '';
    }

    public function deleteCatalogRecord()
    {
        $this->checkCatalogPermission();
        if ($this->confirmingDeletionId && $this->confirmingDeletionType) {
            if ($this->confirmingDeletionType === 'category') {
                $category = Category::findOrFail($this->confirmingDeletionId);
                if ($category->items()->count() > 0) {
                    session()->flash('error', 'Cannot delete Category: It contains active inventory products.');
                } else {
                    $category->delete();
                    session()->flash('success', 'Category deleted successfully.');
                }
            } else {
                $item = Item::findOrFail($this->confirmingDeletionId);
                if ($item->batches()->count() > 0) {
                    session()->flash('error', 'Cannot delete Item: It is linked to active inventory stock batches.');
                } else {
                    $item->delete();
                    session()->flash('success', 'Catalog Item deleted successfully.');
                }
            }
            $this->cancelDeletion();
        }
    }

    public function render()
    {
        $categoriesList = Category::withCount('items')->orderBy('name', 'asc')->get();

        $itemsQuery = Item::with(['category', 'batches']);

        if ($this->search) {
            $itemsQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('size_attribute', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterCategory) {
            $itemsQuery->where('category_id', $this->filterCategory);
        }

        $itemsList = $itemsQuery->orderBy('name', 'asc')->paginate(10);

        return view('livewire.catalog-component', [
            'categoriesList' => $categoriesList,
            'itemsList' => $itemsList,
        ])->layout('components.layouts.app');
    }
}
