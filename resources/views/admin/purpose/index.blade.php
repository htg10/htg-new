@extends('layouts.backend.app')

@section('meta')
    <title>Purpose | Admin</title>
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
                                <h4>Purposes</h4>
                                <a href="{{ route('purpose.create') }}" class="btn btn-primary">
                                    + Add Purpose
                                </a>
                            </div>
                            <div class="card shadow-sm">
                                <div class="card-body table-responsive">
                                    <table class="table table-bordered table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Purpose Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($purposes as $key => $purpose)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $purpose->name }}</td>

                                                    <td>
                                                        <a href="{{ route('purpose.edit', $purpose) }}"
                                                            class="btn btn-sm btn-warning">Edit</a>

                                                        <form method="POST" action="{{ route('purpose.destroy', $purpose) }}"
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
                                                    <td colspan="4" class="text-center text-muted">No Complaints found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- {{ $purposes->links() }} --}}
                        </div>
                    </div>

                </div> <!-- end col -->

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            // Only apply DataTables if there's at least one data row
            const rowCount = $('.table tbody tr').length;
            const isEmpty = $('.table tbody tr td').first().attr('colspan') == 4;

            if (rowCount > 0 && !isEmpty) {
                $('.table').DataTable({
                    ordering: false,
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