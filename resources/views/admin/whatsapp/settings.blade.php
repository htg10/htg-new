@extends('layouts.backend.app')

@section('meta')
    <title>WhatsApp Settings | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            WhatsApp Integration
                            <span class="htg-page-sub">Meta Cloud API settings, templates & quick-send</span>
                        </h4>
                        <a href="{{ route('admin.whatsapp.logs') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-history me-1"></i> Message Logs
                        </a>
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

            {{-- Stats strip --}}
            <div class="htg-wa-stats">
                <div class="htg-wa-stats__item">
                    <i class="bx bx-message-dots"></i>
                    <div>
                        <span>Total Sent</span>
                        <strong>{{ number_format($logsCount) }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item">
                    <i class="bx bx-send"></i>
                    <div>
                        <span>Today</span>
                        <strong>{{ number_format($sentToday) }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item htg-wa-stats__item--bad">
                    <i class="bx bx-error-circle"></i>
                    <div>
                        <span>Failed</span>
                        <strong>{{ number_format($failedCount) }}</strong>
                    </div>
                </div>
                <div class="htg-wa-stats__item">
                    <i class="bx bx-collection"></i>
                    <div>
                        <span>Templates</span>
                        <strong>{{ $templates->count() }}</strong>
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- API Settings --}}
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span><i class="bx bx-cog me-1"></i> API Configuration</span>
                            @if ($settings)
                                <span class="htg-pill htg-pill--ok" style="font-size:11px">Connected</span>
                            @else
                                <span class="htg-pill htg-pill--bad" style="font-size:11px">Not configured</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.whatsapp.settings.save') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Phone Number ID *</label>
                                    <input type="text" name="phone_number_id" class="form-control"
                                        value="{{ old('phone_number_id', $settings->phone_number_id ?? '') }}"
                                        placeholder="e.g. 113456789012345" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Business Account ID</label>
                                    <input type="text" name="business_account_id" class="form-control"
                                        value="{{ old('business_account_id', $settings->business_account_id ?? '') }}"
                                        placeholder="e.g. 102345678901234">
                                    <small class="text-muted">Required for syncing templates</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Access Token *</label>
                                    <input type="password" name="access_token" class="form-control"
                                        value="{{ old('access_token', $settings->access_token ?? '') }}"
                                        placeholder="Permanent or temporary token" required>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">API Version</label>
                                        <input type="text" name="api_version" class="form-control"
                                            value="{{ old('api_version', $settings->api_version ?? 'v21.0') }}">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Display Phone</label>
                                        <input type="text" name="display_phone" class="form-control"
                                            value="{{ old('display_phone', $settings->display_phone ?? '') }}"
                                            placeholder="+91 98765 43210">
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i> Save Settings
                                    </button>
                                    @if ($settings)
                                        <a href="{{ route('admin.whatsapp.test') }}" class="btn btn-light">
                                            <i class="bx bx-check-shield me-1"></i> Test Connection
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Quick Send --}}
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="bx bx-send me-1"></i> Quick Send Message
                        </div>
                        <div class="card-body">
                            @if ($settings)
                                <form method="POST" action="{{ route('admin.whatsapp.send') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number *</label>
                                        <input type="text" name="phone" class="form-control"
                                            placeholder="e.g. 9876543210" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Recipient Name</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Optional">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Message Type *</label>
                                        <select name="type" id="wa-msg-type" class="form-select" onchange="toggleMsgFields()">
                                            <option value="text">Text Message</option>
                                            <option value="template">Template Message</option>
                                        </select>
                                    </div>
                                    <div id="wa-text-fields">
                                        <div class="mb-3">
                                            <label class="form-label">Message *</label>
                                            <textarea name="message" class="form-control" rows="3" placeholder="Type your message..."></textarea>
                                        </div>
                                    </div>
                                    <div id="wa-tpl-fields" style="display:none">
                                        <div class="mb-3">
                                            <label class="form-label">Template *</label>
                                            <select name="template_name" class="form-select">
                                                <option value="">Select template</option>
                                                @foreach ($templates->where('is_active', true) as $tpl)
                                                    <option value="{{ $tpl->template_name }}">
                                                        {{ $tpl->name }} ({{ $tpl->language }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Parameters</label>
                                            <input type="text" name="template_params" class="form-control"
                                                placeholder="Comma-separated: John, 15 Oct, ₹5000">
                                            <small class="text-muted">Body parameters in order, separated by commas</small>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bx bxl-whatsapp me-1"></i> Send WhatsApp Message
                                    </button>
                                </form>
                            @else
                                <div class="htg-empty">
                                    <i class="bx bxl-whatsapp"></i>
                                    <p>
                                        <strong>Configure API first</strong>
                                        Save your WhatsApp Cloud API credentials to start sending messages.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- Templates --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bx bx-collection me-1"></i> Message Templates</span>
                    @if ($settings && $settings->business_account_id)
                        <a href="{{ route('admin.whatsapp.sync') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-refresh me-1"></i> Sync from Meta
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if ($templates->isEmpty())
                        <div class="htg-empty">
                            <i class="bx bx-collection"></i>
                            <p>
                                <strong>No templates yet</strong>
                                Sync your approved templates from Meta Business Account.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Template Name</th>
                                        <th>Language</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th style="width:60px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($templates as $tpl)
                                        <tr>
                                            <td class="htg-strong">{{ $tpl->name }}</td>
                                            <td><code>{{ $tpl->template_name }}</code></td>
                                            <td>{{ $tpl->language }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $tpl->category }}</span>
                                            </td>
                                            <td>
                                                @if ($tpl->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.whatsapp.template.delete', $tpl->id) }}" class="d-inline"
                                                    onsubmit="return confirm('Remove this template?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-soft-danger btn-sm htg-act" title="Remove">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        function toggleMsgFields() {
            const type = document.getElementById('wa-msg-type').value;
            document.getElementById('wa-text-fields').style.display = type === 'text' ? '' : 'none';
            document.getElementById('wa-tpl-fields').style.display = type === 'template' ? '' : 'none';
        }
    </script>
@endsection
