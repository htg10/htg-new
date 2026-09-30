@extends('layouts.backend.app')

@section('meta')
    <title>WhatsApp Chat | Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="page-content" style="padding:0; height:calc(100vh - var(--htg-topbar-h, 60px)); overflow:hidden;">
    <div class="htg-chat">

        {{-- Left Panel: Conversations --}}
        <div class="htg-chat__sidebar" id="chatSidebar">
            <div class="htg-chat__sidebar-head">
                <div class="htg-chat__sidebar-title">
                    <i class="bx bxl-whatsapp" style="color:#25D366;font-size:24px"></i>
                    <span>Chats</span>
                </div>
                <button class="btn btn-sm btn-light" onclick="openNewChat()" title="New chat">
                    <i class="bx bx-edit-alt"></i>
                </button>
            </div>

            <div class="htg-chat__search">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" id="chatSearch" placeholder="Search or start new chat" autocomplete="off">
                </div>
            </div>

            <div class="htg-chat__contacts" id="contactsList">
                @foreach($conversations as $conv)
                    <div class="htg-chat__contact" data-phone="{{ $conv['phone'] }}" data-name="{{ $conv['name'] }}" onclick="openChat('{{ $conv['phone'] }}', '{{ addslashes($conv['name']) }}', '{{ addslashes($conv['company'] ?? '') }}')">
                        <div class="htg-chat__avatar">
                            <span>{{ strtoupper(substr($conv['name'], 0, 1)) }}</span>
                        </div>
                        <div class="htg-chat__contact-info">
                            <div class="htg-chat__contact-top">
                                <strong class="htg-chat__contact-name">{{ $conv['name'] }}</strong>
                                <span class="htg-chat__contact-time">{{ $conv['last_time'] }}</span>
                            </div>
                            <div class="htg-chat__contact-bottom">
                                <span class="htg-chat__contact-preview">
                                    @if($conv['last_message_direction'] === 'out')
                                        <i class="bx bx-check-double" style="font-size:16px;vertical-align:-2px;color:var(--htg-accent)"></i>
                                    @endif
                                    {{ $conv['last_message'] ?: 'No messages yet' }}
                                </span>
                                @if($conv['unread'] > 0)
                                    <span class="htg-chat__unread">{{ $conv['unread'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if(count($conversations) === 0)
                    <div class="htg-chat__empty-contacts">
                        <i class="bx bx-chat" style="font-size:40px;color:var(--htg-text-3)"></i>
                        <p>No conversations yet</p>
                        <small>Start a new chat to message a customer</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Panel: Chat Area --}}
        <div class="htg-chat__main" id="chatMain">
            {{-- Empty State --}}
            <div class="htg-chat__welcome" id="chatWelcome">
                <div class="htg-chat__welcome-inner">
                    <i class="bx bxl-whatsapp" style="font-size:80px;color:#25D366;opacity:.6"></i>
                    <h3>HTG WhatsApp</h3>
                    <p>Send and receive messages from your customers.<br>Select a conversation or start a new chat.</p>
                </div>
            </div>

            {{-- Active Chat --}}
            <div class="htg-chat__active" id="chatActive" style="display:none">
                {{-- Chat Header --}}
                <div class="htg-chat__header">
                    <button class="btn btn-link d-md-none me-2" onclick="showSidebar()" style="color:var(--htg-text)">
                        <i class="bx bx-arrow-back" style="font-size:20px"></i>
                    </button>
                    <div class="htg-chat__avatar htg-chat__avatar--sm">
                        <span id="chatAvatarLetter">A</span>
                    </div>
                    <div class="htg-chat__header-info">
                        <strong id="chatHeaderName">Contact Name</strong>
                        <span id="chatHeaderPhone">+91 00000 00000</span>
                    </div>
                    <div class="htg-chat__header-actions">
                        <a id="chatCallBtn" href="#" class="btn btn-sm btn-light" title="Call">
                            <i class="bx bx-phone"></i>
                        </a>
                        <a id="chatWaLink" href="#" target="_blank" class="btn btn-sm btn-light" title="Open in WhatsApp">
                            <i class="bx bxl-whatsapp"></i>
                        </a>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div class="htg-chat__messages" id="chatMessages">
                    <div class="htg-chat__loading" id="chatLoading">
                        <div class="spinner-border spinner-border-sm text-muted"></div>
                        <span>Loading messages...</span>
                    </div>
                </div>

                {{-- Template Picker (hidden by default) --}}
                <div class="htg-chat__tpl-picker" id="tplPicker" style="display:none">
                    <div class="htg-chat__tpl-picker-head">
                        <strong><i class="bx bx-collection me-1"></i>Send Template</strong>
                        <button type="button" class="btn-close btn-close-sm" onclick="closeTplPicker()"></button>
                    </div>
                    <select id="tplSelect" class="form-select form-select-sm mb-2">
                        <option value="">Select template</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->template_name }}">{{ $tpl->name }} ({{ $tpl->language }})</option>
                        @endforeach
                    </select>
                    <input type="text" id="tplParams" class="form-control form-control-sm mb-2" placeholder="Parameters (comma-separated)" style="display:none">
                    <button class="btn btn-success btn-sm w-100" id="tplSendBtn" onclick="sendTemplateMsg()" disabled>
                        <i class="bx bxl-whatsapp me-1"></i> Send Template
                    </button>
                </div>

                {{-- Input Area --}}
                <div class="htg-chat__input-area">
                    <div class="htg-chat__input-wrap">
                        <button class="htg-chat__attach-btn" onclick="toggleTplPicker()" title="Send template">
                            <i class="bx bx-collection"></i>
                        </button>
                        <textarea id="chatInput" rows="1" placeholder="Type a message" maxlength="4096"></textarea>
                        <button class="htg-chat__send-btn" id="chatSendBtn" onclick="sendMessage()" title="Send" disabled>
                            <i class="bx bx-send"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- New Chat Modal --}}
<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-edit-alt me-2"></i>New Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Search client or lead</label>
                    <input type="text" id="newChatSearch" class="form-control" placeholder="Name, business, or phone number" autocomplete="off">
                </div>
                <div id="newChatResults" class="htg-chat__search-results"></div>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Or enter phone number directly</label>
                    <div class="input-group">
                        <span class="input-group-text">+91</span>
                        <input type="text" id="newChatPhone" class="form-control" placeholder="10-digit number" maxlength="10" inputmode="numeric">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Contact name (optional)</label>
                    <input type="text" id="newChatName" class="form-control" placeholder="Customer name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="startNewChat()">
                    <i class="bx bx-message-dots me-1"></i> Start Chat
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let currentPhone = null;
let currentName = '';
let currentCompany = '';
let searchTimeout = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openChat(phone, name, company) {
    currentPhone = phone;
    currentName = name;
    currentCompany = company || '';

    document.getElementById('chatWelcome').style.display = 'none';
    document.getElementById('chatActive').style.display = 'flex';

    document.getElementById('chatAvatarLetter').textContent = name.charAt(0).toUpperCase();
    document.getElementById('chatHeaderName').textContent = name;

    const last10 = phone.length > 10 ? phone.slice(-10) : phone;
    document.getElementById('chatHeaderPhone').textContent = '+' + phone;
    document.getElementById('chatCallBtn').href = 'tel:' + last10;
    document.getElementById('chatWaLink').href = 'https://wa.me/' + phone;

    // Mark active contact
    document.querySelectorAll('.htg-chat__contact').forEach(c => c.classList.remove('is-active'));
    const active = document.querySelector(`.htg-chat__contact[data-phone="${phone}"]`);
    if (active) {
        active.classList.add('is-active');
        const badge = active.querySelector('.htg-chat__unread');
        if (badge) badge.remove();
    }

    // Hide sidebar on mobile
    document.getElementById('chatSidebar').classList.remove('is-open');

    loadMessages(phone);
}

async function loadMessages(phone) {
    const container = document.getElementById('chatMessages');
    const loading = document.getElementById('chatLoading');
    loading.style.display = 'flex';
    container.innerHTML = '';
    container.appendChild(loading);

    try {
        const res = await fetch(`/admin/whatsapp/chat/${phone}/messages`);
        const data = await res.json();

        container.innerHTML = '';
        let lastDate = '';

        data.messages.forEach(msg => {
            if (msg.date !== lastDate) {
                lastDate = msg.date;
                container.innerHTML += `<div class="htg-chat__date-sep"><span>${msg.date}</span></div>`;
            }
            container.innerHTML += renderMessage(msg);
        });

        if (data.messages.length === 0) {
            container.innerHTML = `<div class="htg-chat__no-messages">
                <i class="bx bxl-whatsapp" style="font-size:48px;color:#25D366;opacity:.5"></i>
                <p>No messages yet</p>
                <small style="color:var(--htg-text-3)">Send a template to start the conversation</small>
                <button class="btn btn-success btn-sm mt-2" onclick="toggleTplPicker()"><i class="bx bx-collection me-1"></i> Send Template</button>
            </div>`;
        }

        container.scrollTop = container.scrollHeight;
    } catch (e) {
        container.innerHTML = '<div class="htg-chat__no-messages"><p>Failed to load messages</p></div>';
    }
}

function renderMessage(msg) {
    const isOut = msg.direction === 'out';
    const statusIcon = getStatusIcon(msg.status);
    const bubbleClass = isOut ? 'htg-chat__bubble--out' : 'htg-chat__bubble--in';

    let content = escapeHtml(msg.content || '');
    content = linkify(content);

    return `
        <div class="htg-chat__bubble ${bubbleClass}">
            <div class="htg-chat__bubble-body">
                ${msg.type !== 'text' ? `<span class="htg-chat__media-label"><i class="bx bx-${getMediaIcon(msg.type)}"></i> ${msg.type}</span>` : ''}
                <span class="htg-chat__bubble-text">${content}</span>
                <span class="htg-chat__bubble-meta">
                    ${msg.time}
                    ${isOut ? statusIcon : ''}
                </span>
            </div>
        </div>
    `;
}

function getStatusIcon(status) {
    switch (status) {
        case 'read': return '<i class="bx bx-check-double" style="color:#53bdeb"></i>';
        case 'delivered': return '<i class="bx bx-check-double"></i>';
        case 'sent': return '<i class="bx bx-check"></i>';
        case 'failed': return '<i class="bx bx-error" style="color:var(--htg-bad)"></i>';
        default: return '<i class="bx bx-check"></i>';
    }
}

function getMediaIcon(type) {
    switch (type) {
        case 'image': return 'image';
        case 'document': return 'file';
        case 'audio': return 'microphone';
        case 'video': return 'video';
        default: return 'message-detail';
    }
}

async function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message || !currentPhone) return;

    input.value = '';
    input.style.height = 'auto';
    document.getElementById('chatSendBtn').disabled = true;

    const container = document.getElementById('chatMessages');
    const tempId = 'temp-' + Date.now();
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

    container.innerHTML += `
        <div class="htg-chat__bubble htg-chat__bubble--out" id="${tempId}">
            <div class="htg-chat__bubble-body">
                <span class="htg-chat__bubble-text">${linkify(escapeHtml(message))}</span>
                <span class="htg-chat__bubble-meta">
                    ${timeStr}
                    <i class="bx bx-time-five" style="opacity:.5"></i>
                </span>
            </div>
        </div>
    `;
    container.scrollTop = container.scrollHeight;

    try {
        const res = await fetch('/admin/whatsapp/chat/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ phone: currentPhone, message, contact_name: currentName }),
        });
        const data = await res.json();

        const tempEl = document.getElementById(tempId);
        if (tempEl) {
            if (data.success) {
                tempEl.outerHTML = renderMessage(data.message);
            } else {
                const meta = tempEl.querySelector('.htg-chat__bubble-meta');
                if (meta) meta.innerHTML = `${timeStr} <i class="bx bx-error" style="color:var(--htg-bad)"></i>`;
            }
        }

        updateContactList(currentPhone, message, timeStr);
    } catch (e) {
        const tempEl = document.getElementById(tempId);
        if (tempEl) {
            const meta = tempEl.querySelector('.htg-chat__bubble-meta');
            if (meta) meta.innerHTML = `${timeStr} <i class="bx bx-error" style="color:var(--htg-bad)"></i>`;
        }
    }

    container.scrollTop = container.scrollHeight;
}

function updateContactList(phone, message, time) {
    const contact = document.querySelector(`.htg-chat__contact[data-phone="${phone}"]`);
    if (contact) {
        const preview = contact.querySelector('.htg-chat__contact-preview');
        if (preview) preview.innerHTML = `<i class="bx bx-check-double" style="font-size:16px;vertical-align:-2px;color:var(--htg-accent)"></i> ${escapeHtml(message).substring(0, 50)}`;
        const timeEl = contact.querySelector('.htg-chat__contact-time');
        if (timeEl) timeEl.textContent = time;
        contact.parentNode.prepend(contact);
    }
}

// Auto-resize textarea
document.getElementById('chatInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    document.getElementById('chatSendBtn').disabled = !this.value.trim();
});

// Enter to send
document.getElementById('chatInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (this.value.trim()) sendMessage();
    }
});

// Search contacts
document.getElementById('chatSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.htg-chat__contact').forEach(c => {
        const name = (c.dataset.name || '').toLowerCase();
        const phone = (c.dataset.phone || '').toLowerCase();
        c.style.display = (!q || name.includes(q) || phone.includes(q)) ? '' : 'none';
    });
});

// New chat
function openNewChat() {
    document.getElementById('newChatSearch').value = '';
    document.getElementById('newChatPhone').value = '';
    document.getElementById('newChatName').value = '';
    document.getElementById('newChatResults').innerHTML = '';
    new bootstrap.Modal(document.getElementById('newChatModal')).show();
}

let newChatTimeout;
document.getElementById('newChatSearch').addEventListener('input', function() {
    clearTimeout(newChatTimeout);
    const q = this.value.trim();
    if (q.length < 2) {
        document.getElementById('newChatResults').innerHTML = '';
        return;
    }
    newChatTimeout = setTimeout(async () => {
        const res = await fetch(`/admin/whatsapp/chat/search-contacts?q=${encodeURIComponent(q)}`);
        const contacts = await res.json();
        const container = document.getElementById('newChatResults');
        if (contacts.length === 0) {
            container.innerHTML = '<div class="text-muted text-center py-2" style="font-size:13px">No contacts found</div>';
            return;
        }
        container.innerHTML = contacts.map(c => `
            <div class="htg-chat__search-item" onclick="selectNewChatContact('${c.phone}', '${escapeAttr(c.name)}', '${escapeAttr(c.company || '')}')">
                <div class="htg-chat__avatar htg-chat__avatar--sm">
                    <span>${c.name.charAt(0).toUpperCase()}</span>
                </div>
                <div>
                    <strong>${escapeHtml(c.name)}</strong>
                    ${c.company ? `<span style="font-size:12px;color:var(--htg-text-3)"> · ${escapeHtml(c.company)}</span>` : ''}
                    <div style="font-size:12px;color:var(--htg-text-3)">+${c.phone} · ${c.source}</div>
                </div>
            </div>
        `).join('');
    }, 300);
});

function selectNewChatContact(phone, name, company) {
    bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide();
    openChat(phone, name, company);
}

function startNewChat() {
    let phone = document.getElementById('newChatPhone').value.trim().replace(/\D/g, '');
    const name = document.getElementById('newChatName').value.trim() || ('+'  + (phone.length === 10 ? '91' : '') + phone);

    if (!phone || phone.length < 10) {
        alert('Please enter a valid 10-digit phone number');
        return;
    }
    if (phone.length === 10) phone = '91' + phone;

    bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide();
    openChat(phone, name, '');
}

function showSidebar() {
    document.getElementById('chatSidebar').classList.add('is-open');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeAttr(text) {
    return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

function linkify(text) {
    return text.replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
}

// Template picker
function toggleTplPicker() {
    const picker = document.getElementById('tplPicker');
    picker.style.display = picker.style.display === 'none' ? '' : 'none';
}
function closeTplPicker() {
    document.getElementById('tplPicker').style.display = 'none';
    document.getElementById('tplSelect').value = '';
    document.getElementById('tplParams').value = '';
    document.getElementById('tplParams').style.display = 'none';
    document.getElementById('tplSendBtn').disabled = true;
}

document.getElementById('tplSelect').addEventListener('change', function() {
    const hasVal = !!this.value;
    document.getElementById('tplSendBtn').disabled = !hasVal;
    document.getElementById('tplParams').style.display = hasVal ? '' : 'none';
});

async function sendTemplateMsg() {
    const templateName = document.getElementById('tplSelect').value;
    if (!templateName || !currentPhone) return;
    const params = document.getElementById('tplParams').value.trim();

    closeTplPicker();

    const container = document.getElementById('chatMessages');
    const tempId = 'temp-' + Date.now();
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

    container.innerHTML += `
        <div class="htg-chat__bubble htg-chat__bubble--out" id="${tempId}">
            <div class="htg-chat__bubble-body">
                <span class="htg-chat__media-label"><i class="bx bx-collection"></i> template</span>
                <span class="htg-chat__bubble-text">[Template: ${escapeHtml(templateName)}]</span>
                <span class="htg-chat__bubble-meta">${timeStr} <i class="bx bx-time-five" style="opacity:.5"></i></span>
            </div>
        </div>
    `;
    container.scrollTop = container.scrollHeight;

    try {
        const res = await fetch('/admin/whatsapp/chat/send-template', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                phone: currentPhone,
                template_name: templateName,
                template_params: params || null,
                contact_name: currentName,
            }),
        });
        const data = await res.json();

        const tempEl = document.getElementById(tempId);
        if (tempEl) {
            if (data.success) {
                tempEl.outerHTML = renderMessage(data.message);
            } else {
                const meta = tempEl.querySelector('.htg-chat__bubble-meta');
                if (meta) meta.innerHTML = `${timeStr} <i class="bx bx-error" style="color:var(--htg-bad)"></i>`;
            }
        }
        updateContactList(currentPhone, '[Template: ' + templateName + ']', timeStr);
    } catch (e) {
        const tempEl = document.getElementById(tempId);
        if (tempEl) {
            const meta = tempEl.querySelector('.htg-chat__bubble-meta');
            if (meta) meta.innerHTML = `${timeStr} <i class="bx bx-error" style="color:var(--htg-bad)"></i>`;
        }
    }
    container.scrollTop = container.scrollHeight;
}

// Auto-open chat from URL parameter
@if($openPhone)
    document.addEventListener('DOMContentLoaded', function() {
        openChat('{{ $openPhone }}', '{{ addslashes($openName ?? "") }}', '');
    });
@endif
</script>
@endsection
