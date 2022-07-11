@extends('layouts.app')

@section('content')
<div class="container mb-5 mt-2 col-lg-9">
    <div class="pricing mb-3 price_list_data">
        @include('package-list')
    </div>

    <div class="row border border-secondary">
    @for($x=0;$x<=2;$x++)
        <div class="col-lg-4 col-md-4 col-12">
            <!--  -->
            <div class="card-body pt-3 pb-3 mx-auto">
                <ul class="subs list-unstyled mb-0">
                    @if($x == 0)
                        <li>{{ Lang::get('table.package.doubt') }}</li>
                        <li>{{ Lang::get('table.package.start') }}</li>
                        <li style="height : 10px;">&nbsp;</li>
                        <li><b class="text-black">{{ Lang::get('table.package.free') }}</b></li>
                        <li><b><a class="text-black text-decoration-none" href="{{ url('register') }}">► {{ Lang::get('table.package.order') }}</a></b></li>
                    @elseif($x == 1)
                        <li>{{ Lang::get('table.package.suitable') }}</li>
                        <li>{{ Lang::get('table.package.limit') }}</li>
                    @else
                        <li><b class="text-black">{{ Lang::get('table.package.feature') }} :</b></li>
                        <li>{{ Lang::get('table.package.max') }}</li>
                        <li>{{ Lang::get('table.package.total') }}</li>
                        <li>{{ Lang::get('table.package.support') }}</li>
                    @endif
                </ul>
            </div>
        </div>
    @endfor
    </div>
</div>
    
<!-- price list --> 
<script src="{{ asset('assets/js/pricing.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        change_price_list();
    });
</script>
@endsection
