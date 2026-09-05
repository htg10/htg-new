@extends('layouts.backend.app')

@section('meta')
    <title>Dashboard | User</title>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card border">
                        <form method="GET" action="{{ route('user.lead.index') }}" class="row g-2 mt-2 mx-2">
                            <div class="col-md-2">
                                <input type="text" name="business" value="{{ request('business') }}" class="form-control"
                                    placeholder="Search Business Name">
                            </div>

                            {{-- <div class="col-md-2">
                                <select name="bdm_id" class="form-select">
                                    <option value="">All BDMs</option>
                                    @foreach ($users as $bdm)
                                        <option value="{{ $bdm->id }}" {{ request('bdm_id') == $bdm->id ? 'selected' : '' }}>
                                            {{ $bdm->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-2">
                                <select name="deal_status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('deal_status') == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="follow up" {{ request('deal_status') == 'follow up' ? 'selected' : '' }}>
                                        Follow Up</option>
                                    <option value="deal closed" {{ request('deal_status') == 'deal closed' ? 'selected' : '' }}>Deal Closed</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control"
                                    placeholder="From Date">
                            </div>

                            <div class="col-md-2">
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control"
                                    placeholder="To Date">
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('user.lead.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                        <div class="card-body table-responsive">
                            <table id="telecallerTable" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                                <thead>
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Business Name</th>
                                        <th>Customer Name</th>
                                        <th>Address</th>
                                        <th>Meeting Date and Time</th>
                                        <th>Mobile Number</th>
                                        {{-- <th>BDM Name</th> --}}
                                        <th>Interest</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($telecallers as $key => $telecaller)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $telecaller->business }}</td>
                                            <td>{{ $telecaller->name }}</td>
                                            <td>
                                                <span class="comment-short">
                                                    {{ Str::limit($telecaller->address, 30) }}
                                                </span>

                                                @if (strlen($telecaller->address) > 30)
                                                    <span class="comment-full d-none">
                                                        {{ $telecaller->address }}
                                                    </span>

                                                    <a href="javascript:void(0)" class="read-more text-primary fw-semibold"
                                                        onclick="toggleComment(this)">
                                                        Read more
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($telecaller->meeting_datetime)->format('d M Y h:i A') }}
                                            </td>
                                            <td>{{ $telecaller->mobile }}</td>
                                            {{-- <td>{{ $telecaller->user->name }}</td> --}}
                                            <td>{{ $telecaller->interest }}</td>
                                            <td>
                                                @if(!empty($telecaller->location_url))
                                                    <a href="{{ $telecaller->location_url }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        View Location
                                                    </a>
                                                @elseif(!empty($telecaller->latitude) && !empty($telecaller->longitude))
                                                    <a href="https://www.google.com/maps?q={{ $telecaller->latitude }},{{ $telecaller->longitude }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        View Location
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">Not Added</span>
                                                @endif
                                            </td>
                                            <td class="
                                                                                                                                @if ($telecaller->deal_status == 'pending') text-danger
                                                                                                                                @elseif($telecaller->deal_status == 'follow up')
                                                                                                                                    text-warning
                                                                                                                                @elseif($telecaller->deal_status == 'deal closed')
                                                                                                                                text-success @endif
                                                                                                                            ">
                                                {{ ucwords($telecaller->deal_status) }}
                                            </td>
                                            <td>
                                                <a href="{{ route('user.lead.edit', $telecaller->id) }}"
                                                    class="btn btn-soft-info btn-sm waves-effect waves-light"><img
                                                        src="{{ asset('assets/icons/edit.svg') }}" alt=""></a>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-soft-danger btn-sm waves-effect waves-light sa-delete"
                                                    title="Delete Brand" data-id="{{ $telecaller->id }}"
                                                    data-link="/user/lead/delete/"><img
                                                        src="{{ asset('assets/icons/delete.svg') }}" alt=""></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->

            </div> <!-- end row -->

        </div>
    </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('#telecallerTable').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });
        });
    </script>

    {{-- Read More Button --}}
    <script>
        function toggleComment(el) {
            let td = el.closest('td');
            let shortText = td.querySelector('.comment-short');
            let fullText = td.querySelector('.comment-full');

            if (fullText.classList.contains('d-none')) {
                shortText.classList.add('d-none');
                fullText.classList.remove('d-none');
                el.innerText = 'Read less';
            } else {
                fullText.classList.add('d-none');
                shortText.classList.remove('d-none');
                el.innerText = 'Read more';
            }
        }
    </script>
@endsection