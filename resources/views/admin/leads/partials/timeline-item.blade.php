@php
    $typeIcons = [
        'note' => 'bx-note',
        'call' => 'bx-phone-call',
        'meeting' => 'bx-calendar-event',
        'whatsapp' => 'bxl-whatsapp',
        'email' => 'bx-envelope',
        'status_change' => 'bx-transfer',
    ];
    $typeColors = [
        'note' => 'var(--htg-accent)',
        'call' => 'var(--htg-ok)',
        'meeting' => 'var(--htg-warn)',
        'whatsapp' => '#25D366',
        'email' => 'var(--htg-accent)',
        'status_change' => '#6c757d',
    ];
@endphp

<div class="htg-timeline__item" data-note-id="{{ $note->id }}">
    <div class="htg-timeline__dot" style="background:{{ $typeColors[$note->type] ?? 'var(--htg-accent)' }}">
        <i class="bx {{ $typeIcons[$note->type] ?? 'bx-note' }}"></i>
    </div>
    <div class="htg-timeline__card">
        <div class="htg-timeline__head">
            <span class="htg-timeline__type">{{ str_replace('_', ' ', $note->type) }}</span>
            <span class="htg-timeline__meta">{{ $note->user->name ?? '—' }} &middot; {{ $note->created_at->diffForHumans() }}</span>
            @if ($note->type !== 'status_change')
                <button class="htg-timeline__del" onclick="deleteNote({{ $note->id }})" title="Delete"><i class="bx bx-trash"></i></button>
            @endif
        </div>
        <div class="htg-timeline__body">{{ $note->content }}</div>
    </div>
</div>
