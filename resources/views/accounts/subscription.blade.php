<div class="row">
    <h3 class="mb-4 account-title"><b><i class="fas fa-exchange-alt main-color"></i>&nbsp;Subscription</b></h3>
</div>
<div class="card">
    <div class="card-header text-capitalize">{{ Lang::get('custom.pck') }} : {{ $pc->check_type(Auth::user()->membership)['package'] }}</div>
    <div class="card-body">
        <div><i class="fas fa-check text-black-50"></i>&nbsp;Total Event : {{ $pc->check_type(Auth::user()->membership)['campaign'] }}</div>
        <div><i class="fas fa-check text-black-50"></i>&nbsp;Total Message Per Day : {{ $pc->check_type(Auth::user()->membership)['wa'] }}</div>
        <div><i class="fas fa-check text-black-50"></i>&nbsp;Total Contestants : {{ $pc->check_type(Auth::user()->membership)['contestants'] }}</div>
    </div>
</div>
 <!-- display subscription -->
<div class="row mt-4">
        @include('package-list')
</div>