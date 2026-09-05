@extends('layouts.backend.app')

@section('meta')
    <title>Expense | Admin</title>
@endsection

@section('content')
    <!--[ Blog Content ] start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <h4>Expenses</h4>
                                <a href="{{ route('expense.create') }}" class="btn btn-primary">
                                    + Add Expense
                                </a>
                            </div>
                            <div class="card shadow-sm">
                                <form method="GET" action="{{ route('expense.index') }}" class="row g-2 mt-2 mx-2">
                                    {{-- <div class="col-md-2">
                                        <input type="text" name="purpose" value="{{ request('purpose') }}"
                                            class="form-control" placeholder="Search Purpose">
                                    </div> --}}
                                    <div class="col-md-2">
                                        <select name="purpose" class="form-select">
                                            <option value="">All Purposes</option>
                                            @foreach ($purposes as $purpose)
                                                <option value="{{ $purpose->name }}" {{ request('payment_mode') == $purpose->name ? 'selected' : '' }}>
                                                    {{ $purpose->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <select name="payment_mode" class="form-select">
                                            <option value="">All Payment Modes</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->bank }}"
                                                    {{ request('payment_mode') == $bank->bank ? 'selected' : '' }}>
                                                    {{ $bank->bank }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                                            class="form-control" placeholder="From Date">
                                    </div>

                                    <div class="col-md-2">
                                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                                            class="form-control" placeholder="To Date">
                                    </div>

                                    <div class="col-md-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('expense.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="alert alert-info m-0">
                                            <strong>Total Expenses:</strong> ₹ {{ number_format($totalAmount, 2) }}
                                        </div>
                                    </div>
                                </form>
                                <div class="card-body table-responsive">
                                    <table class="table table-bordered table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Purpose</th>
                                                <th>Amount</th>
                                                <th>Payment Mode</th>
                                                <th>Date</th>
                                                <th>Attachment</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($expenses as $key => $expense)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $expense->purpose }}</td>
                                                    <td>₹ {{ number_format($expense->amount, 2) }}</td>
                                                    <td>{{ $expense->payment_mode }}</td>
                                                    <td>{{ $expense->date }}</td>
                                                    <td>
                                                        @if ($expense->attachment)
                                                            <a href="{{ asset($expense->attachment) }}" target="_blank">
                                                                View
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('expense.edit', $expense) }}"
                                                            class="btn btn-sm btn-warning">Edit</a>

                                                        <form method="POST"
                                                            action="{{ route('expense.destroy', $expense) }}"
                                                            class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button onclick="return confirm('Delete?')"
                                                                class="btn btn-sm btn-danger">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No Complaints found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- {{ $expenses->links() }} --}}
                        </div>
                    </div>

                </div> <!-- end col -->

            </div>

        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Only apply DataTables if there's at least one data row
            const rowCount = $('.table tbody tr').length;
            const isEmpty = $('.table tbody tr td').first().attr('colspan') == 6;

            if (rowCount > 0 && !isEmpty) {
                $('.table').DataTable({
                    ordering: true,
                    language: {
                        paginate: {
                            previous: "<i class='fas fa-angle-left'></i>",
                            next: "<i class='fas fa-angle-right'></i>"
                        }
                    }
                });
            }
        });
    </script>
@endsection
