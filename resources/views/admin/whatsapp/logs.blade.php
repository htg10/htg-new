@extends('layouts.backend.app')

@section('meta')
    <title>WhatsApp Logs | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            WhatsApp Logs
                            <span class="htg-page-sub">All outgoing WhatsApp messages</span>
                        </h4>
                        <a href="{{ route('admin.whatsapp.settings') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-cog me-1"></i> Settings
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="htg-wa-stats mb-3">
                <div class="htg-wa-stats__item htg-wa-stats__item--ok">
                    <i class="bx bx-check-circle"></i>
                    <div>
                        <span>Delivered</span>
                        <strong>{{ number_format($totalSent) }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item htg-wa-stats__item--bad">
                    <i class="bx bx-error-circle"></i>
                    <div>
                        <span>Failed</span>
                        <strong>{{ number_format($totalFailed) }}</strong>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="card htg-filter mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.whatsapp.logs') }}" class="htg-filter-actions">
                        <input type="text" name="search" class="form-control" style="max-width:220px"
                            placeholder="Search phone, name..." value="{{ request('search') }}">
                        <select name="status" class="form-select" style="max-width:140px">
                            <option value="">All Status</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        <input type="date" name="from_date" class="form-control" style="max-width:150px"
                            value="{{ request('from_date') }}" placeholder="From">
                        <input type="date" name="to_date" class="form-control" style="max-width:150px"
                            value="{{ request('to_date') }}" placeholder="To">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'status', 'from_date', 'to_date']))
                            <a href="{{ route('admin.whatsapp.logs') }}" class="btn btn-light btn-sm">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Logs table --}}
            <div class="card">
                <div class="card-body p-0">
                    @if ($logs->isEmpty())
                        <div class="htg-empty">
                            <i class="bx bx-message-dots"></i>
                            <p>
                                <strong>No messages yet</strong>
                                Messages you send via WhatsApp will appear here.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Recipient</th>
                                        <th>Type</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                        <th>Context</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td>
                                                <span class="htg-tenant__name">{{ $log->to_name ?: '—' }}</span>
                                                <span class="htg-tenant__unit">{{ $log->to_phone }}</span>
                                            </td>
                                            <td>
                                                @if ($log->message_type === 'template')
                                                    <span class="badge bg-info">Template</span>
                                                @else
                                                    <span class="badge bg-secondary">Text</span>
                                                @endif
                                            </td>
                                            <td style="max-width:260px">
                                                @if ($log->template_name)
                                                    <code>{{ $log->template_name }}</code>
                                                @else
                                                    <span class="text-muted" style="font-size:12.5px">
                                                        {{ Str::limit($log->content, 80) }}
                                                    </span>
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
                                            <td>
                                                @if ($log->context_type)
                                                    <span class="badge bg-secondary">{{ $log->context_type }}#{{ $log->context_id }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
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
