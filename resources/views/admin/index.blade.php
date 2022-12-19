@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Daftar User</span></h3>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary mb-4" data-toggle="modal" data-target="#modal-add-user" id="button-add-user-showmodal">
                      Add User (Excel)
                    </button>
                  </div>
              
                <div class="card-body">
                    <span id="msg"><!-- --></span>
                    <div id="users"><!-- user --></div>
                </div>
            </div>
        </div>
    </div>
</div>



  <!-- Modal Add User Membership bonus Excel-->
  <div class="modal fade" id="modal-add-user" role="dialog">
    <div class="modal-dialog">
      
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modaltitle">
            Add User
          </h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data"id="form-add-user">
            {{csrf_field()}}
            <div class="form-group">
              <label class="control-label col-md-5">Attach File Excel</label>
              <div class="col-md-5">
                <label class="btn btn-default btn-file">
                  <input type="file" name="import_file" >
                </label>
              </div>
            </div>
            <div><small>Please use .xls file <a href="{{ asset('assets/excel/example-membership.xlsx') }}">example</a></small></div>
            
          </form>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            Cancel
          </button>
          <button type="button" data-dismiss="modal" class="btn btn-primary" id="btn-add-user-free-trial">
            Add
          </button>
        </div>
      </div>
    </div>
  </div>



<script type="text/javascript">
    $(function(){
        display_users();
        ban_user();
        showModal();
        uploadExcel();
    });

    function showModal()
    {
        $("body").on("click","#button-add-user-showmodal",function(){
            $('#modal-add-user').modal('show');
        });
    }

    function uploadExcel()
    {
        $( "body" ).on( "click", "#btn-add-user-free-trial", function() {
      var uf = $('#form-add-user');
      var fd = new FormData(uf[0]);
      $.ajax({
        url: "{{url('import-excel-user')}}",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        data : fd,
        processData:false,
        contentType: false,
        beforeSend: function(result) {
          $("#div-loading").show();
        },
        dataType: 'text',
        success: function(result)
        {
          var data = jQuery.parseJSON(result);
          /*if(data.status=='error'){
            $('#pesan').html('<div class="alert alert-warning"><strong>Warning!</strong> '+data.message+'</div>');
          } else {
            $('#pesan').html('<div class="alert alert-success"><strong>Success!</strong> '+data.message+'</div>');
          }*/
          $("#div-loading").hide();
          alert(data.message);
        }        
         });
      });
  
    }

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
