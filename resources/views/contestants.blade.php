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
                <div class="text-left mb-4"><a role="button" class="btn btn-success" id="gprize" ev_id="{{ $ev->id }}">{{ Lang::get('table.award') }}</a></div>
                <span id="msg"><!-- message --></span>
                <div class="table-responsive">
                    <table id="dashboard_table" class="display nowrap table">
                        <thead>
                            @if(isset($winner))
                                @if($ungiving > 0)
                                    <th><input type="checkbox" class="form-checks" id="check_all" /></th>
                                @endif
                            @endif
                            <th>{{ Lang::get('table.no') }}</th>
                            <th>{{ Lang::get('table.email') }}</th>
                            <th>{{ Lang::get('table.name') }}</th>
                            <th>{{ Lang::get('table.wa') }}</th>
                            <th>{{ Lang::get('table.entry') }}</th>
                            <th>{{ Lang::get('table.referral') }}</th>
                            <th>{{ Lang::get('table.ip') }}</th>
                            <th>{{ Lang::get('table.date') }}</th>
                            <th><!-- Lang::get('table.act') --></th>
                        </thead>
                        <tbody>
                            @if($data->count() > 0)
                                <form id="prize">
                                @foreach($data as $row) 
                                <tr>
                                    @if(isset($winner))
                                        @if($ungiving > 0)
                                        <td class="align-middle">
                                            @if($row->status == 0)
                                                <input type="checkbox" name="winner[]" class="form-checks checks" value="{{ $row->id }}" />
                                            @endif
                                        </td>
                                        @endif
                                    @endif
                                    <td class="align-middle">{{ $no++ }}</td>
                                    <td class="align-middle">{{ $row->c_email }}</td>
                                    <td class="align-middle">{{ $row->c_name }}</td>
                                    <td class="align-middle">{{ $row->wa_number }}</td>
                                    <td class="align-middle">{{ $row->entries }}</td>
                                    <td class="align-middle">{{ $row->referrals }}</td>
                                    <td class="align-middle">{{ $row->ip }}</td>
                                    <td class="align-middle">{{ $row->date_enter }}</td>
                                    <td class="align-middle">
                                        <div class="input-group">
                                            @if(isset($winner))
                                                @if($row->status == 0)
                                                    -
                                                    <!-- <a class="btn btn-warning draw" ev_id="{{ $row->event_id }}" id="{{ $row->id }}">{{ Lang::get('table.award') }}</a> -->
                                                @else
                                                    <span class="text-success">{{ Lang::get('table.award.done') }}</span>
                                                @endif
                                            @else
                                                <button id="{{ $row->id }}" type="button" class="btn btn-outline-danger del">{{ Lang::get('table.del') }}</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                </form>
                            @else
                                <tr><td colspan="9" class="text-center"><div class="alert alert-info">{{ Lang::get('custom.no_data') }}</div></tr> 
                            @endif
                        </tbody>
                    </table>
                </div>
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
        /* $("body").on("click",".draw",function(){
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
        }); */

        //winner contestant
        $("body").on("click","#gprize",function(){
            var ev_id = $(this).attr('ev_id');
            var conf = confirm('{{ Lang::get("custom.redraw") }}');

            if(conf == true)
            {
                var data = $("#prize").serializeArray();
                data.push({name : 'ev_id', value :"{{ $ev->id }}"});
                prize(data);
            }
            else
            {
                return false;
            }
        });

        // delete contestants
        /* $("body").on("click",".del",function(){
            var id = $(this).attr('id');
            var conf = confirm('{{ Lang::get("custom.delete") }}');
            del_contestants(id);
        }); */
    }

    function prize(data)
    {
        $.ajax({
            headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            url : "{{ url('prize') }}",
            data : data,
            datatType:"json",
            beforeSend : function(){
                $("#loader").show();
                $('.div-loading').addClass('background-load');
            },
            success : function(row)
            {
                if(row.status === 1)
                {
                    location.href="{{ url('contestant-winner') }}/{{ $ev->id }}"
                }
                else
                {
                    $("#loader").hide();
                    $('.div-loading').removeClass('background-load');
                }
            },
            error : function(xhr)
            {
                console.log(xhr.responseText);
            }
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

    function redraw_or_delete(id,target,remove_winner,data)
    {
        data = data.push({'id':id,'ev_id':"{{ $ev->id }}"});
        $.ajax({
            method:'GET',
            url: target,
            data : data, 
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
        // check all logic
        $("body").on("click","#check_all",function(){
            var checked = $(this).prop('checked');

            if(checked === true)
            {
                $(".checks").prop('checked',true);
            }
            else
            {
                $(".checks").prop('checked',false);
            }

        });

        /* $("#dashboard_table").DataTable({
            "pageLength": 5
        }); */
    }

</script>

@endsection
