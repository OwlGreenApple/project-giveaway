<div class="row">
    <h3 class="mb-4 account-title"><b><i class="fas fa-exchange-alt main-color"></i>&nbsp;{{ Lang::get('title.subscription') }}</b></h3>
</div>
<div class="card">
    <div class="card-header text-capitalize">{{ Lang::get('custom.pck') }} : {{ $pc->check_type(Auth::user()->membership)['package'] }}</div>
    <div class="card-body">
        <div><i class="fas fa-check text-black-50"></i>&nbsp;{{ Lang::get('table.total.event') }} : {{ $pc->check_type(Auth::user()->membership)['campaign'] }}</div>
        <div><i class="fas fa-check text-black-50"></i>&nbsp;{{ Lang::get('table.total.message') }} : {{ $pc->check_type(Auth::user()->membership)['wa'] }}</div>
        <div><i class="fas fa-check text-black-50"></i>&nbsp;{{ Lang::get('table.total.contestant') }} : {{ $pc->check_type(Auth::user()->membership)['contestants'] }}</div>
    </div>
</div>
 <!-- display subscription -->
<div class="mt-4 text-left col-lg-4 col-md-6">
    <a class="btn btn-success settings" data_target="6" role="button"><i class="fas fa-arrow-alt-circle-up text-warning"></i>&nbsp;{{ Lang::get('auth.package') }}</a>
</div>