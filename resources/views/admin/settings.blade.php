@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header clearfix bg-white px-3 py-3">
                    <h3 class="float-start align-middle mb-0 info title">Settings</span></h3>
                </div>

                <div class="card-body"> 
                    <span id="msg"><!--  --></span>
                    <form id="settings">
                        <div class="form-group mb-4">
                            <label>Persentasi Affiliate (%):</label>
                            <input name="percentage" value="{{ $row->percentage }}" type="number" min="0" max="100" class="form-control form-control-lg"/>
                        </div>
                        <div class="form-group mb-4">
                            <label>Pesan Sponsor: </label> 
                            <input name="sponsor_message" value="{{ $row->sponsor }}" type="text" class="form-control form-control-lg"/>
                        </div>
                        <button type="button" id="sbt" class="btn btn-success">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        settings();
    });

    function settings()
    {
        $("body").on("click","#sbt",function(){
            var data = $("#settings").serialize();
            save_settings(data); 
        });
    }

    function save_settings(data)
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            data : data,
            url : '{{ url("save-settings") }}',
            dataType : 'json',
            success : function(result)
            {
                if(result.success == 1)
                {
                    $("#msg").html('<div class="alert alert-success">Data berhasil disimpan</div>');
                }
                else
                {
                    $("#msg").html('<div class="alert alert-danger">Server gagal</div>');
                }
            },
            error : function()
            {
                $("#msg").html('<div class="alert alert-danger">Error koneksi</div>');
            }
        });
    }

</script>
@endsection
