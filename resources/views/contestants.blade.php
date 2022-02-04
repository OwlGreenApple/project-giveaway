@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Contestants {{ $ev->title }}</h3>
                </div>

                <div class="card-body">
                <span id="msg"><!-- message --></span>
                <table id="dashboard_table" class="table">
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
                                        <button id="{{ $row->id }}" type="button" class="btn btn-outline-danger del">{{ Lang::get('table.del') }}</button>
                                        <!-- <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ url('edit-event') }}/{{ $row->id }}">Edit</a></li>
                                            <li><a id="{{ $row->id }}" class="dropdown-item duplicate">Duplicate</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a id="{{ $row->id }}" class="dropdown-item text-danger del_ev">Delete</a></li>
                                        </ul> -->
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
        // display_dashboard();
        delete_contestanst();
        datatable();
    });

    function display_dashboard()
    {
        $.ajax({
            method:'GET',
            url:'{{ url("del-contestant") }}',
            dataType:'html',
            success: function(result)
            {
                $("#dashboard").html(result);
            },
            error:function(xhr)
            {
                $("#dashboard").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
            },
            complete : function(xhr)
            {
                datatable();
            }
        });
    }

    function delete_contestanst()
    {
        // // update events
        // $("body").on("click",".duplicate",function(){
        //     var id = $(this).attr('id');
        //     var conf = confirm('{{ Lang::get("custom.duplicate") }}');

        //     if(conf == true)
        //     {
        //         duplicate_or_del_events(id,'{{ url("duplicate-events") }}');
        //     }
        //     else
        //     {
        //         return false;
        //     }
        // });

        // delete contestants
        $("body").on("click",".del",function(){
            var id = $(this).attr('id');
            var conf = confirm('{{ Lang::get("custom.delete") }}');

            if(conf == true)
            {
                duplicate_or_del_events(id,'{{ url("del-contestant") }}');
            }
            else
            {
                return false;
            }
        });
    }

    function duplicate_or_del_events(id,target)
    {
        $.ajax({
            method:'GET',
            url: target,
            data : {'id':id},
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
