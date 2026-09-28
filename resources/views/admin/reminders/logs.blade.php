@extends('layouts.backend.app')

@section('meta')
    <title>Reminder Logs | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Reminder Logs
                            <span class="htg-page-sub">History of all automated reminders sent</span>
                        </h4>
                        <a href="{{ route('admin.reminders.index') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back to Reminders
                        </a>
                    </div>
                </div>
            </div>

            {{-- Channel stats --}}
            <div class="htg-wa-stats mb-3">
                @foreach (['sms' => ['bx-message-dots', 'SMS'], 'email' => ['bx-envelope', 'Email'], 'whatsapp' => ['bxl-whatsapp', 'WhatsApp']] as $ch => [$icon, $label])
                    @php $s = $channelStats[$ch] ?? null; @endphp
                    <div class="htg-wa-stats__item">
                        <i class="bx {{ $icon }}"></i>
                        <div>
                            <span>{{ $label }}</span>
                            <strong>{{ $s ? number_format($s->sent) : 0 }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Filters --}}
            <div class="card htg-filter mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reminders.logs') }}" class="htg-filter-actions">
                        <input type="text" name="search" class="form-control" style="max-width:220px"
                            placeholder="Search client, phone..." value="{{ request('search') }}">
                        <select name="channel" class="form-select" style="max-width:140px">
                            <option value="">All Channels</option>
                            <option value="sms" {{ request('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="email" {{ request('channel') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="whatsapp" {{ request('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        <select name="status" class="form-select" style="max-width:130px">
                            <option value="">All Status</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'channel', 'status']))
                            <a href="{{ route('admin.reminders.logs') }}" class="btn btn-light btn-sm">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Logs table --}}
            <div class="card">
                <div class="card-body p-0">
                    @if ($logs->isEmpty())
                        <div class="htg-empty">
                            <i class="bx bx-bell-off"></i>
                            <p><strong>No logs yet</strong> Reminders that are sent will be logged here.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Client</th>
                                        <th>Product</th>
                                        <th>Rule</th>
                                        <th>Channel</th>
                                        <th>Status</th>
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
                                                @if ($log->rule)
                                                    <span class="badge bg-info">{{ $log->rule->name }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
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
                                                    <span class="htg-pill htg-pill--ok" style="font-size:11px">Sent</span>
                                                @else
                                                    <span class="htg-pill htg-pill--bad" style="font-size:11px"
                                                        title="{{ $log->error }}">Failed</span>
                                                @endif
                                            </td>
                                            <td style="white-space:nowrap">
                                                <span class="htg-when">
                                                    <strong>{{ $log->created_at->format('d M Y') }}</strong>
                                                    <span>{{ $log->created_at->format('h:i A') }}</span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($logs->hasPages())
                            <div class="d-flex justify-content-center py-3">
                                {{ $logs->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
