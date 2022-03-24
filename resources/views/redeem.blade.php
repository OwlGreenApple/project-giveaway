@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="big-theme" align="center">Redeem Money</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8 bg-white px-4 py-4">
            <span id="msg"><!-- --></span>
            <div class="mb-3" align="right">Funds : {{ $helper::currency()['idr'] }}-<b>{{ $helper::format(Auth::user()->money) }}</b></div>

            <form class="contest-form" id="redeem">
                <div class="form-group mb-3">
                    <label>{{Lang::get('auth.account')}}</label>
                    <input name="account" required type="text" class="form-control form-control-lg" />
                    <span class="text-danger err_account"><!-- --></span>
                </div>
                <div class="form-group mb-3">
                    <label>{{Lang::get('auth.account.number')}}</label>
                    <input name="number" required type="number" class="form-control form-control-lg" />
                    <span class="text-danger err_number"><!-- --></span>
                </div>
                <div class="form-group mb-3">
                    <label>{{Lang::get('auth.account.confirm')}}</label>
                    <input name="confirm" type="text" required class="form-control form-control-lg" required/>
                    <span class="text-danger err_confirm"><!-- --></span>
                </div>
                <div class="form-group mb-3">
                    <label>{{Lang::get('auth.account.money')}}</label>
                    <select name="amount" class="form-select">
                        @foreach($funds as $index=>$row)
                            <option value="{{ $index }}">{{ $helper::currency()[Lang::get('auth.currency')] }}-{{ $helper::format($row) }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger err_amount"><!-- --></span>
                </div>
                <button type="submit" class="btn bg-custom text-white">{{ Lang::get('custom.redeem') }}</button>
            </form>
        <!-- end col -->
        </div>

        <!-- display redeem list -->
        <div class="col-md-8 bg-white px-4 py-2">
            <hr/>
            <table id="data_redeem" class="responsive">
                <thead>
                    <th>Name</th>
                    <th>Account Name</th>
                    <th>No Account</th>
                    <th>Total</th>
                    <th>Status</th>
                </thead>
                @if($data->count() > 0)
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->account_name }}</td>
                            <td>{{ $row->account }}</td>
                            <td>{{ $helper::currency()[Lang::get('auth.currency')] }}.{{ $helper::format($row->total) }}</td>
                            <td>{!! $row->status !!}</td> 
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
        </div>

    </div>
    <!--  -->
</div>

<script>

    $(function(){
        redeem();
        datatable();
    });

    function datatable()
    {
        $("#data_redeem").dataTable();
    }

    function redeem()
    {
        $("#redeem").submit(function(e){
            e.preventDefault();
            var data = $(this).serializeArray();
            withdraw(data);
        });
    }

    function withdraw(data)
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            data : data,
            url : '{{ url("redeem-withdraw") }}',
            dataType : 'json',
            beforeSend : function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                if(result.success == 1)
                {
                    location.href="{{ url('redeem-money') }}";
                }
                else if(result.success == 'err')
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    $.each(result, function( index, value ) {
                        $(".err_"+index).html(value);
                    });
                }
                else
                {
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>');
                }
            },
            error : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

</script>
@endsection
