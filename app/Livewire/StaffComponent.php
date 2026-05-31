<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class StaffComponent extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Search and Filters
    public $search = '';
    public $filterRank = '';
    public $filterStatus = '';

    // Form Fields
    public $staffId = null;
    public $belt_no = '';
    public $cnic = '';
    public $first_name = '';
    public $last_name = '';
    public $rank = '';
    public $gender = 'male';
    public $phone_no = '';
    public $current_posting = '';
    public $status = 'active';

    // Modal Control Flags
    public $isFormOpen = false;
    public $confirmingDeletionId = null;
    public $isImportOpen = false;
    public $csvFile;

    protected function rules()
    {
        return [
            'belt_no' => 'required|string|max:50|unique:staff,belt_no,' . $this->staffId,
            'cnic' => 'required|string|max:15|unique:staff,cnic,' . $this->staffId . '|regex:/^\d{5}-\d{7}-\d{1}$/',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'rank' => 'required|string|max:50',
            'gender' => 'required|in:male,female',
            'phone_no' => 'required|string|max:20',
            'current_posting' => 'required|string|max:150',
            'status' => 'required|in:active,suspended,retired,transferred',
        ];
    }

    protected $messages = [
        'cnic.regex' => 'The CNIC format must be XXXXX-XXXXXXX-X (including dashes).',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openForm($id = null)
    {
        if (!auth()->user()->can('manage-staff')) {
            abort(403, 'Unauthorized access to CTPF staff management.');
        }

        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $this->staffId = $id;
            $staff = Staff::findOrFail($id);
            $this->belt_no = $staff->belt_no;
            $this->cnic = $staff->cnic;
            $this->first_name = $staff->first_name;
            $this->last_name = $staff->last_name;
            $this->rank = $staff->rank;
            $this->gender = $staff->gender;
            $this->phone_no = $staff->phone_no;
            $this->current_posting = $staff->current_posting;
            $this->status = $staff->status;
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
        $this->staffId = null;
        $this->belt_no = '';
        $this->cnic = '';
        $this->first_name = '';
        $this->last_name = '';
        $this->rank = '';
        $this->gender = 'male';
        $this->phone_no = '';
        $this->current_posting = '';
        $this->status = 'active';
    }

    public function saveStaff()
    {
        if (!auth()->user()->can('manage-staff')) {
            abort(403, 'Unauthorized access to CTPF staff management.');
        }

        $this->validate();

        Staff::updateOrCreate(
            ['id' => $this->staffId],
            [
                'belt_no' => $this->belt_no,
                'cnic' => $this->cnic,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'rank' => $this->rank,
                'gender' => $this->gender,
                'phone_no' => $this->phone_no,
                'current_posting' => $this->current_posting,
                'status' => $this->status,
            ]
        );

        session()->flash('success', $this->staffId ? 'Warden profile updated successfully.' : 'New Warden registered successfully.');
        
        $this->closeForm();
    }

    public function confirmDeletion($id)
    {
        if (!auth()->user()->can('manage-staff')) {
            abort(403, 'Unauthorized access to CTPF staff management.');
        }
        $this->confirmingDeletionId = $id;
    }

    public function cancelDeletion()
    {
        $this->confirmingDeletionId = null;
    }

    public function deleteStaff()
    {
        if (!auth()->user()->can('manage-staff')) {
            abort(403, 'Unauthorized access to CTPF staff management.');
        }

        if ($this->confirmingDeletionId) {
            $staff = Staff::findOrFail($this->confirmingDeletionId);
            $staff->delete();
            session()->flash('success', 'Warden profile deleted successfully.');
            $this->confirmingDeletionId = null;
        }
    }

    public function openImport()
    {
        if (!auth()->user()->can('manage-staff')) {
            abort(403, 'Unauthorized access to CTPF staff management.');
        }
        $this->resetValidation();
        $this->csvFile = null;
        $this->isImportOpen = true;
    }

    public function closeImport()
    {
        $this->isImportOpen = false;
        $this->csvFile = null;
    }

    public function importStaff()
    {
        if (!auth()->user()->can('manage-staff')) {
            abort(403, 'Unauthorized access to CTPF staff management.');
        }

        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->csvFile->getRealPath();
        $file = fopen($path, 'r');

        // Read header
        $header = fgetcsv($file);
        if (!$header) {
            session()->flash('error', 'The uploaded CSV file is empty.');
            fclose($file);
            return;
        }

        // Required headers mapping
        $expectedHeaders = ['belt_no', 'cnic', 'first_name', 'last_name', 'rank', 'gender', 'phone_no', 'current_posting'];
        
        $header = array_map(function($h) {
            return strtolower(trim($h));
        }, $header);

        // Validate headers exist
        foreach ($expectedHeaders as $expected) {
            if (!in_array($expected, $header)) {
                session()->flash('error', "CSV must contain the column header: '{$expected}'");
                fclose($file);
                return;
            }
        }

        $importedCount = 0;
        $errors = [];

        DB::transaction(function () use ($file, $header, &$importedCount, &$errors) {
            $rowNum = 1;
            while (($row = fgetcsv($file)) !== false) {
                $rowNum++;
                
                // Pad or slice row array to match header length
                if (count($row) !== count($header)) {
                    $errors[] = "Row {$rowNum}: Columns count mismatch.";
                    continue;
                }

                $data = array_combine($header, $row);

                // Trim all values
                $data = array_map('trim', $data);

                $belt = $data['belt_no'];
                $cnic = $data['cnic'];

                // Basic validation checks
                if (empty($belt) || empty($cnic) || empty($data['first_name']) || empty($data['last_name'])) {
                    $errors[] = "Row {$rowNum}: Missing mandatory fields.";
                    continue;
                }

                // Unique checks in current DB
                if (Staff::where('belt_no', $belt)->exists()) {
                    $errors[] = "Row {$rowNum}: Belt #{$belt} already registered.";
                    continue;
                }

                if (Staff::where('cnic', $cnic)->exists()) {
                    $errors[] = "Row {$rowNum}: CNIC {$cnic} already registered.";
                    continue;
                }

                Staff::create([
                    'belt_no' => $belt,
                    'cnic' => $cnic,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'rank' => $data['rank'] ?: 'Traffic Warden',
                    'gender' => strtolower($data['gender']) === 'female' ? 'female' : 'male',
                    'phone_no' => $data['phone_no'] ?: 'N/A',
                    'current_posting' => $data['current_posting'] ?: 'Headquarters, Faisalabad',
                    'status' => 'active'
                ]);

                $importedCount++;
            }
        });

        fclose($file);

        if (!empty($errors)) {
            $errorMsg = implode(', ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $errorMsg .= ' and ' . (count($errors) - 3) . ' more issues.';
            }
            session()->flash('error', "Imported {$importedCount} Wardens. Issues: {$errorMsg}");
        } else {
            session()->flash('success', "Successfully imported {$importedCount} Warden profiles in bulk!");
        }

        $this->closeImport();
    }

    public function render()
    {
        $query = Staff::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('belt_no', 'like', '%' . $this->search . '%')
                  ->orWhere('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('cnic', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterRank) {
            $query->where('rank', $this->filterRank);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $staffList = $query->orderBy('belt_no', 'asc')->paginate(10);
        $ranks = Staff::select('rank')->distinct()->pluck('rank');

        return view('livewire.staff-component', [
            'staffList' => $staffList,
            'ranks' => $ranks,
        ])->layout('components.layouts.app');
    }
}
