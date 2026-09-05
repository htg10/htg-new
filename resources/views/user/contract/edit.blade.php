<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Edit</title>
</head>

<body>
    <div class="add m-3">
        <a href="/index" class="btn btn-primary">Back</a>
    </div>
    <div class="row g-1">
        <div class="card col-lg-5 mt-2">
            <div class="form-section mx-3 my-2">
                <form action="{{ url('entry/' . $entries->id) }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation row g-3" novalidate>
                    @method('PATCH')
                    @csrf
                    <div class="col-md-6">
                        <input type="date" name="date" value="{{ $entries->date }}" class="form-control"
                            id="inputEmail4">
                    </div>
                    <div class="col-md-6">
                        <select id="type" name="type" class="form-select">
                            <option value="">New/Renew</option>
                            <option value="new" {{ $entries->type == 'new' ? 'selected' : '' }}>New</option>
                            <option value="renew" {{ $entries->type == 'renew' ? 'selected' : '' }}>Renew</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control" name="company" value="{{ $entries->company }}"
                            placeholder="Company Name">
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control" name="address" value="{{ $entries->address }}"
                            placeholder="Address">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="gst" value="{{ $entries->gst }}"
                            placeholder="GST Number">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="contact" value="{{ $entries->contact }}"
                            placeholder="Contact Person">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="contactno" value="{{ $entries->contactno }}"
                            placeholder="Contact Number">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="email" value="{{ $entries->email }}"
                            placeholder="Contact Email">
                    </div>
                    <div class="col-md-3">
                        <select name="type1" class="form-select">
                            <option value="">Self/Tally</option>
                            <option value="self" {{ $entries->type1 == 'self' ? 'selected' : '' }}>Self</option>
                            <option value="tally" {{ $entries->type1 == 'tally' ? 'selected' : '' }}>Tally</option>
                        </select>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="totalamount" value="{{ $totalAmount }}"
                                placeholder="Total Amount" readonly>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="receivedamount"
                                value="{{ $totalPayment }}" placeholder="Received Amount" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="payment" class="form-select">
                            <option value="">Payment Mode</option>
                            @foreach ($banks as $b)
                                <option value="{{ $b->bank }}"
                                    {{ $entries->payment == $b->bank ? 'selected' : '' }}>
                                    {{ $b->bank }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-3">

                        <select class="form-select form-control form-control-sm" name="user_id" id="user_id">
                            <option value="">Select User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ $user->id == $entries->user_id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="remark"
                                value="{{ $entries->remark }}" placeholder="Remark">
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
                        <div class="col-3">
                            <h5>Validity</h5>
                        </div>
                        <div class="col-3">
                            <h5>Total Amount</h5>
                        </div>
                        <div class="col-3">
                            <h5>Paid Amount</h5>
                        </div>
                        <div class="col-3">
                            <h5>New Amount</h5>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $count = 0;
            $selected_products = array();
            foreach($products as $product_db){
                $selected_products[] = $product_db->product_name;
            }
            foreach($all_products as $product){
                $checkbox_id = $count+1;
                $selected = false;
                $tot_amt = 0;
                $paid_amt = 0;
                $bal_amt = 0;
                $validity = '';
                foreach($products as $product_db){
                    if($product_db->product_name == $product){
                        $validity = $product_db->validity;
                        $selected = true;
                        $tot_amt = $product_db->total_amount;
                        $paid_amt = $product_db->paid_amount;
                        $bal_amt = $product_db->balance_amount;
                        break;
                    }
                }
            ?>
            <div class="row <?= $count > 0 ? 'mt-1' : '' ?>">
                <div class="col-3">
                    <label>
                        <input type="checkbox" id="checkbox<?= $checkbox_id ?>"
                            name="products[{{ $count }}][name]" value="{{ $product }}"
                            class="toggle-fields" <?= $selected ? 'checked' : '' ?>> {{ $product }}
                    </label>
                </div>
                <div id="fields<?= $checkbox_id ?>" class="fields col-9" style="display: none;">
                    <div class="row">
                        <select name="products[{{ $count }}][validity]" class="col-3">
                            <option selected>Select Validity</option>
                            <option {{ $validity == '1 Month' ? 'selected' : '' }}>1 Month</option>
                            <option {{ $validity == '3 Months' ? 'selected' : '' }}>3 Months</option>
                            <option {{ $validity == '4 Months' ? 'selected' : '' }}>4 Months</option>
                            <option {{ $validity == '6 Months' ? 'selected' : '' }}>6 Months</option>
                            <option {{ $validity == '12 Months' ? 'selected' : '' }}>12 Months</option>
                            <option {{ $validity == '24 Months' ? 'selected' : '' }}>24 Months</option>
                            <option {{ $validity == '36 Months' ? 'selected' : '' }}>36 Months</option>
                            <option {{ $validity == 'Lifetime' ? 'selected' : '' }}>Lifetime</option>
                        </select>
                        <input class="col-3" type="text" name="products[{{ $count }}][total_amount]"
                            value="{{ $tot_amt }}" placeholder="total_amount">
                        <input class="col-3" type="text" name="products[{{ $count }}][old_paid_amount]"
                            value="{{ $paid_amt }}" readonly>
                        <input class="col-3" type="text" name="products[{{ $count }}][paid_amount]"
                            value="" placeholder="">
                    </div>
                </div>
            </div>
            <?php
                $count++;
            } ?>

        </div>
        <div class="col-lg-12">
            <div class="card action-btn text-center">
                <div class="card-body p-2">
                    <button type="reset" class="btn btn-warning waves-effect waves-light">Clear Form</button>
                    <button type="submit" class="btn btn-success m-0">Submit Form</button>
                </div>
            </div>
        </div>
        </form>
    </div>


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
            var selected_products_string = '<?= implode(',', $selected_products) ?>';
            var selected_products = selected_products_string.split(",");
            console.log(selected_products);
            $('.toggle-fields').each(function() {
                var checkboxId = this.id.replace('checkbox', '');
                $('#fields' + checkboxId).toggle(this.checked);
            });

        });
    </script>
</body>

</html>
