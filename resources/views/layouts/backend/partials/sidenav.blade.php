@php
    // Marks the current row. Purely presentational — hrefs are untouched.
    $on = fn(...$patterns) => request()->is(...$patterns) ? 'htg-active' : '';

    $htgHome = match (auth()->user()->role_id ?? 0) {
        1 => route('admin.dashboard'),
        2 => route('user.dashboard'),
        default => route('telecaller.dashboard'),
    };
@endphp

<!--[ Left Sidebar Navigation ] start -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!--[ Sidemenu ] start -->
        <div id="sidebar-menu">

            <!--[ Left Menu ] start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="{{ $htgHome }}"
                        class="waves-effect {{ $on('admin/dashboard', 'user/dashboard', 'telecaller/dashboard', 'dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if (auth()->user()->role_id == 1)

                    <span class="htg-nav-label">Contracts</span>

                    <li>
                        <a href="/addnew" class="waves-effect {{ $on('addnew') }}">
                            <i class="bx bx-user-plus"></i>
                            <span key="t-chat">Add New Contract</span>
                        </a>
                    </li>
                    <li>
                        <a href="/renew" class="waves-effect {{ $on('renew') }}">
                            <i class="bx bx-reset"></i>
                            <span key="t-chat">Renew Contract</span>
                        </a>
                    </li>
                    <li>
                        <a href="/index" class="waves-effect {{ $on('index', 'entry/*') }}">
                            <i class="bx bx-list-ul"></i>
                            <span key="t-chat">All Contracts</span>
                        </a>
                    </li>

                    <span class="htg-nav-label">Leads</span>

                    <li>
                        <a href="{{ route('leads') }}" class="waves-effect {{ $on('admin/leads') }}">
                            <i class="bx bx-phone-call"></i>
                            <span>All Assigned Leads</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/lead/create" class="waves-effect {{ $on('admin/lead/create') }}">
                            <i class="bx bx-user-plus"></i>
                            <span key="t-chat">Add New Lead</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/lead/index"
                            class="waves-effect {{ $on('admin/lead/index', 'admin/lead/*/edit') }}">
                            <i class="bx bx-list-ul"></i>
                            <span key="t-chat">All Leads</span>
                        </a>
                    </li>

                    <span class="htg-nav-label">Finance</span>

                    <li>
                        <a href="{{ route('bank.index') }}" class="waves-effect {{ $on('admin/bank*') }}">
                            <i class="fas fa-university"></i>
                            <span>Banks</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('purpose.index') }}" class="waves-effect {{ $on('admin/purpose*') }}">
                            <i class="fas fa-tags"></i>
                            <span>Purpose</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expense.index') }}" class="waves-effect {{ $on('admin/expense*') }}">
                            <i class="fas fa-wallet"></i>
                            <span>Expense</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/rent/index" class="waves-effect {{ $on('admin/rent*') }}">
                            <i class="bx bx-building-house"></i>
                            <span key="t-chat">Building Rent</span>
                        </a>
                    </li>

                    <span class="htg-nav-label">Team</span>

                    <li>
                        <a href="/admin/users" class="waves-effect {{ $on('admin/users') }}">
                            <i class="bx bx-user"></i>
                            <span key="t-chat">All Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/telecaller/addnew" class="waves-effect {{ $on('admin/telecaller*') }}">
                            <i class="bx bx-support"></i>
                            <span key="t-chat">Add New Telecaller</span>
                        </a>
                    </li>

                @elseif (auth()->user()->role_id == 2)

                    <span class="htg-nav-label">Contracts</span>

                    <li>
                        <a href="/user/addnew" class="waves-effect {{ $on('user/addnew') }}">
                            <i class="bx bx-user-plus"></i>
                            <span key="t-chat">Add New Contract</span>
                        </a>
                    </li>
                    <li>
                        <a href="/user/renew" class="waves-effect {{ $on('user/renew') }}">
                            <i class="bx bx-reset"></i>
                            <span key="t-chat">Renew Contract</span>
                        </a>
                    </li>
                    <li>
                        <a href="/user/index" class="waves-effect {{ $on('user/index', 'user/entry/*') }}">
                            <i class="bx bx-list-ul"></i>
                            <span key="t-chat">All Contracts</span>
                        </a>
                    </li>

                    <span class="htg-nav-label">Leads</span>

                    <li>
                        <a href="{{ route('user.leads') }}" class="waves-effect {{ $on('user/leads') }}">
                            <i class="bx bx-phone-call"></i>
                            <span>Assigned Leads</span>
                        </a>
                    </li>
                    <li>
                        <a href="/user/lead/create" class="waves-effect {{ $on('user/lead/create') }}">
                            <i class="bx bx-user-plus"></i>
                            <span key="t-chat">Add New Lead</span>
                        </a>
                    </li>
                    <li>
                        <a href="/user/lead/index"
                            class="waves-effect {{ $on('user/lead/index', 'user/lead/*/edit') }}">
                            <i class="bx bx-list-ul"></i>
                            <span key="t-chat">All Leads</span>
                        </a>
                    </li>

                @elseif (auth()->user()->role_id == 3)

                    <span class="htg-nav-label">Leads</span>

                    <li>
                        <a href="/telecaller/create" class="waves-effect {{ $on('telecaller/create') }}">
                            <i class="bx bx-user-plus"></i>
                            <span key="t-chat">Add New Lead</span>
                        </a>
                    </li>
                    <li>
                        <a href="/telecaller/index"
                            class="waves-effect {{ $on('telecaller/index', 'telecaller/*/edit') }}">
                            <i class="bx bx-list-ul"></i>
                            <span key="t-chat">All Leads</span>
                        </a>
                    </li>

                @endif
            </ul>

            <div class="htg-side-foot">
                <p>Signed in as</p>
                <p><b>{{ Auth::user()->name }}</b></p>
            </div>

        </div>
    </div>
</div>
