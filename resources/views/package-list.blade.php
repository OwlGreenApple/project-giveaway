@if(count( $pc->get_price() ) > 0)
@php $idx = 0; @endphp
    @foreach($pc->get_price() as $index=>$row)
        @if($index == 1)  @php $idx = 2; @endphp @endif
        @if($index == 3)  @php $idx = 4; @endphp @endif
        @if($index == 5)  @php $idx = 6; @endphp @endif

        @if($index == 0 || $index == 2 || $index == 4 || $index == 6)
            @php continue; @endphp
        @endif

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
                            <li><input name="year" id="{{ $idx }}" data-id="{{ $index }}" class="me-2" type="checkbox" />{{ Lang::get('order.year') }} {{ Lang::get('custom.currency') }}{{ $pc::format($pc->get_price()[$idx]['price']) }} / {{ $pc::format($pc->get_price()[$idx]['discount']) }} {{ Lang::get('order.month') }}</li>
                        @endif
                        </ul>
                    <a href="{{ url('checkout') }}/{{$index}}" target="_blank" class="btn bg-custom text-white mb-3 order-{{ $index }}">{{ Lang::get('order.order') }}</a>
                </div>
            </div>
        </div>
    @endforeach

<script type="text/javascript">
    $(function(){
        change_link();
    });

    function change_link()
    {
        $("input[name='year']").click(function()
        {
            var origin, res;
            var id = $(this).attr('id');
            var data_id = $(this).attr('data-id');
            
            if($(this).is(':checked'))
            {
                res = id;
            }
            else
            {
                res = data_id;
            }

            console.log(res);

            $(".order-"+data_id).attr('href',"{{ url('checkout') }}/"+res);
        });
    }
</script>
@endif