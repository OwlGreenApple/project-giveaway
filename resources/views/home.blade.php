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

    function datatable()
    {
        $("#dashboard_table").DataTable();
    }

</script>

@endsection
