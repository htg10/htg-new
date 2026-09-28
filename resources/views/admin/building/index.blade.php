@extends('layouts.backend.app')

@section('meta')
    <title>Rent Management | Admin</title>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0 font-size-18">Rent Management</h4>
                        <span class="htg-page-sub">Properties, tenants &amp; rent collection</span>
                    </div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <form method="GET" action="{{ route('admin.rent.index') }}" class="d-flex gap-2 align-items-center">
                            <input type="month" name="month" value="{{ $month }}" class="form-control" style="width:170px" onchange="this.form.submit()">
                        </form>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
                            <i class="bx bx-plus me-1"></i> Add Building
                        </button>
                        <a href="{{ route('admin.rent.create') }}" class="btn btn-success">
                            <i class="bx bx-rupee me-1"></i> Record Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Strip --}}
        <div class="htg-rent-summary">
            <div class="htg-rent-summary__item">
                <span class="htg-rent-summary__label"><i class="bx bx-building-house"></i> Buildings</span>
                <strong class="htg-rent-summary__value">{{ $totalProperties }}</strong>
            </div>
            <div class="htg-rent-summary__item">
                <span class="htg-rent-summary__label"><i class="bx bx-group"></i> Active Tenants</span>
                <strong class="htg-rent-summary__value">{{ $totalTenants }}</strong>
            </div>
            <div class="htg-rent-summary__item">
                <span class="htg-rent-summary__label"><i class="bx bx-calendar"></i> Monthly Rent</span>
                <strong class="htg-rent-summary__value"><span class="htg-cur">₹</span>{{ number_format($totalMonthlyRent, 0) }}</strong>
            </div>
            <div class="htg-rent-summary__item htg-rent-summary__item--ok">
                <span class="htg-rent-summary__label"><i class="bx bx-check-circle"></i> Collected</span>
                <strong class="htg-rent-summary__value"><span class="htg-cur">₹</span>{{ number_format($totalCollected, 0) }}</strong>
            </div>
            <div class="htg-rent-summary__item htg-rent-summary__item--warn">
                <span class="htg-rent-summary__label"><i class="bx bx-time-five"></i> Pending</span>
                <strong class="htg-rent-summary__value"><span class="htg-cur">₹</span>{{ number_format($totalPending, 0) }}</strong>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Property Cards --}}
        @if($properties->count())
            <div class="htg-prop-grid">
                @foreach($properties as $property)
                    @php
                        $pct = $property->monthly_rent > 0 ? round(($property->collected / $property->monthly_rent) * 100) : 0;
                        $pct = min($pct, 100);
                    @endphp
                    <div class="htg-prop-card {{ !$property->is_active ? 'htg-prop-card--inactive' : '' }}">
                        {{-- Card Header --}}
                        <div class="htg-prop-card__head">
                            <div class="htg-prop-card__icon">
                                <i class="bx bx-building-house"></i>
                            </div>
                            <div class="htg-prop-card__info">
                                <h5 class="htg-prop-card__name">{{ $property->name }}</h5>
                                @if($property->address)
                                    <span class="htg-prop-card__addr">{{ $property->address }}</span>
                                @endif
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded" style="font-size:20px"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="editProperty({{ $property->id }}, '{{ addslashes($property->name) }}', '{{ addslashes($property->address ?? '') }}')">
                                            <i class="bx bx-edit me-2"></i> Edit Building
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="addTenantTo({{ $property->id }}, '{{ addslashes($property->name) }}')">
                                            <i class="bx bx-user-plus me-2"></i> Add Tenant
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.rent.property.delete', $property->id) }}" method="POST"
                                              onsubmit="return confirm('Delete this building and all its tenants?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-2"></i> Delete Building
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Stats Row --}}
                        <div class="htg-prop-card__stats">
                            <div class="htg-prop-card__stat-box htg-prop-card__stat-box--ok">
                                <span>Collected</span>
                                <strong><span class="htg-cur">₹</span>{{ number_format($property->collected, 0) }}</strong>
                            </div>
                            @php $pending = $property->monthly_rent - $property->collected; @endphp
                            <div class="htg-prop-card__stat-box {{ $pending > 0 ? 'htg-prop-card__stat-box--pending' : '' }}">
                                <span>Pending</span>
                                <strong><span class="htg-cur">₹</span>{{ number_format(max($pending, 0), 0) }}</strong>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="htg-prop-card__progress">
                            <div class="htg-prop-card__bar">
                                <div class="htg-prop-card__bar-fill {{ $pct >= 100 ? 'htg-prop-card__bar-fill--full' : '' }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="htg-prop-card__pct">{{ $pct }}%</span>
                        </div>

                        {{-- Tenant List --}}
                        <div class="htg-prop-card__tenants">
                            <div class="htg-prop-card__tenants-head">
                                <span><i class="bx bx-group me-1"></i> Tenants</span>
                                <span class="badge bg-primary">{{ $property->active_tenants_count }}</span>
                            </div>
                            @forelse($property->tenants as $tenant)
                                @php
                                    $isPaid = $tenant->month_paid >= $tenant->rent_amount && $tenant->rent_amount > 0;
                                    $isPartial = $tenant->month_paid > 0 && $tenant->month_paid < $tenant->rent_amount;
                                @endphp
                                <div class="htg-tenant {{ !$tenant->is_active ? 'htg-tenant--off' : '' }}">
                                    <div class="htg-tenant__info">
                                        <strong class="htg-tenant__name">{{ $tenant->name }}</strong>
                                        @if($tenant->unit)
                                            <span class="htg-tenant__unit">{{ $tenant->unit }}</span>
                                        @endif
                                    </div>
                                    <div class="htg-tenant__rent">
                                        <span class="htg-cur">₹</span>{{ number_format($tenant->rent_amount, 0) }}
                                    </div>
                                    <div class="htg-tenant__status">
                                        @if(!$tenant->is_active)
                                            <span class="badge bg-secondary">Inactive</span>
                                        @elseif($isPaid)
                                            <span class="badge bg-success"><i class="bx bx-check"></i> Paid</span>
                                        @elseif($isPartial)
                                            <span class="badge bg-warning">Partial ₹{{ number_format($tenant->month_paid, 0) }}</span>
                                        @else
                                            <span class="badge bg-danger">Pending</span>
                                        @endif
                                    </div>
                                    <div class="htg-tenant__actions dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded" style="font-size:18px"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                   onclick="editTenant({{ $tenant->id }}, '{{ addslashes($tenant->name) }}', '{{ addslashes($tenant->mobile ?? '') }}', '{{ addslashes($tenant->unit ?? '') }}', {{ $tenant->rent_amount }})">
                                                    <i class="bx bx-edit me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.rent.tenant.toggle', $tenant->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bx {{ $tenant->is_active ? 'bx-hide' : 'bx-show' }} me-2"></i>
                                                        {{ $tenant->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            </li>
                                            @if($tenant->is_active && !$isPaid)
                                                <li>
                                                    <a class="dropdown-item text-success" href="{{ route('admin.rent.create') }}?tenant_id={{ $tenant->id }}">
                                                        <i class="bx bx-rupee me-2"></i> Record Payment
                                                    </a>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.rent.tenant.delete', $tenant->id) }}" method="POST"
                                                      onsubmit="return confirm('Remove this tenant?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bx bx-trash me-2"></i> Remove
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @empty
                                <div class="htg-prop-card__empty">
                                    <i class="bx bx-user-plus"></i>
                                    <span>No tenants yet</span>
                                </div>
                            @endforelse
                        </div>

                        {{-- Card Footer --}}
                        <div class="htg-prop-card__foot">
                            <button class="btn btn-sm btn-soft-primary" onclick="addTenantTo({{ $property->id }}, '{{ addslashes($property->name) }}')">
                                <i class="bx bx-user-plus"></i> Add Tenant
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="htg-empty">
                <i class="bx bx-building-house"></i>
                <p>
                    <strong>No buildings yet</strong>
                    Add your first building to start managing rent
                </p>
            </div>
        @endif

    </div>
</div>

{{-- Add Property Modal --}}
<div class="modal fade" id="addPropertyModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.rent.property.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Building Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. C 41/2 Third Floor" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Full address (optional)">
                </div>
                <div class="mb-0">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Notes (optional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Building</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Property Modal --}}
<div class="modal fade" id="editPropertyModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editPropertyForm" method="POST" class="modal-content">
            @csrf @method('PATCH')
            <div class="modal-header">
                <h5 class="modal-title">Edit Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Building Name *</label>
                    <input type="text" name="name" id="editPropName" class="form-control" required>
                </div>
                <div class="mb-0">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" id="editPropAddress" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Tenant Modal --}}
<div class="modal fade" id="addTenantModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.rent.tenant.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="property_id" id="tenantPropertyId">
            <div class="modal-header">
                <h5 class="modal-title">Add Tenant to <span id="tenantPropertyName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tenant Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Full name" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" class="form-control" placeholder="10-digit number">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit / Flat No.</label>
                        <input type="text" name="unit" class="form-control" placeholder="e.g. Flat 3B">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Monthly Rent *</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="rent_amount" class="form-control" placeholder="0" min="0" step="100" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Tenant</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Tenant Modal --}}
<div class="modal fade" id="editTenantModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editTenantForm" method="POST" class="modal-content">
            @csrf @method('PATCH')
            <div class="modal-header">
                <h5 class="modal-title">Edit Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tenant Name *</label>
                    <input type="text" name="name" id="editTenantName" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" id="editTenantMobile" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit / Flat No.</label>
                        <input type="text" name="unit" id="editTenantUnit" class="form-control">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Monthly Rent *</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="rent_amount" id="editTenantRent" class="form-control" min="0" step="100" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
function editProperty(id, name, address) {
    document.getElementById('editPropertyForm').action = '/admin/rent/property/' + id;
    document.getElementById('editPropName').value = name;
    document.getElementById('editPropAddress').value = address;
    new bootstrap.Modal(document.getElementById('editPropertyModal')).show();
}

function addTenantTo(propertyId, propertyName) {
    document.getElementById('tenantPropertyId').value = propertyId;
    document.getElementById('tenantPropertyName').textContent = propertyName;
    new bootstrap.Modal(document.getElementById('addTenantModal')).show();
}

function editTenant(id, name, mobile, unit, rent) {
    document.getElementById('editTenantForm').action = '/admin/rent/tenant/' + id;
    document.getElementById('editTenantName').value = name;
    document.getElementById('editTenantMobile').value = mobile;
    document.getElementById('editTenantUnit').value = unit;
    document.getElementById('editTenantRent').value = rent;
    new bootstrap.Modal(document.getElementById('editTenantModal')).show();
}
</script>
@endsection
