@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Your Giveaways</h3>
                    <div class="float-end align-middle "><a href="{{ url('create') }}" class="btn btn-default bg-custom text-white">New Giveaway</a></div>
                </div>

                <div id="dashboard" class="card-body"><!-- display dashboard here --></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        display_dashboard();
        duplicate_or_del_ev();
    });

    function display_dashboard()
    {
        $.ajax({
            method:'GET',
            url:'{{ url("dashboard") }}',
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

    function duplicate_or_del_ev()
    {
        // update events
        $("body").on("click",".duplicate",function(){
            var id = $(this).attr('id');
            var conf = confirm('{{ Lang::get("custom.duplicate") }}');

            if(conf == true)
            {
                duplicate_or_del_events(id,'{{ url("duplicate-events") }}');
            }
            else
            {
                return false;
            }
        });

        // delete events
        $("body").on("click",".del_ev",function(){
            var id = $(this).attr('id');
            var conf = confirm('{{ Lang::get("custom.delete") }}');

            if(conf == true)
            {
                duplicate_or_del_events(id,'{{ url("delete-events") }}');
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
            dataType:'html',
            beforeSend : function()
            {
                $("#loader").show();
                $('.div-loading').addClass('background-load');
            },
            success: function(result)
            {
                display_dashboard();
            },
            error:function(xhr)
            {
                $("#dashboard").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
            },
            complete : function(xhr){
                $("#loader").hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function datatable()
    {
        $("#dashboard_table").DataTable();
    }

</script>

@endsection
