@php
    $htgRole = match (auth()->user()->role_id ?? 0) {
        1 => 'Admin',
        2 => 'BDM',
        3 => 'Telecaller',
        default => 'User',
    };
@endphp

<!--[ Main Header ] start -->
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex align-items-center">

            <!--[ Logo ] start -->
            <div class="navbar-brand-box">
                <a href="{{ url('/dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt="HTG" height="32">
                    </span>
                    <span class="logo-lg d-flex align-items-center gap-2">
                        <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt="Help Together Group" height="34">
                        <span class="htg-wordmark">
                            <b>Help Together</b>
                            <small>Ledger</small>
                        </span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="htg-menu-btn"
                aria-label="Toggle navigation">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <span class="d-none d-xl-inline-block ms-2 text-muted" style="font-size:12.5px;">
                {{ now()->format('D, d M Y') }}
            </span>

        </div>

        <div class="d-flex align-items-center">

            <button type="button" class="btn header-item noti-icon waves-effect" id="htg-theme-btn"
                title="Switch to dark" aria-label="Switch to dark">
                <i class="bx bx-moon"></i>
            </button>

            <div class="dropdown d-none d-lg-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen"
                    title="Full screen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" title="Notifications">
                    <i class="bx bx-bell"></i>
                    {{-- @if (Auth()->user()->unreadnotifications != null)
                        <span class="badge bg-danger rounded-pill">{{ notification() }}</span>
                    @endif --}}
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="htg-dd-head">
                        <strong key="t-notifications">Notifications</strong>
                        <span>Assignment and renewal alerts land here</span>
                    </div>
                    <div class="htg-empty" style="padding:26px 16px;">
                        <i class="bx bx-bell-off"></i>
                        <p>Nothing new right now.</p>
                    </div>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset('assets/admin/images/avatar.png') }}"
                        alt="">
                    <span class="d-none d-xl-inline-block" key="t-henry">{{ Auth::user()->name }}</span>
                    <span class="htg-role-chip d-none d-xl-inline-block">{{ $htgRole }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="htg-dd-head">
                        <strong>{{ Auth::user()->name }}</strong>
                        <span>{{ Auth::user()->email }}</span>
                    </div>
                    <a class="dropdown-item text-danger" href="{{ url('admin/logout') }}">
                        <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                        <span key="t-logout">Log out</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
