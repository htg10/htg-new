@extends('layouts.backend.app')

@section('meta')
    <title>Balance Reminder Logs | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Balance Reminder Logs
                            <span class="htg-page-sub">History of all balance payment reminders sent</span>
                        </h4>
                        <a href="{{ route('admin.balance-reminders.index') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- Channel Stats --}}
            <div class="htg-wa-stats" style="grid-template-columns: repeat(3, 1fr);">
                @foreach (['sms' => ['bx-message-dots', 'SMS'], 'email' => ['bx-envelope', 'Email'], 'whatsapp' => ['bxl-whatsapp', 'WhatsApp']] as $ch => [$icon, $label])
                    <div class="htg-wa-stats__item">
                        <i class="bx {{ $icon }}"></i>
                        <div>
                            <span>{{ $label }} Sent</span>
                            <strong>{{ $channelStats[$ch]->sent ?? 0 }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Filter --}}
            <div class="card htg-filter">
                <div class="card-body">
                    <form method="GET" class="htg-filter-actions">
                        <input type="text" name="search" class="form-control form-control-sm" style="width:220px"
                            placeholder="Search client, phone…" value="{{ request('search') }}">
                        <select name="channel" class="form-select form-select-sm" style="width:130px" onchange="this.form.submit()">
                            <option value="">All Channels</option>
                            <option value="sms" {{ request('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="email" {{ request('channel') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="whatsapp" {{ request('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        <select name="status" class="form-select form-select-sm" style="width:120px" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-light"><i class="bx bx-search"></i></button>
                        @if (request()->hasAny(['search', 'channel', 'status']))
                            <a href="{{ route('admin.balance-reminders.logs') }}" class="btn btn-sm btn-light"><i class="bx bx-x"></i></a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Logs Table --}}
            <div class="card">
                <div class="card-body p-0">
                    @if ($logs->isEmpty())
                        <div class="htg-empty">
                            <i class="bx bx-history"></i>
                            <p><strong>No logs yet</strong> Balance reminders will appear here once sent.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Client</th>
                                        <th>Product</th>
                                        <th>Balance</th>
                                        <th>Channel</th>
                                        <th>Status</th>
                                        <th>Sent By</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td>
                                                <span class="htg-tenant__name">{{ $log->entry->company ?? '—' }}</span>
                                                <span class="htg-tenant__unit">{{ $log->entry->contact ?? '' }}</span>
                                            </td>
                                            <td>{{ $log->product->product_name ?? '—' }}</td>
                                            <td>
                                                @if ($log->product)
                                                    <span class="htg-cur">₹</span>{{ number_format($log->product->balance_amount, 0) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->channel === 'sms')
                                                    <span class="htg-rule__ch htg-rule__ch--sms"><i class="bx bx-message-dots"></i></span>
                                                @elseif ($log->channel === 'email')
                                                    <span class="htg-rule__ch htg-rule__ch--email"><i class="bx bx-envelope"></i></span>
                                                @else
                                                    <span class="htg-rule__ch htg-rule__ch--whatsapp"><i class="bx bxl-whatsapp"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->status === 'sent')
                                                    <span class="htg-pill htg-pill--ok">Sent</span>
                                                @else
                                                    <span class="htg-pill htg-pill--bad" title="{{ $log->error }}">Failed</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $log->sender->name ?? 'System' }}
                                            </td>
                                            <td style="white-space:nowrap; font-size:12.5px">
                                                {{ $log->created_at->format('d M Y, h:i A') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3">
                            {{ $logs->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
