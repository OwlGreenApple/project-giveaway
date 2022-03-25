@extends('layouts.app')

@section('content')

<section id="tabs" class="col-md-10 offset-md-1 col-12 pl-0 pr-0 project-tab" style="margin-top:30px;margin-bottom: 120px;">
  <div class="container body-content-mobile main-cont">
    <div class="row">
        <div class="col-lg-11">
          <h2>Affiliate</h2>
        </div>
    </div>
 
    <div class="table-responsive"><div id="content"><!-- content --></div></div>
    <!-- end container -->
  </div>

  <!-- MODAL -->
  <div class="modal fade child-modal" id="buktibayar" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-header text-center">
          <div class="modal-title">
            Upload Bukti Bayar
          </div>
        </div>

        <div class="modal-body text-center">
            <div class="err"><!-- error --></div>
            <form id="payment">
              <div class="form-group text-left">
                <input type="file" class="form-control" name="buktibayar" />
                <div class="text-danger"><small class="error"><!-- error validation --></small></div>
                <button id="upload_payment" class="btn btn-success btn-sm mt-2">Upload Payment</button>
                <button type="button" data-dismiss="modal" class="btn btn-dark btn-sm mt-2">Batal</button>
              </div>
            </form>
        </div>

      </div>
      
    </div>
  </div>
  <!-- End Modal -->

  <!-- MODAL -->
  <div class="modal fade child-modal" data-bs-toggle="modal" id="buktibayar2" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-header text-center">
          <div class="modal-title">
            Confirm penarikan
          </div>
        </div>

        <div class="modal-body text-center">
            <div class="err"><!-- error --></div>
            <form id="payment2">
              <div class="form-group text-left">
                <label class="label">Metode penarikan : </label>
                <input type="text" class="form-control" value="DANA" name="withdrawal_method" id="withdrawal_method">
                <div class="text-danger"><small class="error"><!-- error validation --></small></div>
                <button id="upload_payment_2" class="btn btn-success btn-sm mt-2">Confirm</button>
                <button type="button" data-dismiss="modal" class="btn btn-dark btn-sm mt-2">Batal</button>
              </div>
            </form>
        </div>

      </div>
      
    </div>
  </div>
  <!-- End Modal -->
  
  
</section>
  <script type="text/javascript">

    $(document).ready(function() {
      refresh_page();
      open_payment();
      payment();
    });

    function open_payment()
    {
      $("body").on("click",".reward",function(){
        var id = $(this).attr('id');
        // $("#buktibayar").modal();
        $("#buktibayar2").modal('show');
        $("#upload_payment_2").attr('data-id',id);
      });
    }

    function payment()
    {
      $("#payment2").submit(function(e){
        e.preventDefault();
        var id = $("#upload_payment_2").attr('data-id');
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append('id',id);
        upload_payment(formData);
      });
    }

    function upload_payment(formData)
    {
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type : 'POST',
        processData : false,
        cache : false,
        contentType: false,
        url : "{{ url('upload-payment-2-redeem') }}",
        data : formData,
        dataType: 'json',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $(".err").show();

          if(result.err == 0)
          {
            $(".err").html('<div class="alert alert-success">Your payment gift has been uploaded.</div>');
            $(".error").html('');
            refresh_page();
          }
          else if(result.err == 2)
          {
            $(".error").html('File cannot be empty');
          }
          else
          {
            $(".err").html('<div class="alert alert-danger">Sorry our server is too busy.</div>');
          }
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $(".err").html('<div class="alert alert-danger">Sorry our server is too busy.</div>');
        },
        complete : function(xhr)
        {
          setTimeout(function(){
            $(".err").hide();
          },3500);
        }
      });
    }

    function refresh_page(){
      $.ajax({
        type : 'GET',
        url : "{{ url('affiliate-admin-data') }}",
        dataType: 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $('#content').html(result);
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          alert('Sorry our server is too busy, please try again later');
        }
      });
    }

  </script>    
@endsection