@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">@if(!isset($winner)) {{ Lang::get('table.contestant') }} @endif {{ $ev->title }} @if(isset($winner)) {{ Lang::get('table.winner') }} @endif</h3>
                    @if(Auth::user()->membership !== 'free' && !isset($winner))
                        <h3 class="float-end"><a href="{{ url('export-contestant') }}/{{ $ev->id }}" class="btn btn-success">Export XLS</a></h3>
                    @endif
                </div>

                <div class="card-body">
                <span id="msg"><!-- message --></span>
                <table id="dashboard_table" class="table stripe">
                    <thead>
                        <th>{{ Lang::get('table.no') }}</th>
                        <th>{{ Lang::get('table.name') }}</th>
                        <th>{{ Lang::get('table.email') }}</th>
                        <th>{{ Lang::get('table.wa') }}</th>
                        <th>{{ Lang::get('table.entry') }}</th>
                        <th>{{ Lang::get('table.referral') }}</th>
                        <th>{{ Lang::get('table.ip') }}</th>
                        <th>{{ Lang::get('table.date') }}</th>
                        <th>{{ Lang::get('table.act') }}</th>
                    </thead>
                    <tbody>
                        @if($data->count() > 0)
                            @foreach($data as $row) 
                            <tr>
                                <td class="align-middle">{{ $no++ }}</td>
                                <td class="align-middle">{{ $row->c_name }}</td>
                                <td class="align-middle">{{ $row->c_email }}</td>
                                <td class="align-middle">{{ $row->wa_number }}</td>
                                <td class="align-middle">{{ $row->entries }}</td>
                                <td class="align-middle">{{ $row->referrals }}</td>
                                <td class="align-middle">{{ $row->ip }}</td>
                                <td class="align-middle">{{ $row->date_enter }}</td>
                                <td class="align-middle">
                                    <div class="input-group">
                                        @if(isset($winner))
                                            @if($row->status == 0)
                                                <a class="btn btn-warning draw" ev_id="{{ $row->event_id }}" id="{{ $row->id }}">{{ Lang::get('table.award') }}</a>
                                            @else
                                                <span class="text-info">{{ Lang::get('table.award.done') }}</span>
                                            @endif
                                        @else
                                            <button id="{{ $row->id }}" type="button" class="btn btn-outline-danger del">{{ Lang::get('table.del') }}</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr><td colspan="9" class="text-center"><div class="alert alert-info">{{ Lang::get('custom.no_data') }}</div></tr> 
                        @endif
                    </tbody>
                </table>
                <!--  -->
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        action_table();
        datatable();
    });

    function action_table()
    {
        // redraw contestants
        $("body").on("click",".draw",function(){
            var id = $(this).attr('id');
            var conf = confirm('{{ Lang::get("custom.redraw") }}');

            if(conf == true)
            {
                redraw_or_delete(id,'{{ url("draw-contestant") }}',0);
            }
            else
            {
                return false;
            }
        });

        // delete contestants
        $("body").on("click",".del",function(){
            var id = $(this).attr('id');
            var conf = confirm('{{ Lang::get("custom.delete") }}');
            del_contestants(id);
        });
    }

    function del_contestants(id)
    {
        $.ajax({
            method:'GET',
            url:'{{ url("del-contestant") }}',
            data : {'id':id},
            dataType:'json',
            success: function(result)
            {
                if(result.err == 0)
                {
                    location.href="{{ url('list-contestants') }}/{{ $ev->id }}";
                }
                else
                {
                    $("#loader").hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}--</div>");
                }
            },
            error:function(xhr)
            {
                $("#loader").hide();
                $('.div-loading').removeClass('background-load');
                $("#dashboard").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
            },
            complete : function(xhr)
            {
                datatable();
            }
        });
    }

    function redraw_or_delete(id,target,remove_winner)
    {
        $.ajax({
            method:'GET',
            url: target,
            data : {'id':id,'ev_id':"{{ $ev->id }}"},
            dataType:'json',
            beforeSend : function()
            {
                $("#loader").show();
                $('.div-loading').addClass('background-load');
            },
            success: function(result)
            {
                if(result.err == 0)
                {
                    location.href="{{ url('contestant-winner') }}/{{ $ev->id }}";
                }
                else
                {
                    $("#loader").hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}--</div>");
                }
            },
            error:function(xhr)
            {
                $("#loader").hide();
                $('.div-loading').removeClass('background-load');
                $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
            },
        });
    }

    function datatable()
    {
        $("#dashboard_table").DataTable();
    }

</script>

@endsection
