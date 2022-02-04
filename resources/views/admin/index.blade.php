@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Daftar User</span></h3>
                </div>

                <div class="card-body">
                    <span id="msg"><!-- --></span>
                    <div id="users"><!-- user --></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        display_users();
        ban_user();
    });

    function data_table()
    {
        $("#contestant").DataTable();
    }

    function display_users()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("load-user") }}',
            dataType : 'html',
            success : function(result)
            {
                $("#users").html(result);
            },
            error : function()
            {
                $("#msg").html('<div class="alert alert-danger">Error koneksi</div>');
            },
            complete : function()
            {
                data_table();
            }
        });
    }

    function ban_user()
    {
        $("body").on("click",".ban",function(){
            var id = $(this).attr('id');
            var conf = confirm('Apakah yakin akan me-ban user ini?');

            if(conf == false)
            {
                return false;
            }

            $.ajax({
                method : 'GET',
                url : '{{ url("ban-user") }}',
                data : {id : id},
                dataType : 'json',
                success : function(result)
                {
                    if(result.error == 0)
                    {
                        $("#msg").html('<div class="alert alert-success">User telah di ban</div>');
                    }
                    else
                    {
                        $("#msg").html('<div class="alert alert-warning">Error server</div>');
                    }
                },
                error : function()
                {
                    $("#msg").html('<div class="alert alert-danger">Error koneksi</div>');
                },
                complete : function()
                {
                    display_users();
                }
            });
        });
    }

</script>
@endsection
