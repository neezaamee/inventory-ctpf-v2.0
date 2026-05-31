<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;

class AuditLogComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $filterEvent = '';
    public $filterModel = '';

    // Active audit details
    public $selectedAuditId = null;
    public $activeAudit = null;

    // Modal Control Flags
    public $isDetailModalOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAuditDetails($id)
    {
        $this->selectedAuditId = $id;
        $this->activeAudit = Audit::with('user')->findOrFail($id);
        $this->isDetailModalOpen = true;
    }

    public function closeAuditDetails()
    {
        $this->isDetailModalOpen = false;
        $this->selectedAuditId = null;
        $this->activeAudit = null;
    }

    public function render()
    {
        // Restrict based on view-audit-logs Spatie permission check
        if (!auth()->user()->can('view-audit-logs')) {
            abort(403, 'Unauthorized access to CTPF audit logs.');
        }

        $query = Audit::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('event', 'like', '%' . $this->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                  ->orWhere('auditable_type', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }

        if ($this->filterModel) {
            $query->where('auditable_type', $this->filterModel);
        }

        $audits = $query->orderBy('id', 'desc')->paginate(15);

        // Fetch distinct models and events for filters
        $distinctModels = Audit::select('auditable_type')->distinct()->pluck('auditable_type')
            ->map(function ($type) {
                return [
                    'class' => $type,
                    'short' => class_basename($type)
                ];
            });

        return view('livewire.audit-log-component', [
            'audits' => $audits,
            'distinctModels' => $distinctModels,
        ])->layout('components.layouts.app');
    }
}
