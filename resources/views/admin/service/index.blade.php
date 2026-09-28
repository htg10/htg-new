@extends('layouts.backend.app')

@section('meta')
    <title>Services | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Services
                            <span class="htg-page-sub">Manage the products and services available in contracts</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Add new --}}
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Add New Service</h5>
                            <form method="POST" action="{{ route('service.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Service Name *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           placeholder="e.g. Google Ads Management" required value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button class="btn btn-primary w-100">
                                    <i class="bx bx-plus me-1"></i>Add Service
                                </button>
                            </form>

                            <hr class="my-4">

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success-subtle text-success">{{ $services->where('is_active', true)->count() }} Active</span>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $services->where('is_active', false)->count() }} Inactive</span>
                                <span class="badge bg-primary-subtle text-primary">{{ $services->count() }} Total</span>
                            </div>
                            <p class="text-muted mb-0" style="font-size:12px">
                                Drag to reorder. Toggle the switch to hide a service from contract forms without deleting it.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Service list --}}
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="htg-svc-header">
                                <span class="htg-svc-header__col">#</span>
                                <span class="htg-svc-header__col htg-svc-header__col--name">Service Name</span>
                                <span class="htg-svc-header__col">Status</span>
                                <span class="htg-svc-header__col">Actions</span>
                            </div>

                            <div id="serviceList">
                                @forelse ($services as $service)
                                    <div class="htg-svc-row {{ !$service->is_active ? 'htg-svc-row--off' : '' }}" data-id="{{ $service->id }}">
                                        <span class="htg-svc-row__drag" title="Drag to reorder">
                                            <i class="bx bx-grid-vertical"></i>
                                        </span>
                                        <span class="htg-svc-row__order">{{ $loop->iteration }}</span>

                                        <span class="htg-svc-row__name" id="name-display-{{ $service->id }}">
                                            {{ $service->name }}
                                        </span>

                                        <form class="htg-svc-row__edit-form d-none" id="name-form-{{ $service->id }}"
                                              method="POST" action="{{ route('service.update', $service) }}">
                                            @csrf
                                            <input type="text" name="name" value="{{ $service->name }}" class="form-control form-control-sm">
                                            <button type="submit" class="btn btn-sm btn-success"><i class="bx bx-check"></i></button>
                                            <button type="button" class="btn btn-sm btn-light" onclick="cancelEdit({{ $service->id }})"><i class="bx bx-x"></i></button>
                                        </form>

                                        <span class="htg-svc-row__status">
                                            <form method="POST" action="{{ route('service.toggle', $service) }}">
                                                @csrf
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" onchange="this.form.submit()"
                                                           {{ $service->is_active ? 'checked' : '' }}>
                                                </div>
                                            </form>
                                        </span>

                                        <span class="htg-svc-row__actions">
                                            <button class="btn btn-sm btn-soft-primary" onclick="startEdit({{ $service->id }})" title="Edit">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                            <form method="POST" action="{{ route('service.destroy', $service) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete {{ $service->name }}?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-soft-danger" title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </span>
                                    </div>
                                @empty
                                    <div class="htg-empty py-5">
                                        <i class="bx bx-package"></i>
                                        <p><strong>No services yet</strong><br>Add your first service using the form on the left.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        function startEdit(id) {
            document.getElementById('name-display-' + id).classList.add('d-none');
            document.getElementById('name-form-' + id).classList.remove('d-none');
            document.getElementById('name-form-' + id).querySelector('input').focus();
        }

        function cancelEdit(id) {
            document.getElementById('name-display-' + id).classList.remove('d-none');
            document.getElementById('name-form-' + id).classList.add('d-none');
        }

        const list = document.getElementById('serviceList');
        if (list && list.children.length > 0) {
            Sortable.create(list, {
                handle: '.htg-svc-row__drag',
                animation: 150,
                ghostClass: 'htg-svc-row--ghost',
                onEnd: function () {
                    const order = [...list.querySelectorAll('.htg-svc-row')].map(el => el.dataset.id);
                    fetch("{{ route('service.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order })
                    });

                    list.querySelectorAll('.htg-svc-row__order').forEach((el, i) => {
                        el.textContent = i + 1;
                    });
                }
            });
        }
    </script>
@endsection
