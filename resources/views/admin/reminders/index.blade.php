@extends('layouts.backend.app')

@section('meta')
    <title>Reminders | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Auto-Reminders
                            <span class="htg-page-sub">Renewal & expiry notifications via SMS, Email, WhatsApp</span>
                        </h4>
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.reminders.run') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm"
                                    onclick="return confirm('Process all active reminder rules now?')">
                                    <i class="bx bx-play-circle me-1"></i> Run Now
                                </button>
                            </form>
                            <a href="{{ route('admin.reminders.logs') }}" class="btn btn-light btn-sm">
                                <i class="bx bx-history me-1"></i> Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash --}}
            @if (session('success'))
                <div class="alert alert-success"><i class="bx bx-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger"><i class="bx bx-error"></i> {{ session('error') }}</div>
            @endif

            {{-- Stats --}}
            <div class="htg-wa-stats">
                <div class="htg-wa-stats__item">
                    <i class="bx bx-time-five"></i>
                    <div>
                        <span>Expiring (30d)</span>
                        <strong>{{ $totalUpcoming }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item htg-wa-stats__item--bad">
                    <i class="bx bx-error-circle"></i>
                    <div>
                        <span>Urgent (7d)</span>
                        <strong>{{ $urgentCount }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item htg-wa-stats__item--ok">
                    <i class="bx bx-check-circle"></i>
                    <div>
                        <span>Sent Today</span>
                        <strong>{{ $sentToday }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item">
                    <i class="bx bx-x-circle"></i>
                    <div>
                        <span>Failed Today</span>
                        <strong>{{ $failedToday }}</strong>
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- Reminder Rules --}}
                <div class="col-lg-5 mb-4">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span><i class="bx bx-bell me-1"></i> Reminder Rules</span>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                                <i class="bx bx-plus me-1"></i> Add Rule
                            </button>
                        </div>
                        <div class="card-body p-0">
                            @forelse ($rules as $rule)
                                <div class="htg-rule {{ !$rule->is_active ? 'htg-rule--off' : '' }}">
                                    <div class="htg-rule__info">
                                        <strong>{{ $rule->name }}</strong>
                                        <span class="htg-rule__meta">
                                            @if ($rule->type === 'before_expiry')
                                                {{ $rule->days }}d before
                                            @elseif ($rule->type === 'on_expiry')
                                                On expiry day
                                            @else
                                                {{ $rule->days }}d after
                                            @endif
                                        </span>
                                    </div>
                                    <div class="htg-rule__channels">
                                        @foreach ($rule->channels as $ch)
                                            <span class="htg-rule__ch htg-rule__ch--{{ $ch }}">
                                                @if ($ch === 'sms') <i class="bx bx-message-dots"></i>
                                                @elseif ($ch === 'email') <i class="bx bx-envelope"></i>
                                                @else <i class="bx bxl-whatsapp"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="htg-rule__actions">
                                        <form method="POST" action="{{ route('admin.reminders.rule.toggle', $rule->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm {{ $rule->is_active ? 'btn-soft-success' : 'btn-soft-danger' }} htg-act"
                                                title="{{ $rule->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bx {{ $rule->is_active ? 'bx-toggle-right' : 'bx-toggle-left' }}"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-soft-primary htg-act" title="Edit"
                                            onclick="editRule({{ json_encode($rule) }})">
                                            <i class="bx bx-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.reminders.rule.delete', $rule->id) }}" class="d-inline"
                                            onsubmit="return confirm('Remove this rule?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-soft-danger htg-act" title="Remove">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="htg-empty">
                                    <i class="bx bx-bell-off"></i>
                                    <p><strong>No rules</strong> Add a reminder rule to get started.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Upcoming Expirations --}}
                <div class="col-lg-7 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="bx bx-time-five me-1"></i> Upcoming Expirations (Next 30 Days)
                        </div>
                        <div class="card-body p-0">
                            @if ($upcoming->isEmpty())
                                <div class="htg-empty">
                                    <i class="bx bx-check-shield"></i>
                                    <p><strong>All clear</strong> No products expiring in the next 30 days.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Client</th>
                                                <th>Product</th>
                                                <th>Expires</th>
                                                <th>Status</th>
                                                <th style="width:50px"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($upcoming as $product)
                                                @php
                                                    $daysLeft = (int) now()->diffInDays($product->expiry_date, false);
                                                    $urgency = $daysLeft <= 3 ? 'bad' : ($daysLeft <= 7 ? 'warn' : ($daysLeft <= 15 ? 'info' : ''));
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="htg-tenant__name">{{ $product->entry->company ?? '—' }}</span>
                                                        <span class="htg-tenant__unit">{{ $product->entry->contact ?? '' }}</span>
                                                    </td>
                                                    <td>{{ $product->product_name }}</td>
                                                    <td style="white-space:nowrap">
                                                        <span class="htg-when {{ $urgency ? 'is-' . ($daysLeft <= 3 ? 'due' : ($daysLeft <= 7 ? 'today' : 'soon')) : '' }}">
                                                            <strong>{{ \Carbon\Carbon::parse($product->expiry_date)->format('d M Y') }}</strong>
                                                            <span>{{ $daysLeft }}d left</span>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($urgency === 'bad')
                                                            <span class="htg-pill htg-pill--bad">Urgent</span>
                                                        @elseif ($urgency === 'warn')
                                                            <span class="htg-pill htg-pill--warn">Soon</span>
                                                        @else
                                                            <span class="htg-pill">Upcoming</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($product->entry && $product->entry->reminders_enabled)
                                                            <form method="POST" action="{{ route('admin.reminders.client.toggle', $product->entry->id) }}" class="d-inline"
                                                                title="Disable reminders for this client">
                                                                @csrf
                                                                <button class="btn btn-sm btn-soft-success htg-act">
                                                                    <i class="bx bx-bell"></i>
                                                                </button>
                                                            </form>
                                                        @elseif ($product->entry)
                                                            <form method="POST" action="{{ route('admin.reminders.client.toggle', $product->entry->id) }}" class="d-inline"
                                                                title="Enable reminders for this client">
                                                                @csrf
                                                                <button class="btn btn-sm btn-soft-danger htg-act">
                                                                    <i class="bx bx-bell-off"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recently Expired --}}
                    @if ($expired->isNotEmpty())
                        <div class="card mt-3">
                            <div class="card-header">
                                <i class="bx bx-error me-1 text-danger"></i> Recently Expired (Last 30 Days)
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Client</th>
                                                <th>Product</th>
                                                <th>Expired On</th>
                                                <th>Days Ago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($expired->take(10) as $product)
                                                <tr>
                                                    <td>
                                                        <span class="htg-tenant__name">{{ $product->entry->company ?? '—' }}</span>
                                                        <span class="htg-tenant__unit">{{ $product->entry->contact ?? '' }}</span>
                                                    </td>
                                                    <td>{{ $product->product_name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($product->expiry_date)->format('d M Y') }}</td>
                                                    <td>
                                                        <span class="htg-pill htg-pill--bad">
                                                            {{ (int) now()->diffInDays($product->expiry_date) }}d ago
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Add Rule Modal --}}
    <div class="modal fade" id="addRuleModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.reminders.rule.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Reminder Rule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rule Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. 7 Days Before" required>
                        </div>
                        <div class="row">
                            <div class="col-7 mb-3">
                                <label class="form-label">Trigger *</label>
                                <select name="type" class="form-select" required>
                                    <option value="before_expiry">Before Expiry</option>
                                    <option value="on_expiry">On Expiry Day</option>
                                    <option value="after_expiry">After Expiry</option>
                                </select>
                            </div>
                            <div class="col-5 mb-3">
                                <label class="form-label">Days</label>
                                <input type="number" name="days" class="form-control" value="7" min="0" max="365">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Channels *</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="ch-sms" checked>
                                    <label class="form-check-label" for="ch-sms">SMS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="ch-email" checked>
                                    <label class="form-check-label" for="ch-email">Email</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="ch-wa">
                                    <label class="form-check-label" for="ch-wa">WhatsApp</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">WhatsApp Template Name</label>
                            <input type="text" name="wa_template_name" class="form-control" placeholder="Leave blank for text message">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SMS DLT Template ID</label>
                            <input type="text" name="sms_template_id" class="form-control" placeholder="Leave blank for default">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Add Rule
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Rule Modal --}}
    <div class="modal fade" id="editRuleModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editRuleForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Reminder Rule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rule Name *</label>
                            <input type="text" name="name" id="edit-name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-7 mb-3">
                                <label class="form-label">Trigger *</label>
                                <select name="type" id="edit-type" class="form-select" required>
                                    <option value="before_expiry">Before Expiry</option>
                                    <option value="on_expiry">On Expiry Day</option>
                                    <option value="after_expiry">After Expiry</option>
                                </select>
                            </div>
                            <div class="col-5 mb-3">
                                <label class="form-label">Days</label>
                                <input type="number" name="days" id="edit-days" class="form-control" min="0" max="365">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Channels *</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="edit-ch-sms">
                                    <label class="form-check-label" for="edit-ch-sms">SMS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="edit-ch-email">
                                    <label class="form-check-label" for="edit-ch-email">Email</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="edit-ch-wa">
                                    <label class="form-check-label" for="edit-ch-wa">WhatsApp</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">WhatsApp Template Name</label>
                            <input type="text" name="wa_template_name" id="edit-wa-tpl" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SMS DLT Template ID</label>
                            <input type="text" name="sms_template_id" id="edit-sms-tpl" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('js')
    <script>
        function editRule(rule) {
            document.getElementById('editRuleForm').action = '/admin/reminders/rule/' + rule.id;
            document.getElementById('edit-name').value = rule.name;
            document.getElementById('edit-type').value = rule.type;
            document.getElementById('edit-days').value = rule.days;
            document.getElementById('edit-wa-tpl').value = rule.wa_template_name || '';
            document.getElementById('edit-sms-tpl').value = rule.sms_template_id || '';

            const channels = rule.channels || [];
            document.getElementById('edit-ch-sms').checked = channels.includes('sms');
            document.getElementById('edit-ch-email').checked = channels.includes('email');
            document.getElementById('edit-ch-wa').checked = channels.includes('whatsapp');

            new bootstrap.Modal(document.getElementById('editRuleModal')).show();
        }
    </script>
@endsection
