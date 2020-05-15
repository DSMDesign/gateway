{!!Form::open(['route' => 'stripe', 'method' => 'post' ,'id' => 'stripePay'])!!}
    {{-- products for the cart --}}
    @foreach ($cart['products'] as $item)
        {!! Form::hidden('line_items.name[]', $item['product']) !!} {{-- product name --}}
        {!! Form::hidden('line_items.description[]', $item['slug']) !!} {{-- item description--}}
        {!! Form::hidden('line_items.images[]', env('IMAGE_URL').$item['image']) !!} {{-- product image --}}
        {!! Form::hidden('line_items.amount[]', encrypt($item['exc_vat'])) !!} {{-- the price of the item you seling to the user --}}
        {!! Form::hidden('line_items.quantity[]', encrypt($item['cart_qty'])) !!} {{-- how many items the user us buying --}}
    @endforeach

    {{-- geting the customer info if is not empty --}}
    @if (!empty($cart['customerInfo']))
        {!! Form::hidden('use_customer', encrypt(true)) !!}
        {!! Form::hidden('customer_email', encrypt($cart['customerInfo']['email'])) !!}
    @endif

    @php
        $postageAmount = round($cart['postage_exc_vat'] * 100);
    @endphp
    {{-- the cart vat --}}
    {!! Form::hidden('postage.name[]', "POSTAGE") !!} {{-- product name --}}
    {!! Form::hidden('postage.description[]', "POSTAGE") !!} {{-- item description--}}
    {!! Form::hidden('postage.amount[]', encrypt($postageAmount)) !!} {{-- the price of the item you seling to the user --}}
    {!! Form::hidden('postage.quantity[]', encrypt(1)) !!} {{-- how many items the user us buying --}}

    @php
        $taxAmount = round($cart['cart_vat'] * 100);
    @endphp
    {{-- the cart vat --}}
    {!! Form::hidden('vat.name[]', "VAT") !!} {{-- product name --}}
    {!! Form::hidden('vat.description[]', "VAT") !!} {{-- item description--}}
    {!! Form::hidden('vat.amount[]', encrypt($taxAmount)) !!} {{-- the price of the item you seling to the user --}}
    {!! Form::hidden('vat.quantity[]', encrypt(1)) !!} {{-- how many items the user us buying --}}

    <button type="submit" class="btn btn-primary">Pay with stripe</button>
{!! Form::close() !!}
