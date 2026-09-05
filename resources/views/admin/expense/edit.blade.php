@extends('layouts.backend.app')

@section('meta')
    <title>Expense | Admin</title>
@endsection

@section('style')
@endsection
@section('content')
    <!--[ Blog Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">
                    <h4>Edit Expense</h4>

                    <form method="POST" enctype="multipart/form-data" action="{{ route('expense.update', $expense) }}">
                        @csrf @method('POST')

                        <div class="mb-2 col-lg-6">
                            <label>Title *</label>

                            <select name="purpose" id="purpose" class="form-select" required>

                                <option value="">Select Purpose</option>

                                @foreach($purposes as $purpose)

                                    <option value="{{ $purpose->name }}" {{ old('purpose', $expense->purpose) == $purpose->name ? 'selected' : '' }}>

                                        {{ $purpose->name }}

                                    </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Amount *</label>
                            <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}"
                                class="form-control">
                        </div>
                        <div class="mb-2 col-lg-6">
                            <label>Payment Mode *</label>
                            <select name="payment_mode" id="payment_mode" class="form-select" required>
                                <option value="">Payment Mode</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->bank }}" {{ $expense->payment_mode == $b->bank ? 'selected' : '' }}>
                                        {{ $b->bank }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Date *</label>
                            <input type="date" name="date" value="{{ $expense->date }}" class="form-control">
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control">{{ $expense->remark }}</textarea>
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Attachment</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <button class="btn btn-primary">Update Expense</button>
                    </form>
                </div>
            </div>



        </div>
        <!-- container-fluid -->
    </div>
@endsection

@section('script')
    <script>
        $(window).on('load', function () {
            ClassicEditor.create(document.querySelector('#description'))
                .catch(error => {
                    console.error(error);
                });
        });

        $(document).ready(function () {
            $('.dropify').dropify();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const dobInput = document.getElementById("age");
            const ageInput = document.getElementById("dob");

            function calculateAge(dobValue) {
                const dob = new Date(dobValue);
                const today = new Date();

                let age = today.getFullYear() - dob.getFullYear();
                const m = today.getMonth() - dob.getMonth();

                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                return age;
            }

            // 🔥 AUTO-FILL AGE WHEN EDIT PAGE LOADS
            if (dobInput.value) {
                ageInput.value = calculateAge(dobInput.value);
            }

            // Optional: DOB change → Age update
            dobInput.addEventListener("change", function () {
                ageInput.value = calculateAge(this.value);
            });
        });
    </script>
@endsection