@if(count( $data ) > 0)
    <div class="ms-auto me-auto col-lg-6 col-md-6 col-12 mb-4">
        <div style="max-width:236px" class="input-group ms-auto me-auto col-lg-6 col-md-6 col-12">
            <button type="button" class="btn bg-custom text-white pricing_list">{{ Lang::get('order.month.m') }}</button>
            <button data-total="3" style="border-left:1px solid white;" type="button" class="btn bg-custom text-white pricing_list">{{ Lang::get('order.month.t') }}</button>
            <button data-total="12" style="border-left : 1px solid white;" type="button" class="btn bg-custom text-white pricing_list">{{ Lang::get('order.month.y') }}</button>
        </div>
        @if($save > 0)
            <div class="ms-auto me-auto text-center mt-2 col-lg-6 text-success">{{ Lang::get('order.save') }} {{ $save }}%</div>
        @endif
    </div>

    <div class="row">
    @foreach($data as $index)
        <div class="col-lg-4 col-md-4 col-12">
            <div class="card card-pricing @if($index == 1) popular @endif shadow text-center px-3 mb-4">
                <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-custom text-white shadow-sm text-capitalize">{{ $pc->get_price()[$index]['package'] }}</span>
                <div class="bg-transparent card-header pt-4 border-0">
                    <h3 class="@if($account == 1) h4 @else h3 @endif font-weight-normal text-custom text-center mb-0" data-pricing-value="30">{{ Lang::get('custom.currency') }}&nbsp;<span class="price">{{ $pc::format($pc->get_price()[$index]['price']) }}</span><hr><div class="h6 text-muted ml-2"><span class="text-capitalize">{{ Lang::get('order.month') }}</span></div></h3>
                </div>
                <!--  -->
                <div class="card-body pt-0">
                    <ul style="@if($account == 1) font-size:14px @else font-size:16px @endif" class="subs list-unstyled mb-4">
                        <li>Max {{ $pc->get_price()[$index]['contestants'] }} {{ Lang::get('order.contestants') }}</li>
                        <li>{{ Lang::get('order.campaign') }} : <b>{{ $pc->get_price()[$index]['campaign'] }}</b></li>
                        <li>{{ Lang::get('order.auto') }}</li>
                        <li>@if($index > 2) <b>{{ Lang::get('order.no_sponsor') }}</b> @endif {{ Lang::get('order.sponsor') }}</li>

                        @if($index > 0)
                            <li>{{ Lang::get('order.history') }}</li>
                            <li>{{ Lang::get('order.winner') }}</li>
                            <li>{{ Lang::get('order.export') }}</li>
                            <li>WA {{ $pc->get_price()[$index]['wa'] }} Message / {{ Lang::get('order.day') }}</li>
                        @endif
                        </ul>
                    <a href="{{ url('checkout') }}/{{$index}}" target="_blank" class="btn bg-custom text-white mb-3 order-{{ $index }}">{{ Lang::get('order.order') }}</a>
                </div>
            </div>
        </div>
    @endforeach
    </div>
@endif