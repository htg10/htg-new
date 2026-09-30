@extends('layouts.backend.app')

@section('meta')
    <title>Pipeline Board | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Pipeline Board
                            <span class="htg-page-sub">Drag leads between stages to update their status</span>
                        </h4>
                        <div class="d-flex gap-2 align-items-center">
                            <select id="bdmFilter" class="form-select form-select-sm" style="width:180px" onchange="filterByBdm(this.value)">
                                <option value="">All BDMs</option>
                                @foreach ($users as $bdm)
                                    <option value="{{ $bdm->id }}" {{ request('bdm_id') == $bdm->id ? 'selected' : '' }}>{{ $bdm->name }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('admin.lead.index') }}" class="btn btn-light btn-sm">
                                <i class="bx bx-list-ul me-1"></i>List View
                            </a>
                            <a href="/admin/lead/create" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Add Lead
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="htg-kanban" id="kanbanBoard">
                @foreach ($columns as $status => $meta)
                    <div class="htg-kanban__col">
                        <div class="htg-kanban__header htg-kanban__header--{{ $meta['color'] }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx {{ $meta['icon'] }}"></i>
                                <span>{{ $meta['label'] }}</span>
                            </div>
                            <span class="htg-kanban__count">{{ $grouped[$status]->count() }}</span>
                        </div>

                        <div class="htg-kanban__cards" data-status="{{ $status }}"
                             ondrop="kanbanDrop(event)" ondragover="kanbanDragOver(event)" ondragleave="kanbanDragLeave(event)">
                            @foreach ($grouped[$status] as $lead)
                                @php
                                    $mobileDigits = preg_replace('/\D+/', '', $lead->mobile ?? '');
                                    $waNumber = strlen($mobileDigits) === 10 ? '91' . $mobileDigits : $mobileDigits;
                                    $interest = strtolower(trim($lead->interest ?? ''));
                                    $interestClass = match ($interest) {
                                        'high' => 'htg-interest--high',
                                        'moderate' => 'htg-interest--mod',
                                        default => 'htg-interest--low',
                                    };
                                @endphp

                                <div class="htg-kanban__card" draggable="true" data-lead-id="{{ $lead->id }}"
                                     ondragstart="kanbanDragStart(event)" ondragend="kanbanDragEnd(event)">
                                    <a href="{{ route('admin.lead.detail', $lead->id) }}" class="htg-kanban__card-link">
                                        <strong>{{ $lead->business ?: '—' }}</strong>
                                        <span class="htg-kanban__name">{{ $lead->name }}</span>
                                    </a>

                                    <div class="htg-kanban__card-meta">
                                        @if ($mobileDigits)
                                            <a href="tel:{{ $mobileDigits }}" class="htg-kanban__phone" title="Call">
                                                <i class="bx bx-phone"></i> {{ $lead->mobile }}
                                            </a>
                                        @endif
                                        <span class="htg-interest {{ $interestClass }}" style="font-size:11px">
                                            <span class="htg-interest__bars"><i></i><i></i><i></i></span>
                                            {{ $lead->interest ?: '—' }}
                                        </span>
                                    </div>

                                    @if ($lead->meeting_datetime)
                                        <div class="htg-kanban__meeting">
                                            <i class="bx bx-calendar-event"></i>
                                            {{ $lead->meeting_datetime->format('d M, h:i A') }}
                                        </div>
                                    @endif

                                    <div class="htg-kanban__card-foot">
                                        <span class="text-muted" style="font-size:11px">{{ $lead->user->name ?? '—' }}</span>
                                        <div class="htg-kanban__card-actions">
                                            @if ($mobileDigits)
                                                <a href="{{ route('admin.whatsapp.chat') }}?phone={{ $waNumber }}&name={{ urlencode($lead->name ?? $lead->business ?? '') }}" title="WhatsApp" class="htg-kanban__action is-wa"><i class="bx bxl-whatsapp"></i></a>
                                            @endif
                                            <a href="{{ route('admin.lead.detail', $lead->id) }}" title="View" class="htg-kanban__action"><i class="bx bx-show"></i></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection

@section('script')
<script>
const CSRF = '{{ csrf_token() }}';
let draggedCard = null;

function kanbanDragStart(e) {
    draggedCard = e.target.closest('.htg-kanban__card');
    draggedCard.classList.add('is-dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', draggedCard.dataset.leadId);
}

function kanbanDragEnd(e) {
    if (draggedCard) draggedCard.classList.remove('is-dragging');
    document.querySelectorAll('.htg-kanban__cards').forEach(c => c.classList.remove('is-over'));
    draggedCard = null;
}

function kanbanDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    e.currentTarget.classList.add('is-over');
}

function kanbanDragLeave(e) {
    e.currentTarget.classList.remove('is-over');
}

async function kanbanDrop(e) {
    e.preventDefault();
    const col = e.currentTarget;
    col.classList.remove('is-over');

    if (!draggedCard) return;

    const leadId = draggedCard.dataset.leadId;
    const newStatus = col.dataset.status;

    col.appendChild(draggedCard);
    updateColumnCounts();

    await fetch('/admin/lead/kanban-update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ lead_id: leadId, status: newStatus })
    });
}

function updateColumnCounts() {
    document.querySelectorAll('.htg-kanban__col').forEach(col => {
        const count = col.querySelectorAll('.htg-kanban__card').length;
        col.querySelector('.htg-kanban__count').textContent = count;
    });
}

function filterByBdm(bdmId) {
    const url = new URL(window.location);
    if (bdmId) {
        url.searchParams.set('bdm_id', bdmId);
    } else {
        url.searchParams.delete('bdm_id');
    }
    window.location = url;
}
</script>
@endsection
