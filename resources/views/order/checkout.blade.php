@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{asset('assets/css/checkout.css')}}">

<div class="container" style="margin-bottom:100px">
  <div class="row justify-content-center">
    <div class="col-md-8 col-12">
      <div class="card-custom">
        <div class="card cardpad">

          <!-- FORM -->
						<form id="submit_checkout">
              <h2 class="Daftar-Disini">{{ Lang::get('order.choose') }}</h2>
							
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <label class="text" for="formGroupExampleInput">{{ Lang::get('order.package') }} :</label>
                  <select class="form-select text-capitalize" id="select-auto-manage">
                      @foreach($api->get_price() as $index=>$row)
                        @if($row['price'] > 0) 
                           @if($index == $page)
                            <option data-price="{{ $row['price'] }}" value="{{ $row['package'] }}" selected="selected">
                              {{$row['package']}} - IDR {{ $api::format($row['price']) }}
                            </option>
                            @else
                            <option data-price="{{ $row['price'] }}" value="{{ $row['package'] }}">
                              {{$row['package']}} - IDR {{ $api::format($row['price']) }}
                            </option>
                            @endif
                        @endif
                      @endforeach
                  </select>
                  <div class="text-danger package"><!-- error --></div>
                </div>
                <!--  -->
              </div>

             <!--  if(is_coupon == false)
              <div class="form-group">
                <div class="col-md-12 col-12">
                  <label class="label-title-test" for="formGroupExampleInput">
                    Coupon code (optional):
                  </label>

                  <input type="text" class="form-control form-control-lg" name="kupon" id="kupon" placeholder="Kode Kupon Anda" style="width:100%">  
                  <button type="button" class="btn btn-primary btn-kupon  form-control-lg col-md-3 col-sm-12 col-xs-12 mt-3">
                    Apply
                  </button>  
                </div>
              </div>
              endif;
 -->
              <div class="form-group mb-1">
                <div class="col-md-12 col-12">
                  <div id="pesan"><!-- notification --></div>
                </div>
              </div>
             
              <div class="form-group">
                <div class="col-md-12 col-12">
                  <label class="label-title-test" for="formGroupExampleInput">
                    Total: 
                  </label>
                  <div class="col-md-12 pl-0">
                    <span class="total" style="font-size:18px"></span>
                  </div>
              </div>

              <div class="form-group mt-4">
                <div class="col-12 col-md-12">
                  <input type="checkbox" name="agree-term" id="agree-term" class="agree-term mr-1" required/>
                  <label for="agree-term" class="label-agree-term text">{{ Lang::get('order.agreement.checkout') }}<a href="{{ url('privacy') }}" class="term-service" target="_blank">{{ Lang::get('auth.terms') }}</a></label>
                </div>
              </div>
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <input type="submit" name="submit" id="submit" class="col-md-12 col-12 btn btn-primary bsub btn-block" value="{{ Lang::get('order.proceed') }}"/>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function(){
    setPricing();
    // check_kupon();
    manageSelectPackage();
    checkout();
    change_qty();
    // applyCoupon();
    // setUpgradeOption();
  });

  function checkout()
  {
    $("#submit_checkout").submit(function(e){
      e.preventDefault();
      submit_payment();
    });
  }

  function submit_payment()
  {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: "{{url('submit_payment')}}",
      data: {
        package :  $("#select-auto-manage").val()
      },
      dataType: 'json',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result)
      {
        if(result.status == 1)
        {
          location.href="{{url('summary')}}";
        }
        else if(result.status == 2)
        {
          location.href="{{url('thankyou')}}";
        }
        else
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(result.status == 0 && result.msg == undefined)
          {
            $(".alert-danger").hide();
            $(".package").html(result.package);
          }
          else
          {
            $("#pesan").html('<div class="alert alert-danger">'+result.msg+'</div>');
            $(".alert-danger").show();
          }
        }
      },
      error : function()
      {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
      }
    });
    /**/
  }

  function setUpgradeOption()
  {
    $("input[name='status_upgrade']").change(function(){
      var value = $(this).val();
      if(value == 2)
      {
        $(".upgrade-later").hide();
      }
      else
      {
        $(".upgrade-later").show();
      }
      // check_kupon(value);
    });
  }

  function check_kupon(status_upgrade = null){

    if(status_upgrade == null)
    {
      status_upgrade = $("input[name='status_upgrade']").val();
    }

    $.ajax({
      type: 'POST',
      url: "{{url('/check-coupon')}}",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        harga : $('#price').val(),
        kupon : $('#kupon').val(),
        reseller_coupon : "",
        idpaket : $( "#select-auto-manage" ).val(),
        status_upgrade : status_upgrade,
        chat : 0,
        namapaket :  $("#select-auto-manage").find("option:selected").attr("data-paket")
      },
      dataType: 'text',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');

        var data = jQuery.parseJSON(result);

        $('#pesan').html(data.message);
        $('#pesan').show();
        if (data.message=="") {
          $('#pesan').hide();
        }
        
        if (data.status == 'success') {
          $('.total').html('IDR '+' <strike>'+formatNumber(data.price)+'</strike> '+formatNumber(data.total));
          $('#pesan').removeClass('alert-danger');
          $('#pesan').addClass('alert-success');

         if(status_upgrade == 2)
          {
            $(".upgrade").show();
            $("#package-upgrade").hide();
            $("#label-priceupgrade").hide();
            $("input[name='status_upgrade']").prop('disabled',false);
          }
          else if(data.membership == 'upgrade')
          {
            $(".upgrade").show();
            $("#package-upgrade").show();
            $("#label-priceupgrade").show();
            $("input[name='status_upgrade']").prop('disabled',false);
            $(".dayleft").html(data.dayleft);
            $("#package-upgrade").html("IDR "+formatNumber(data.upgrade_price));
            $("#label-priceupgrade").html("IDR "+formatNumber(data.packageupgrade));
          }
          else //downgrade
          {
            $("#package-upgrade").hide();
            $("#label-priceupgrade").hide();
            $("input[name='status_upgrade']").prop('disabled',true);
            $(".upgrade").hide();
          }
        } 
        else {
          $('#pesan').removeClass('alert-success');
          $('#pesan').addClass('alert-danger');
          $('.total').html('IDR '+formatNumber(data.total));
        }
      },
      error: function(xhr,atrribute,throwable)
      {
         $('#loader').hide();
         $('.div-loading').removeClass('background-load');
         // console.log(xhr.responseText);
      }
    });
  }
  
	function formatNumber(num) {

    num = parseInt(num);
    if(isNaN(num) == true)
    {
       return 0;
    }
    else
    {
       var formatted = num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
       return formatted;
    }
	}

  function setPricing()
  {
    var price = parseInt($("#select-auto-manage").find("option:selected").attr("data-price"));
    $('.total').html('IDR '+formatNumber(price));
  }

  function change_qty()
  {
    $("input[name='qty']").on('change keyup',function(){
      setPricing();
    });
  }

  function applyCoupon()
  {
    $(".btn-kupon").click(function(){
      var value = $("input[name='status_upgrade']:checked").val();
      check_kupon(value);

      if(value == 2)
      {
        $(".upgrade-later").hide();
      }
      else
      {
        $(".upgrade-later").show();
      }
    });
  }

  // change option when user choose package
  function manageSelectPackage()
  {
    $( "#select-auto-manage" ).change(function() {
      setPricing();
    })
  }
    
</script>
@endsection