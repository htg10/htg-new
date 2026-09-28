@extends('layouts.backend.app')

@section('meta')
    <title>Lead Analytics | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Lead Analytics
                            <span class="htg-page-sub">Pipeline health, team performance, and activity feed</span>
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.lead.kanban') }}" class="btn btn-light btn-sm">
                                <i class="bx bx-columns me-1"></i>Kanban
                            </a>
                            <a href="{{ route('admin.lead.index') }}" class="btn btn-light btn-sm">
                                <i class="bx bx-list-ul me-1"></i>All Leads
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI cards --}}
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid htg-kpi">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Total Leads</p>
                                    <h4 class="mb-0">{{ $total }}</h4>
                                    <small class="text-muted">{{ $thisMonthLeads }} this month</small>
                                </div>
                                <div class="htg-kpi__icon htg-kpi__icon--accent">
                                    <i class="bx bx-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid htg-kpi">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Conversion Rate</p>
                                    <h4 class="mb-0">{{ $conversionRate }}%</h4>
                                    <small class="text-muted">{{ $closed }} deals closed</small>
                                </div>
                                <div class="htg-kpi__icon htg-kpi__icon--ok">
                                    <i class="bx bx-trending-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid htg-kpi">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Overdue Follow-ups</p>
                                    <h4 class="mb-0 {{ $overdue > 0 ? 'text-danger' : '' }}">{{ $overdue }}</h4>
                                    <small class="text-muted">{{ $followUp }} in pipeline</small>
                                </div>
                                <div class="htg-kpi__icon htg-kpi__icon--bad">
                                    <i class="bx bx-error-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid htg-kpi">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Meetings</p>
                                    <h4 class="mb-0">{{ $meetingsToday }}</h4>
                                    <small class="text-muted">{{ $meetingsWeek }} this week</small>
                                </div>
                                <div class="htg-kpi__icon htg-kpi__icon--warn">
                                    <i class="bx bx-calendar-event"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Pipeline breakdown --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Pipeline Breakdown</h6>
                            <div class="htg-pipeline-bars">
                                @php
                                    $bars = [
                                        ['label' => 'Pending', 'count' => $pending, 'color' => 'var(--htg-warn)'],
                                        ['label' => 'Follow Up', 'count' => $followUp, 'color' => 'var(--htg-accent)'],
                                        ['label' => 'Deal Closed', 'count' => $closed, 'color' => 'var(--htg-ok)'],
                                        ['label' => 'Not Interested', 'count' => $lost, 'color' => 'var(--htg-bad)'],
                                    ];
                                    $maxBar = max($pending, $followUp, $closed, $lost, 1);
                                @endphp
                                @foreach ($bars as $bar)
                                    <div class="htg-bar-row">
                                        <div class="htg-bar-row__label">
                                            <span>{{ $bar['label'] }}</span>
                                            <strong>{{ $bar['count'] }}</strong>
                                        </div>
                                        <div class="htg-bar-row__track">
                                            <div class="htg-bar-row__fill" style="width:{{ $total > 0 ? round(($bar['count'] / $maxBar) * 100) : 0 }}%; background:{{ $bar['color'] }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Interest distribution --}}
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Interest Distribution</h6>
                            <div class="htg-pipeline-bars">
                                @php
                                    $interestColors = ['High' => 'var(--htg-ok)', 'Moderate' => 'var(--htg-warn)', 'Low' => 'var(--htg-bad)', 'Unknown' => '#6c757d'];
                                    $maxInterest = max($byInterest->max() ?? 1, 1);
                                @endphp
                                @foreach ($byInterest as $level => $count)
                                    <div class="htg-bar-row">
                                        <div class="htg-bar-row__label">
                                            <span>{{ $level }}</span>
                                            <strong>{{ $count }}</strong>
                                        </div>
                                        <div class="htg-bar-row__track">
                                            <div class="htg-bar-row__fill" style="width:{{ round(($count / $maxInterest) * 100) }}%; background:{{ $interestColors[$level] ?? '#6c757d' }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Team performance --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Team Performance</h6>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>BDM</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Closed</th>
                                            <th class="text-center">Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($byBdm as $name => $stats)
                                            @php $rate = $stats['total'] > 0 ? round(($stats['closed'] / $stats['total']) * 100) : 0; @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $name }}</strong>
                                                    <div style="font-size:11px;color:var(--htg-text-3)">
                                                        {{ $stats['pending'] }} pending, {{ $stats['follow_up'] }} follow-up
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $stats['total'] }}</td>
                                                <td class="text-center">
                                                    <span class="text-success fw-semibold">{{ $stats['closed'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $rate >= 30 ? 'bg-success' : ($rate >= 15 ? 'bg-warning' : 'bg-secondary') }}">{{ $rate }}%</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent activity --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Recent Activity</h6>
                            @if ($recentNotes->count())
                                <div class="htg-activity-feed">
                                    @foreach ($recentNotes as $note)
                                        @php
                                            $typeIcons = ['note' => 'bx-note', 'call' => 'bx-phone-call', 'meeting' => 'bx-calendar-event', 'whatsapp' => 'bxl-whatsapp', 'email' => 'bx-envelope', 'status_change' => 'bx-transfer'];
                                        @endphp
                                        <div class="htg-activity-feed__item">
                                            <div class="htg-activity-feed__icon">
                                                <i class="bx {{ $typeIcons[$note->type] ?? 'bx-note' }}"></i>
                                            </div>
                                            <div class="htg-activity-feed__body">
                                                <div>
                                                    <strong>{{ $note->user->name ?? '—' }}</strong>
                                                    <span class="text-muted">logged a {{ str_replace('_', ' ', $note->type) }}</span>
                                                    @if ($note->lead)
                                                        <span>on <a href="{{ route('admin.lead.detail', $note->telecaller_id) }}">{{ $note->lead->business ?: $note->lead->name }}</a></span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="htg-empty">
                                    <i class="bx bx-history"></i>
                                    <p><strong>No activity yet</strong></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
