{{--
    Shared product matrix for every contract form.
    Used by: admin/addnew, admin/renew, admin/addnew-from-lead, admin/edit

    Params:
      $mode      'create' (default) or 'edit'
      $existing  collection of product rows from the DB — edit mode only

    Field names are byte-for-byte what the controller already expects:
      products[i][name] [validity] [total_amount] [paid_amount] [old_paid_amount]
    Element ids checkbox{n} / fields{n} and classes toggle-fields / fields /
    total-amount / paid-amount are unchanged too.
--}}

@php
    $mode = $mode ?? 'create';
    $existing = $existing ?? [];

    $all_products = \App\Models\Service::active()->ordered()->pluck('name')->toArray();

    $validities = ['1 Month', '3 Months', '4 Months', '6 Months', '12 Months', '24 Months', '36 Months', 'Lifetime'];

    // Index the existing rows once instead of scanning the list per product.
    $existingByName = [];
    foreach ($existing as $row) {
        $existingByName[$row->product_name] = $row;
    }

    $selected_products = array_keys($existingByName);
@endphp

<div class="htg-matrix {{ $mode === 'edit' ? 'htg-matrix--edit' : '' }}">

    <div class="htg-matrix__head">
        <div>
            <h5>Products</h5>
            <p>Tick a product to bill it, then set validity and amounts.</p>
        </div>
        <div class="htg-matrix__search">
            <i class="bx bx-search"></i>
            <input type="text" id="productSearch" class="form-control" placeholder="Filter products"
                autocomplete="off">
        </div>
    </div>

    <div class="htg-matrix__cols">
        <span class="htg-matrix__cols-name">Product</span>
        <span>Validity</span>
        <span>Total Amount</span>
        <span>{{ $mode === 'edit' ? 'Already Paid' : 'Paid Amount' }}</span>
        @if ($mode === 'edit')
            <span>New Payment</span>
            <span>Payment Bank</span>
        @endif
    </div>

    <div class="htg-matrix__rows" id="productRows">
        @foreach ($all_products as $count => $product)
            @php
                $checkbox_id = $count + 1;
                $row = $existingByName[$product] ?? null;
                $selected = (bool) $row;
                $validity = $row->validity ?? '';
                $tot_amt = $row->total_amount ?? '';
                $paid_amt = $row->paid_amount ?? '';
            @endphp

            <div class="htg-prod {{ $selected ? 'is-on' : '' }}" data-name="{{ strtolower($product) }}">

                <label class="htg-prod__pick" for="checkbox{{ $checkbox_id }}">
                    <input type="checkbox" id="checkbox{{ $checkbox_id }}" name="products[{{ $count }}][name]"
                        value="{{ $product }}" class="toggle-fields form-check-input"
                        {{ $selected ? 'checked' : '' }}>
                    <span>{{ $product }}</span>
                </label>

                <div id="fields{{ $checkbox_id }}" class="fields htg-prod__fields" style="display: none;">

                    <select name="products[{{ $count }}][validity]" class="form-select form-select-sm">
                        <option value="">Select Validity</option>
                        @foreach ($validities as $v)
                            <option value="{{ $v }}" {{ $validity == $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>

                    @if ($mode === 'edit')
                        <input class="form-control form-control-sm htg-fig" type="text"
                            name="products[{{ $count }}][total_amount]" value="{{ $tot_amt }}"
                            placeholder="Total" inputmode="decimal">

                        <input class="form-control form-control-sm htg-fig" type="text"
                            name="products[{{ $count }}][old_paid_amount]" value="{{ $paid_amt }}"
                            readonly>

                        <input class="form-control form-control-sm htg-fig" type="text"
                            name="products[{{ $count }}][paid_amount]" value="" placeholder="New payment"
                            inputmode="decimal">

                        <select name="products[{{ $count }}][payment_bank]" class="form-select form-select-sm">
                            <option value="">Select Bank</option>
                            @foreach ($banks ?? [] as $b)
                                <option value="{{ $b->bank }}">{{ $b->bank }}</option>
                            @endforeach
                        </select>
                    @else
                        <input class="form-control form-control-sm htg-fig total-amount" type="text"
                            name="products[{{ $count }}][total_amount]" placeholder="Total"
                            inputmode="decimal" oninput="calculateTotals()">

                        <input class="form-control form-control-sm htg-fig paid-amount" type="text"
                            name="products[{{ $count }}][paid_amount]" placeholder="Paid" inputmode="decimal"
                            oninput="calculateTotals()">
                    @endif

                </div>
            </div>
        @endforeach
    </div>

    <div class="htg-matrix__foot">
        <span><b id="productCount">0</b> selected</span>
        <span id="productNoMatch" class="text-muted" hidden>No product matches that filter.</span>
    </div>

</div>
