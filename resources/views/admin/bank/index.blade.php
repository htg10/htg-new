@extends('layouts.backend.app')

@section('meta')
    <title>Banks | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Banks
                            <span class="htg-page-sub">Account balances and transaction overview</span>
                        </h4>
                        <div class="page-title-right">
                            <a href="{{ route('bank.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Add Bank
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary strip --}}
            @php
                $totalBalance = $banks->sum('current_balance');
                $totalIncome  = $banks->sum('total_income');
                $totalExpense = $banks->sum('total_expense');
            @endphp
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card mini-stats-wid mb-0">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Total Balance</p>
                                    <h4 class="mb-0"><span class="htg-cur">&#8377;</span>{{ number_format($totalBalance, 2) }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title"><i class="bx bx-wallet font-size-24"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid mb-0">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Total Income</p>
                                    <h4 class="mb-0 text-success"><span class="htg-cur">&#8377;</span>{{ number_format($totalIncome, 2) }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle" style="background:var(--htg-ok-soft)">
                                        <span class="avatar-title" style="background:var(--htg-ok)"><i class="bx bx-trending-up font-size-24"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid mb-0">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium mb-1">Total Expense</p>
                                    <h4 class="mb-0 text-danger"><span class="htg-cur">&#8377;</span>{{ number_format($totalExpense, 2) }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle" style="background:var(--htg-bad-soft)">
                                        <span class="avatar-title" style="background:var(--htg-bad)"><i class="bx bx-trending-down font-size-24"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Cards Grid --}}
            <div class="htg-bank-grid">
                @forelse ($banks as $bank)
                    @php
                        $colors = ['#2B59C3','#0F7A52','#B26908','#C33C2E','#6A4FC7','#1D8AA8','#4A5A72','#8E5BB5','#2F8F6E','#A2551C'];
                        $color  = $colors[$loop->index % count($colors)];
                        $initials = collect(explode(' ', $bank->bank))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                    @endphp
                    <div class="htg-bank-card">
                        <div class="htg-bank-card__head">
                            <div class="htg-bank-card__avatar" style="background:{{ $color }}">
                                @if ($bank->attachment)
                                    <img src="{{ asset($bank->attachment) }}" alt="{{ $bank->bank }}">
                                @else
                                    <span>{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="htg-bank-card__info">
                                <h5 class="htg-bank-card__name">{{ $bank->bank }}</h5>
                                <span class="htg-bank-card__label">Current Balance</span>
                                <span class="htg-bank-card__balance {{ $bank->current_balance >= 0 ? 'htg-bank-card__balance--pos' : 'htg-bank-card__balance--neg' }}">
                                    <span class="htg-cur">&#8377;</span>{{ number_format(abs($bank->current_balance), 2) }}
                                </span>
                            </div>
                            <div class="htg-bank-card__actions dropdown">
                                <button class="btn btn-link p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded font-size-18"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('bank.edit', $bank) }}"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('bank.destroy', $bank) }}" onsubmit="return confirm('Delete {{ $bank->bank }}?')">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger"><i class="bx bx-trash me-2"></i>Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="htg-bank-card__stats">
                            <div class="htg-bank-card__stat">
                                <span class="htg-bank-card__stat-label">Opening</span>
                                <span class="htg-bank-card__stat-value">&#8377;{{ number_format($bank->opening_balance, 2) }}</span>
                            </div>
                            <div class="htg-bank-card__stat htg-bank-card__stat--in">
                                <span class="htg-bank-card__stat-label"><i class="bx bx-up-arrow-alt"></i> Income</span>
                                <span class="htg-bank-card__stat-value">&#8377;{{ number_format($bank->total_income, 2) }}</span>
                            </div>
                            <div class="htg-bank-card__stat htg-bank-card__stat--out">
                                <span class="htg-bank-card__stat-label"><i class="bx bx-down-arrow-alt"></i> Expense</span>
                                <span class="htg-bank-card__stat-value">&#8377;{{ number_format($bank->total_expense, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="htg-empty" style="grid-column:1/-1">
                        <i class="bx bx-bank"></i>
                        <p><strong>No banks added yet</strong><br>Add your first bank account to start tracking balances.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
@endsection
