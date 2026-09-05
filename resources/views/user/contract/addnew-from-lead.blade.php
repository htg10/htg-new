@extends('layouts.backend.app')

@section('meta')
    <title>Add New | User</title>
@endsection
@section('content')
    <!--[ Page Content ] start -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Add New Service</h4>

                        <div class="page-title-right">
                            <a href="{{ url('user/index') }}" class="btn btn-primary waves-effect waves-light"><i
                                    class="fas fa-reply-all"></i> Back to list</a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <div class="row g-1">
                <div class="card col-lg-5 mt-2">
                    <div class="form-section mx-3 my-2">
                        <form action="{{ route('user.addnew.store') }}" method="POST" enctype="multipart/form-data"
                            class="needs-validation row g-3" novalidate>
                            @csrf
                            {{-- @if (isset($lead))
                                <div class="alert alert-info mb-3">
                                    <strong>Lead Interest:</strong> {{ $lead->interest }} <br>
                                    <strong>Meeting:</strong> {{ $lead->meeting_datetime }}
                                </div>
                            @endif --}}
                            <input type="hidden" name="telecaller_id" value="{{ $lead->id }}">
                            <div class="col-md-6">
                                <input type="date" name="date" class="form-control" id="inputEmail4">
                            </div>
                            <div class="col-md-6">
                                <select id="type" name="type" class="form-select">
                                    {{-- <option value="">New/Renew</option> --}}
                                    <option value="new" selected>New</option>
                                    {{-- <option value="renew">Renew</option> --}}
                                </select>
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" name="company" value="{{ $lead->name }}"
                                    placeholder="Company Name">
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" name="address" value="{{ $lead->address }}"
                                    placeholder="Address">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="gst" placeholder="GST Number">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="contact" value="{{ $lead->name }}"
                                    placeholder="Contact Person">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="contactno" value="{{ $lead->mobile }}"
                                    placeholder="Contact Number">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="email" placeholder="Contact Email"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <select name="type1" class="form-select">
                                    <option value="">Self/Tally</option>
                                    <option value="self">Self</option>
                                    <option value="tally">Tally</option>
                                </select>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="totalAmount" name="totalamount"
                                        placeholder="Total Amount" readonly>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="receivedAmount" name="receivedamount"
                                        placeholder="Received Amount" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="payment" id="payment" class="form-select">
                                    <option value="">Payment Mode</option>
                                    @foreach ($banks as $b)
                                    <option value="{{ $b->bank }}">{{$b->bank}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select form-control form-control-sm" name="user_id" id="user_id"
                                    required>
                                    <option value="{{ auth()->id() }}" selected>
                                        {{ auth()->user()->name }}
                                    </option>
                                </select>
                                <div class="invalid-feedback">This field is required. </div>
                            </div>
                            <div class="col-12">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="remark" placeholder="Remark">
                                </div>
                            </div>
                            <div class="image col-md-6">
                                <input type="file" name="image[]" class="form-control" multiple>
                            </div>
                    </div>
                </div>
                <?php
                $all_products = ['Local Keyword SEO', 'Virtual Tour', 'Google Business Profile Management', 'Zonal Keyword SEO', 'Google Ads', 'Google Ads Recharge', 'Meta Ads Management', 'Facebook Ads Recharge', 'Social Media Management', 'Website Design', 'Custom Development', 'Website Amc', 'Product Photography', 'Domain', 'Hosting', 'QR Code', 'Web SEO', 'Others'];
                ?>
                <div class="card col-lg-6 ms-2 mt-2 p-3">
                    <div class="row">
                        <div class="col-3">
                            <h5>Product</h5>
                        </div>
                        <div class="col-9">
                            <div class="row">
                                <div class="col-4">
                                    <h5>Validity</h5>
                                </div>
                                <div class="col-4">
                                    <h5>Total Amount</h5>
                                </div>
                                <div class="col-4">
                                    <h5>Paid Amount</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
            $count = 0;
            $selected_products = array();
            foreach($all_products as $product){
                $checkbox_id = $count+1;
                $validity = '';
            ?>
                    <div class="row <?= $count > 0 ? 'mt-1' : '' ?>">
                        <div class="col-3">
                            <label>
                                <input type="checkbox" id="checkbox<?= $checkbox_id ?>"
                                    name="products[{{ $count }}][name]" value="{{ $product }}"
                                    class="toggle-fields">
                                {{ $product }}
                            </label>
                        </div>
                        <div id="fields<?= $checkbox_id ?>" class="fields col-9" style="display: none;">
                            <div class="row gap-4">
                                <select name="products[{{ $count }}][validity]" class="col-3">
                                    <option value="">Select Validity</option>
                                    <option value="1 Month">1 Month</option>
                                    <option value="3 Months">3 Months</option>
                                    <option value="4 Months">4 Months</option>
                                    <option value="6 Months">6 Months</option>
                                    <option value="12 Months">12 Months</option>
                                    <option value="24 Months">24 Months</option>
                                    <option value="36 Months">36 Months</option>
                                    <option value="Lifetime">Lifetime</option>
                                </select>
                                <input class="col-3 total-amount" type="text"
                                    name="products[{{ $count }}][total_amount]" placeholder="Total Amount"
                                    oninput="calculateTotals()">
                                <input class="col-3 paid-amount" type="text"
                                    name="products[{{ $count }}][paid_amount]" placeholder="Paid Amount"
                                    oninput="calculateTotals()">
                            </div>
                        </div>
                    </div>
                    <?php
                $count++;
            } ?>

                    <div class="col-lg-12 mt-3">
                        <div class="card action-btn text-center">
                            <div class="card-body p-2">
                                <button type="submit" class="btn btn-success m-0">Submit Form</button>
                                <button type="reset" class="btn btn-warning waves-effect waves-light">Clear
                                    Form</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.toggle-fields').change(function() {
                var checkboxId = this.id.replace('checkbox', '');
                $('#fields' + checkboxId).toggle(this.checked);
            });
        });
    </script>

    <script>
        function calculateTotals() {
            let totalAmount = 0;
            let receivedAmount = 0;

            // Sum all total_amount fields
            document.querySelectorAll('.total-amount').forEach(input => {
                const value = parseFloat(input.value) || 0;
                totalAmount += value;
            });

            // Sum all paid_amount fields
            document.querySelectorAll('.paid-amount').forEach(input => {
                const value = parseFloat(input.value) || 0;
                receivedAmount += value;
            });

            // Update the total and received amount fields
            document.getElementById('totalAmount').value = totalAmount.toFixed(2);
            document.getElementById('receivedAmount').value = receivedAmount.toFixed(2);
        }
    </script>
@endsection
