<div>
    <!-- Dashboard Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary-subtle">
        <div>
            <h1 class="h3 fw-bold text-success-emphasis mb-1">
                <i class="bi bi-shield-shaded me-1 text-success"></i> Wardi Godaam Dashboard
            </h1>
            <p class="text-muted small mb-0">City Traffic Police Faisalabad • Main Logistics Control Panel</p>
        </div>
        <div class="text-end">
            <span class="badge badge-ctpf-emerald py-2 px-3 shadow-sm rounded-pill">
                <i class="bi bi-clock-history me-1"></i> System Active
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards Grid -->
    <div class="row mb-4">
        <!-- Total Registered Items -->
        <div class="col-md-6 col-lg-3">
            <div class="card ctpf-card p-3">
                <div class="d-flex align-items-center">
                    <div class="p-3 rounded bg-success-subtle text-success me-3">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">Catalog Items</h6>
                        <h3 class="mb-0 fw-bold">{{ $totalItems }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Traffic Wardens -->
        <div class="col-md-6 col-lg-3">
            <div class="card ctpf-card p-3">
                <div class="d-flex align-items-center">
                    <div class="p-3 rounded bg-primary-subtle text-primary me-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">Active Wardens</h6>
                        <h3 class="mb-0 fw-bold">{{ $activeWardens }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-md-6 col-lg-3">
            <div class="card ctpf-card p-3" style="border-top-color: #dc3545;">
                <div class="d-flex align-items-center">
                    <div class="p-3 rounded {{ $lowStockCount > 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-secondary' }} me-3">
                        <i class="bi bi-exclamation-octagon fs-3 {{ $lowStockCount > 0 ? 'animate-pulse' : '' }}"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">Low Stock alerts</h6>
                        <h3 class="mb-0 fw-bold {{ $lowStockCount > 0 ? 'text-danger' : '' }}">{{ $lowStockCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Field Allocations -->
        <div class="col-md-6 col-lg-3">
            <div class="card ctpf-card p-3">
                <div class="d-flex align-items-center">
                    <div class="p-3 rounded bg-warning-subtle text-warning-emphasis me-3">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">Outstanding Slips</h6>
                        <h3 class="mb-0 fw-bold">{{ $activeIssuancesCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Operations Links -->
    @if(auth()->user()->can('create-issuance') || auth()->user()->can('process-return') || auth()->user()->can('create-grn') || auth()->user()->can('manage-staff'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card ctpf-card p-3">
                <h5 class="fw-bold text-success-emphasis mb-3"><i class="bi bi-lightning-charge me-1"></i> Quick Actions</h5>
                <div class="d-flex flex-wrap gap-2">
                    @can('create-issuance')
                    <a href="{{ route('issuance.create') }}" class="btn btn-ctpf-primary px-3 py-2">
                        <i class="bi bi-plus-circle me-1"></i> New Uniform Issuance
                    </a>
                    @endcan
                    @can('process-return')
                    <a href="{{ route('returns.index') }}" class="btn btn-ctpf-outline px-3 py-2">
                        <i class="bi bi-arrow-left-right me-1"></i> Process Returns/Exchanges
                    </a>
                    @endcan
                    @can('create-grn')
                    <a href="{{ route('grn.index') }}" class="btn btn-outline-secondary fw-semibold px-3 py-2">
                        <i class="bi bi-journal-plus me-1"></i> Create Goods Note (GRN)
                    </a>
                    @endcan
                    @can('manage-staff')
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary fw-semibold px-3 py-2">
                        <i class="bi bi-person-plus me-1"></i> Register New Officer
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Analytics and Alerts Row -->
    <div class="row mb-4">
        <!-- Stock Distribution Chart -->
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="card ctpf-card h-100 mb-0 shadow-sm">
                <div class="ctpf-card-header d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold text-success-emphasis"><i class="bi bi-pie-chart-fill me-1"></i> Stock Distribution by Category</span>
                    <span class="badge badge-ctpf-emerald rounded-pill px-2.5 py-1">Analytics</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center" style="min-height: 380px;">
                    @if(empty($chartCounts))
                        <div class="text-center text-muted">
                            <i class="bi bi-bar-chart fs-1 mb-2 d-block text-secondary"></i>
                            <span class="small fw-semibold">No stock data available to display chart.</span>
                        </div>
                    @else
                        <div id="categoryChart" style="width: 100%; max-width: 450px;"></div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts Warning Widget -->
        <div class="col-lg-5">
            <div class="card ctpf-card h-100 mb-0 shadow-sm border-danger">
                <div class="ctpf-card-header bg-danger-subtle py-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-danger-emphasis fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Critical Low Stock Alerts</span>
                    <span class="badge bg-danger rounded-pill px-2 py-1">{{ $lowStockItems->count() }}</span>
                </div>
                <div class="card-body p-0" style="max-height: 380px; overflow-y: auto;">
                    @if($lowStockItems->isEmpty())
                        <div class="p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center h-100">
                            <i class="bi bi-check2-circle text-success fs-1 mb-2"></i>
                            <span class="small fw-bold text-success">Optimal Inventory!</span>
                            <span class="text-muted small mt-1">All catalog items satisfy threshold safety margins.</span>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($lowStockItems as $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                                    <div>
                                        <h6 class="mb-0 fw-bold small text-dark">{{ $item->name }}</h6>
                                        <small class="text-muted text-uppercase small" style="font-size: 0.75rem;">{{ $item->category->name }} • Size: {{ $item->size_attribute ?? 'N/A' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill mb-1 d-inline-block px-2.5 py-1">
                                            Current: {{ $item->available_stock }} {{ $item->unit_of_measure }}
                                        </span>
                                        <small class="text-muted d-block small" style="font-size: 0.7rem;">Safety Min: {{ $item->min_quantity }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Issuances Activity Log (Full-Width) -->
    <div class="row">
        <div class="col-12">
            <div class="card ctpf-card shadow-sm">
                <div class="ctpf-card-header d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold text-success-emphasis"><i class="bi bi-file-earmark-text-fill me-1"></i> Recent Inventory Issuances</span>
                    <a href="{{ route('issuance.create') }}" class="btn btn-sm btn-ctpf-primary px-3 fw-bold rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i> New Issuance
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentIssuances->isEmpty())
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-inbox fs-1 mb-2 d-block text-secondary"></i>
                            <span class="small fw-semibold">No stock issuances recorded yet. Click 'New Issuance' to start.</span>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Slip #</th>
                                        <th class="py-3 border-0">Officer Name</th>
                                        <th class="py-3 border-0">Belt No</th>
                                        <th class="py-3 border-0">Rank / Assignment</th>
                                        <th class="py-3 border-0">Date Issued</th>
                                        <th class="py-3 border-0">Status</th>
                                        <th class="py-3 border-0 text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentIssuances as $issuance)
                                        <tr>
                                            <td class="ps-4 fw-bold text-success-emphasis py-3">{{ $issuance->issuance_slip_no }}</td>
                                            <td class="fw-semibold text-dark">{{ $issuance->staff->full_name }}</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 fw-bold">{{ $issuance->staff->belt_no }}</span></td>
                                            <td class="text-secondary">{{ $issuance->staff->rank }}</td>
                                            <td class="text-secondary small">{{ $issuance->issuance_date->format('d M, Y') }}</td>
                                            <td>
                                                @if($issuance->status === 'issued')
                                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small fw-bold">Issued</span>
                                                @elseif($issuance->status === 'approved')
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 small fw-bold">Approved</span>
                                                @elseif($issuance->status === 'pending')
                                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 small fw-bold">Pending</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 small fw-bold">Rejected</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('returns.index') }}" class="btn btn-sm btn-light border fw-semibold" title="Process Returns">
                                                    <i class="bi bi-arrow-left-right text-success"></i> Return
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Script assets for dynamic visual chart rendering -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector("#categoryChart")) {
                var options = {
                    chart: {
                        type: 'donut',
                        height: 350,
                        fontFamily: 'Inter, sans-serif'
                    },
                    series: @json($chartCounts),
                    labels: @json($chartCategories),
                    colors: ['#0b3c24', '#d4af37', '#2e7d32', '#1b5e20', '#164f33', '#b89428', '#ffc107'],
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '12px',
                        offsetY: 0,
                        markers: {
                            radius: 12
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '14px',
                                        fontWeight: 600,
                                        color: '#2d3748'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '20px',
                                        fontWeight: 700,
                                        color: '#0b3c24',
                                        formatter: function (val) {
                                            return val;
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total Items',
                                        color: '#718096',
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => {
                                                return a + b
                                            }, 0)
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 280
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                var chart = new ApexCharts(document.querySelector("#categoryChart"), options);
                chart.render();
            }
        });
    </script>
</div>
