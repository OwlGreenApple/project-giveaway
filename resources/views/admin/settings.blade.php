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
                    <div class="alert">
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

                    <hr/>

                    <div class="alert">
                        <div class="card-header clearfix bg-white px-1 py-1 mb-3">
                            <h3 class="float-start align-middle mb-0 info title">Tambah no WA</span></h3>
                        </div>
                        <span id="msg_phone"><!--  --></span>
                        <form id="admin_phone"> 
                            <div class="form-group mb-4">
                                <label>Service</label>
                                <select class="form-select" name="service">
                                    <option value="1">WABLAS</option>
                                    <option value="2">WAFONNTE</option>
                                </select>
                            </div>
                            <div class="form-group mb-4 wablas-server">
                                <label>WABLAS Server</label>
                                <select class="form-select" name="wablas">
                                    @foreach($ct::get_wablas() as $value=>$row)
                                        <option value="{{ $value }}">{{ $row }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label>Nomor WA</label>
                                <input name="phone" required="true" type="text" class="form-control"/>
                            </div>
                            <div class="form-group mb-4"> 
                                <label>API KEY</label> 
                                <input name="api_key" required="true" type="text" class="form-control"/>
                            </div>
                            <input name="phone_id" type="hidden" class="form-control"/>
                            <button type="button" id="wa" class="btn btn-success">Tambah No WA</button>
                            <button type="button" id="cancel" class="btn btn-secondary">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- phone table list -->

    <div class="mt-4 table-responsive" id="phone_table"></div>

    <!-- end container -->
</div>

<script src="{{ asset('assets/js/pricing.js') }}"></script> 
<script type="text/javascript"> 
    $(function(){
        service();
        settings();
        display_phone();
        edit_phone();
        cancel();
    });

    function cancel()
    {
        $("#cancel").click(function(){
            $("input").val('');
            $("#wa").html('Tambah No WA');
            $("#msg_phone .alert").addClass('d-none');
        });
    }

    function settings()
    {
        $("body").on("click","#sbt",function(){
            var data = $("#settings").serialize();
            save_settings(data,'{{ url("save-settings") }}'); 
        });

        $("#wa").click(function(){
            var data = $("#admin_phone").serialize();
            var vld = simple_validate();

            if(vld === true)
            {
                save_settings(data,'{{ url("save-admin-phone") }}',1); 
            }
        });
    }

    function simple_validate()
    {
        var phone = $("input[name='phone']").val();
        var api = $("input[name='api_key']").val();

        if(phone.length < 6)
        {
            alert('No WA tidak valid');
            return false;
        }

        if(api.length < 1)
        {
            alert('API token tidak boleh kosong');
            return false;
        }

        return true;
    }

    function save_settings(data,url,event)
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method : 'POST',
            data : data,
            url : url,
            dataType : 'json',
            success : function(result)
            {
                if(result.success == 1)
                {
                    if(event == 1)
                    {
                        $("#msg_phone .alert").removeClass('d-none');
                        $("#msg_phone").html('<div class="alert alert-success">Data berhasil disimpan</div>');
                    }
                    else
                    {
                        $("#msg").html('<div class="alert alert-success">Data berhasil disimpan</div>');
                    }
                }
                else
                {
                    $("#msg").html('<div class="alert alert-danger">Server gagal</div>');
                }
            },
            error : function()
            {
                $("#msg").html('<div class="alert alert-danger">Error koneksi</div>');
            },
            complete : function()
            {
                display_phone();
            }
        });
    }

    function dataTable()
    {
        $("#phone_admin").dataTable();
    }

    function display_phone()
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("admin-phone") }}',
            dataType : 'html',
            success : function(result)
            {
                $("#phone_table").html(result);
                cancel();
            },
            error : function()
            {
                $("#msg_phone").html('<div class="alert alert-danger">Error koneksi</div>');
            },
            complete : function()
            {
                dataTable();
            }
        });
    }

    function delete_phone(id)
    {
        $.ajax({
            method : 'GET',
            url : '{{ url("admin-phone-del") }}',
            data : {id : id},
            dataType : 'json',
            success : function(result)
            {
                if(result.success == 1)
                {
                    $("#msg_phone .alert").removeClass('d-none');
                    $("#msg_phone").html('<div class="alert alert-success">Data berhasil dihapus</div>');
                    display_phone();
                }
                else
                {
                    $("#msg_phone").html('<div class="alert alert-danger">Server gagal</div>');
                }
            },
            error : function()
            {
                $("#msg_phone").html('<div class="alert alert-danger">Error koneksi</div>');
            }
        });
    }

    function edit_phone()
    {
        $("body").on("click",".edit",function()
        {
            var id = $(this).attr('data-id');
            var phone = $(this).attr('data-phone');
            var api_key = $(this).attr('data-api');
            var service = $(this).attr('data-service');
            var wablas = $(this).attr('data-wablas');

            $("input[name='phone_id']").val(id);
            $("input[name='phone']").val(phone);
            $("input[name='api_key']").val(api_key);
            $("select[name='service'] > option[value='"+service+"']").prop('selected',true);
            $("select[name='wablas'] > option[value='"+wablas+"']").prop('selected',true);

            change_service(service);
            $("#wa").html('Edit No WA');
            location.href="#admin_phone";
        });

        $("body").on("click",".del",function()
        {
            var id = $(this).attr('data-id');
            var cfm = confirm('Apakah yakin mau menghapus?')

            if(cfm == true)
            {
                delete_phone(id);
            }
            else
            {
                return false;
            }
        });
    }

</script>
@endsection
