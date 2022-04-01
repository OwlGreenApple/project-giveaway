@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mt-3 px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">{{ Lang::get('title.account') }}</h1>
        </div>
        <!-- LEFT TAB -->
        <div class="col-md-3 mb-4 px-3">
            <div class="card">
                <div class="px-4 py-4">
                    <a class="settings text-black-50 mn_1 active" data_target="1"><i class="far fa-user"></i>&nbsp;{{ Lang::get('title.profile') }}</a>
                    <a class="settings text-black-50 mn_2" data_target="2"><i class="fas fa-exchange-alt"></i>&nbsp;{{ Lang::get('title.subscription') }}</a>
                    <a class="settings text-black-50 mn_6" data_target="6"><i class="fas fa-arrow-alt-circle-up"></i>&nbsp;Upgrade</a>
                    <a class="settings text-black-50 mn_3" data_target="3"><i class="fab fa-sketch"></i>&nbsp;{{ Lang::get('title.branding') }}</a>
                    <a class="settings text-black-50 mn_4" data_target="4"><i class="fas fa-plug"></i>&nbsp;{{ Lang::get('table.api') }}</a>
                    <a class="settings text-black-50 mn_5" data_target="5"><i class="fas fa-shopping-basket"></i>&nbsp;{{ Lang::get('title.order') }}</a>
                </div>
            </div>
        </div>

        <!-- RIGHT TAB -->
        <div class="col-md-9 px-3">
            <span id="msg"><!-- message --></span>
            <!-- PROFILE -->
            <div id="settings_target_1" class="card target_hide">
                <div class="card-body px-5 py-5">
                    @include('accounts.profile')
                </div>
            </div>

            <!-- SUBSCRIPTION -->
            <div id="settings_target_2" class="card target_hide d-none">
                <div class="card-body px-5 py-5">
                    @include('accounts.subscription')
                </div>
            </div>

             <!-- UPGRADE PACKAGE -->
             <div id="settings_target_6" class="card target_hide d-none">
                <div class="card-body px-5 py-5 row">
                    @include('package-list')
                </div>
            </div>


            <!-- BRANDING -->
            <div id="settings_target_3" class="card target_hide d-none">
                <div class="card-body px-5 py-5">
                    @include('accounts.branding')
                </div>
            </div>

            <!-- INTEGRATION -->
            <div id="settings_target_4" class="card target_hide d-none">
                <div class="card-body px-5 py-5">
                    @include('accounts.integration')
                </div>
            </div>

            <!-- UPLOAD PAYMENT PROOF -->
            <div id="settings_target_5" class="card target_hide d-none">
                <div class="card-body px-5 py-5">
                    @include('accounts.order')
                </div>
            </div>

            <!-- end col -->

    <!--  -->
    </div>
</div>

<script>
var segment = "{{ $conf }}";

$(function(){
    data_tabs();
    set_lang_cur();
    update_profile();
    popup_payment();
    display_detail_payment();
    payment_detail();
    upload_branding();
});

    function upload_branding()
    {
        $("#upload_branding").submit(function(e){
            e.preventDefault();
            var data = new FormData($(this)[0]);

            $.ajax({
                headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method : 'POST',
                url : '{{ url("upload-branding") }}',
                cache : false,
                processData : false,
                data : data,
                contentType: false,
                dataType : 'json',
                beforeSend: function()
                {
                    $('#loader').show();
                    $('.div-loading').addClass('background-load');
                },
                success: function(result)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    if(result.err == 0)
                    {
                        $("#brd").html("<div class='alert alert-success'>{{ Lang::get('custom.success') }}</div>");
                    }
                    else if(result.err == 'vdt')
                    {
                        $.each(result, function(i, val){
                            $(".err_"+i).html(val);
                        });
                    }
                    else
                    {
                        $("#brd").html("<div class='alert alert-danger'>{{ Lang::get('custom.failed') }}</div>");
                    }
                },
                error: function(xhr){
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }

    function display_detail_payment()
    {
        $("body").on("click",".b_payment",function()
        {
            var name = $(this).attr('data-name');
            var no = $(this).attr('data-no');
            var owner = $(this).attr('data-owner');
            var method = $(this).attr('data-value');

            $("input[name='bank_name']").val(name);
            $("input[name='bank_no']").val(no);
            $("input[name='bank_customer']").val(owner);
            $("#save-bank").attr('method',method);
            $("#bank-del").attr('data-value',method).show();
            $("#bank-payment").show();
        });
    }

    function load_page()
      {
        $("#data_order").DataTable({
            "processing": true,
            "serverSide": true,
            "lengthMenu": [ 10, 25, 50, 75, 100, 500 ],
            "ajax": "{{ url('orders') }}",
            "destroy": true
        });

        $('.dataTables_filter input')
         .off()
         .on('keyup', delay(function() {
            $('#data_order').DataTable().search(this.value.trim(), false, false).draw();
         },1000));    
    }

    function delay(callback, ms) {
      var timer = 0;
      return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
    }

    function popup_payment()
    {
      $( "body" ).on( "click", ".popup-newWindow", function()
      {
        event.preventDefault();
        window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes");
      });
    }

    function data_tabs()
    {
        if(segment == 'payment')
        {
            target_tabs(5);
        }
        
        $(".settings").click(function(){
            var target = $(this).attr('data_target');
            target_tabs(target)
        });
    }

    function target_tabs(target)
    {
        $(".settings").removeClass('active');
        $(".target_hide").addClass('d-none');
        $("#settings_target_"+target).removeClass('d-none');
        
        $(".mn").removeClass('active');
        $(".mn_"+target).addClass('active');

        if(target == 2)
        {
            $(".subs li:not(:last-child)").hide();
        }
        else
        {
            $(".subs li").show();
        }

        if(target == 5)
        {
            load_page();
        }
    }

    //PROFILE
    function set_lang_cur()
    {
        var currency = '{{ $user->currency }}';
        var lang = '{{ $user->lang }}';

        // console.log(lang);
        $("select[name='profile_currency'] option[value='"+currency+"']").prop('selected',true);
        $("select[name='profile_lang'] option[value='"+lang+"']").prop('selected',true);
    }

    function update_profile()
    {
        $("#profile").submit(function(e){
            e.preventDefault();
            var data = $(this).serialize();
            save_profile(data);
        });

        // save api
        $("#api").submit(function(e){
            e.preventDefault();
            var data = $(this).serialize();
            save_api(data);
        });
    }

    function save_profile(data)
    {
        $.ajax({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method : 'POST',
            url : '{{ url("update-profile") }}',
            data : data,
            dataType : "json",
            beforeSend: function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success :function(result)
            {
                if(result.success == 1)
                {
                    $("#msg").html("<div class='alert alert-success'>{{ Lang::get('custom.success') }}</div>");
                }
                else if(result.success == 'err')
                {
                    $(".err_"+result[0][1]).html(result[0][0]);
                    $(".err_"+result[1][1]).html(result[1][0]);
                    $(".err_"+result[2][1]).html(result[2][0]);
                    $(".err_"+result[3][1]).html(result[3][0]);
                }
                else
                {
                    $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
                }

                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            },
            error : function(xhr)
            {
                $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    function save_api(data)
    {
        $.ajax({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method : 'POST',
            url : '{{ url("save-api") }}',
            data : data,
            dataType : "json",
            beforeSend: function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success :function(result)
            {
                if(result.success == true)
                {
                    $("#msg").html("<div class='alert alert-success'>{{ Lang::get('custom.success') }}</div>");
                }
                else
                {
                    $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
                }
            },
            error:function()
            {
                $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
            },
            complete:function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    }

    //END PROFILE

    function payment_detail()
    {
        $( "body" ).on( "click", ".btn-confirm", function() {
            $('#id_confirm').val($(this).attr('data-id'));
            $('#mod-no_order').html($(this).attr('data-no-order'));
            $('#mod-package').html($(this).attr('data-package'));

            var total = parseInt($(this).attr('data-total'));
            $('#mod-total').html('Rp. ' + total.toLocaleString());
            $('#mod-purchased_view').html(parseInt($(this).attr('data-purchased-view')).toLocaleString()); 
        
            $('#mod-date').html($(this).attr('data-date'));

            var keterangan = '-';
            // console.log($(this).attr('data-keterangan'));
            if($(this).attr('data-keterangan')!='' || $(this).attr('data-keterangan')!=null){
            keterangan = $(this).attr('data-keterangan');
            }

            $('#mod-keterangan').html(keterangan);
            $("#transfer-information").modal();
        });
    }

    function view_details()
    {
        $( "body" ).on( "click", ".view-details", function() {
            var id = $(this).attr('data-id');
            $('.details-'+id).toggleClass('d-none');
        });
    }
    
  
  $( "body" ).on( "click", ".btn-search", function() {
    currentPage = '';
    refresh_page();
  });

  $( "body" ).on( "click", ".btn-delete", function() {
    $('#id_delete').val($(this).attr('data-id'));
  });

  $( "body" ).on( "click", "#btn-delete-ok", function() {
    delete_order();
  });

  $(document).on('click', '.checkAll', function (e) {
    $('input:checkbox').not(this).prop('checked', this.checked);
  });

</script>
@endsection
