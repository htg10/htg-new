{{--
    Product / service picker for the lead forms.

    Params:
      $selected  array of already-chosen product names (edit form)

    Submits as products[] — exactly what the controller validates.
--}}

@php
    $selected = $selected ?? [];

    $all_products = \App\Models\Service::active()->ordered()->pluck('name')->toArray();
@endphp

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
    <label class="form-label mb-0">Products the lead is interested in</label>
    <span class="text-muted" style="font-size:12px;"><b id="leadProductCount">0</b> selected</span>
</div>

<div class="htg-checkgrid" id="leadProductGrid">
    @foreach ($all_products as $product)
        @php $isOn = in_array($product, $selected); @endphp
        <div class="form-check {{ $isOn ? 'is-on' : '' }}">
            <input class="form-check-input lead-product" type="checkbox" name="products[]"
                value="{{ $product }}" id="product_{{ $loop->index }}" {{ $isOn ? 'checked' : '' }}>
            <label class="form-check-label" for="product_{{ $loop->index }}">
                {{ $product }}
            </label>
        </div>
    @endforeach
</div>
