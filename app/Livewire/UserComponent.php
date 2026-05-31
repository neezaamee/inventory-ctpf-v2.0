<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filter
    public $search = '';

    // Form Fields
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $selectedRole = '';

    // Modal Control Flags
    public $isFormOpen = false;
    public $confirmingDeletionId = null;

    protected function rules()
    {
        $passwordRule = $this->userId ? 'nullable|string|min:6' : 'required|string|min:6';
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $this->userId,
            'password' => $passwordRule,
            'selectedRole' => 'required|exists:roles,name',
        ];
    }

    public function mount()
    {
        // Guard access
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'Unauthorized access to CTPF user management.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $this->userId = $id;
            $user = User::findOrFail($id);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->selectedRole = $user->roles->first()?->name ?? '';
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
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRole = '';
    }

    public function saveUser()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'Unauthorized access.');
        }

        $this->validate();

        DB::transaction(function () {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $user = User::updateOrCreate(
                ['id' => $this->userId],
                $data
            );

            // Sync user Spatie roles
            $user->syncRoles([$this->selectedRole]);
        });

        session()->flash('success', $this->userId ? 'System User updated successfully.' : 'New System User registered successfully.');
        
        $this->closeForm();
    }

    public function confirmDeletion($id)
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'Unauthorized access.');
        }

        // Prevent self deletion
        if (auth()->id() == $id) {
            session()->flash('error', 'Cannot delete your own active administrator account.');
            return;
        }

        $this->confirmingDeletionId = $id;
    }

    public function cancelDeletion()
    {
        $this->confirmingDeletionId = null;
    }

    public function deleteUser()
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'Unauthorized access.');
        }

        if ($this->confirmingDeletionId) {
            $user = User::findOrFail($this->confirmingDeletionId);
            $user->delete();
            session()->flash('success', 'System User deleted successfully.');
            $this->confirmingDeletionId = null;
        }
    }

    public function render()
    {
        $query = User::with('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $usersList = $query->orderBy('name', 'asc')->paginate(10);
        $rolesList = Role::orderBy('name', 'asc')->get();

        return view('livewire.user-component', [
            'usersList' => $usersList,
            'rolesList' => $rolesList,
        ])->layout('components.layouts.app');
    }
}
