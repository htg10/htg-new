@extends('layouts.backend.app')

@section('meta')
    <title>Balance Reminders | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Balance Reminders
                            <span class="htg-page-sub">Notify clients about outstanding payment balances</span>
                        </h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkSendModal">
                                <i class="bx bx-send me-1"></i> Send Bulk
                            </button>
                            <a href="{{ route('admin.balance-reminders.logs') }}" class="btn btn-light btn-sm">
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
                <div class="htg-wa-stats__item htg-wa-stats__item--bad">
                    <i class="bx bx-rupee"></i>
                    <div>
                        <span>Total Outstanding</span>
                        <strong><span class="htg-cur"></span>{{ number_format($totalOutstanding, 0) }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item">
                    <i class="bx bx-buildings"></i>
                    <div>
                        <span>Clients</span>
                        <strong>{{ $clientCount }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item">
                    <i class="bx bx-package"></i>
                    <div>
                        <span>Products</span>
                        <strong>{{ $productCount }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item htg-wa-stats__item--ok">
                    <i class="bx bx-check-circle"></i>
                    <div>
                        <span>Sent Today / Week</span>
                        <strong>{{ $sentToday }} / {{ $sentThisWeek }}</strong>
                    </div>
                </div>
            </div>

            {{-- Filter + Table --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span><i class="bx bx-wallet me-1"></i> Outstanding Balances</span>
                    <form method="GET" class="d-flex gap-2 align-items-center">
                        <input type="text" name="search" class="form-control form-control-sm" style="width:220px"
                            placeholder="Search client, product, phone…" value="{{ request('search') }}">
                        <select name="sort" class="form-select form-select-sm" style="width:160px"
                            onchange="this.form.submit()">
                            <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Highest
                                Balance</option>
                            <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Lowest Balance
                            </option>
                            <option value="company" {{ request('sort') == 'company' ? 'selected' : '' }}>Company A–Z</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-light"><i class="bx bx-search"></i></button>
                        @if (request()->hasAny(['search', 'sort']))
                            <a href="{{ route('admin.balance-reminders.index') }}" class="btn btn-sm btn-light" title="Clear"><i
                                    class="bx bx-x"></i></a>
                        @endif
                    </form>
                </div>
                <div class="card-body p-0">
                    @if ($products->isEmpty())
                        <div class="htg-empty">
                            <i class="bx bx-check-shield"></i>
                            <p><strong>All clear</strong> No products with outstanding balance.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Client</th>
                                        <th>Product</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Balance</th>
                                        <th>Last Reminded</th>
                                        <th style="width:110px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        @php
                                            $pct = $product->total_amount > 0
                                                ? round(((float) $product->paid_amount / $product->total_amount) * 100)
                                                : 0;
                                            $lastSent = $lastSentMap[$product->id] ?? null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="htg-tenant__name">{{ $product->entry->company ?? '—' }}</span>
                                                <span class="htg-tenant__unit">{{ $product->entry->contact ?? '' }}</span>
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td class="text-end">
                                                <span class="htg-cur">₹</span>{{ number_format($product->total_amount, 0) }}
                                            </td>
                                            <td class="text-end">
                                                <span class="htg-cur">₹</span>{{ number_format((float) $product->paid_amount, 0) }}
                                                <div class="htg-bal-bar mt-1">
                                                    <div class="htg-bal-bar__fill" style="width:{{ $pct }}%"></div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-danger">
                                                    <span class="htg-cur">₹</span>{{ number_format($product->balance_amount, 0) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if ($lastSent)
                                                    <span class="htg-when">
                                                        <strong>{{ \Carbon\Carbon::parse($lastSent)->format('d M') }}</strong>
                                                        <span>{{ \Carbon\Carbon::parse($lastSent)->diffForHumans() }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted" style="font-size:12px">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-soft-primary htg-act" title="Send reminder"
                                                    onclick="sendSingle({{ $product->id }}, '{{ addslashes($product->entry->company ?? '') }}')">
                                                    <i class="bx bx-send"></i>
                                                </button>
                                                @if ($product->entry && $product->entry->reminders_enabled)
                                                    <form method="POST"
                                                        action="{{ route('admin.reminders.client.toggle', $product->entry->id) }}"
                                                        class="d-inline" title="Disable reminders for this client">
                                                        @csrf
                                                        <button class="btn btn-sm btn-soft-success htg-act">
                                                            <i class="bx bx-bell"></i>
                                                        </button>
                                                    </form>
                                                @elseif ($product->entry)
                                                    <form method="POST"
                                                        action="{{ route('admin.reminders.client.toggle', $product->entry->id) }}"
                                                        class="d-inline" title="Enable reminders for this client">
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

                        <div class="p-3">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Send Single Modal --}}
    <div class="modal fade" id="sendSingleModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form id="sendSingleForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Reminder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3" style="font-size:13px">
                            Send payment reminder to <strong id="sendSingleCompany"></strong>
                        </p>
                        <label class="form-label">Channels</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="s-sms"
                                    checked>
                                <label class="form-check-label" for="s-sms">SMS</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="s-email"
                                    checked>
                                <label class="form-check-label" for="s-email">Email</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp"
                                    id="s-wa">
                                <label class="form-check-label" for="s-wa">WhatsApp</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-send me-1"></i> Send
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Send Modal --}}
    <div class="modal fade" id="bulkSendModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('admin.balance-reminders.send-bulk') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Send Reminders</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size:13px" class="mb-3">
                            Send payment reminders to <strong>all {{ $productCount }} products</strong> with outstanding
                            balance.
                            Products reminded within the interval will be skipped.
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Skip if reminded within (days)</label>
                            <input type="number" name="interval" class="form-control" value="7" min="1" max="90">
                        </div>
                        <label class="form-label">Channels</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="b-sms"
                                    checked>
                                <label class="form-check-label" for="b-sms">SMS</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="b-email"
                                    checked>
                                <label class="form-check-label" for="b-email">Email</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp"
                                    id="b-wa">
                                <label class="form-check-label" for="b-wa">WhatsApp</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Send balance reminders to all clients? Products reminded recently will be skipped.')">
                            <i class="bx bx-send me-1"></i> Send All
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('js')
    <script>
        function sendSingle(productId, company) {
            document.getElementById('sendSingleForm').action = '/admin/balance-reminders/send/' + productId;
            document.getElementById('sendSingleCompany').textContent = company;
            new bootstrap.Modal(document.getElementById('sendSingleModal')).show();
        }
    </script>
@endsection