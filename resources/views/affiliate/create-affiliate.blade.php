@extends('layouts.app')

@section('content')
<!-- Modal Copy Link -->
<div class="modal fade" id="copy-link" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          {{ Lang::get('custom.copy.link') }}
        </h5>
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          {{ Lang::get('custom.copy.link.success') }}
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-bs-dismiss="modal">
          OK
        </button>
      </div>
    </div>
      
  </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">{{ Lang::get('auth.affiliate') }}</h1>
        </div>

        <div class="col-md-8">
            <div id="msg"><!-- --></div>
            <form id="create_affiliate">
                <!-- mode=0 insert, mode<>0 -> edit (id broadcastnya) -->
                <input type="hidden" name="mode" id="mode" value="0">
                <!-- form 1 -->
                <div class="card px-4 py-4 mb-3">
                    <div class="card-body text-center">
                        <!--<h3 class="main-color main-theme">Giveaway Information</h3>
                        <div class="border-bottom info">Competition Information</div>-->
                        <div id="div-affiliate-link">
                            <?php if (is_null($referral_code)) {?>
                                <button type="submit" class="btn bg-custom btn-lg text-white">{{ Lang::get('custom.generate') }}</button>
                            <?php } else { ?>
                                {{ Lang::get('custom.generate.your') }} : 
                                <?php 
                                    $referral_link = url('').'/'.$referral_code;
                                ?>
                                <a href="" target="_blank" id="custom-link-show">{{$referral_link}}</a> <span id="btn-copy-custom-link" class="btn-copy" data-link="{{$referral_link}}"><svg class="svg-inline--fa fa-file fa-w-12" aria-hidden="true" style="width: 0.75em;" focusable="false" data-prefix="fas" data-icon="file" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg=""><path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm160-14.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path></svg><!-- <i class="fas fa-file"></i> Font Awesome fontawesome.com --></span>

                            <?php } ?>
                        </div>
                        
                        <!-- condition -->
                        <div class="mt-3 text-justify mx-auto">
                            <h2><b>Syarat Referral Topleads :</b></h2>
                            <p>
                            - Setiap user akan mendapatkan 1 kode referral aktif<br/>
                            - Referral fee didapatkan jika user yang di refer melakukan pembelian di Topleads<br/>
                            - Berikut fee yang didapatkan :
                            </p>
                            <p>
                                1-10 referrals = dapat 15%<br/>
                                11-50 referrals = dapat 20%<br/>
                                51-100 referrals = dapat 25%<br/>
                                lebih dari 101 referrals = dapat 30%
                            </p>
                            <p>
                            - Wajib mendaftar dan memiliki member Premium (min Starter)<br/>
                            - Apabila belum memiliki member premium, Referral fee tidak bisa dicashout<br/>
                            - Segala bentuk kecurangan yang ada akan menyebabkan referral fee hangus<br/>
                            - Ketentuan & Peraturan adalah hak Topleads dan bersifat mutlak
                            </p>
                            <p>
                            Syarat Cashout Referral Fee Topleads:<br/>
                            - User wajib memiliki member Premium (min Starter)<br/>
                            - Cashout pertama yang dapat dilakukan dengan nominal Rp. 29.000<br/>
                            - Pencairan selanjutnya Anda bisa memilih nominal yang tertera, yaitu :
                                <p>
                                1. Rp. 195.000<br/>
                                2. Rp. 295.000<br/>
                                3. Rp. 395.000 <br/>
                                4. Rp. 495.000
                                </p>
                            - Referral fee dikirimkan melalui <b>DANA</b> yang telah diinformasikan dengan mengisi Nama dan No Telp DANA<br/>
                            - Pencairan akan diproses max <b>7</b> hari kerja
                            </p>
                            <a role="button" class="btn bg-custom text-white" href="{{ url('redeem-money') }}">{{ Lang::get('title.redeem') }}</a>
                        </div>
                        <!-- end condition -->
                    </div>
                </div>

                <div class="mt-5 text-center">
                </div>

            </form>
        <!-- end col -->
        </div>

    </div>
</div>



<script>
$(function() {
    save_data();
    $( "body" ).on( "click", ".btn-copy", function(e) 
    {
        e.preventDefault();
        e.stopPropagation();

        //var id = $(this).attr("data-id");
        var link = $(this).attr("data-link");

        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);

        /*$(".link-"+id).select();
        document.execCommand("copy");*/
        $('#copy-link').modal('show');
    });

});

function save_data()
{
    $("#create_affiliate").submit(function(e){
        e.preventDefault();
        var form = $("#create_affiliate")[0];
        var data = new FormData(form);

        // return false;

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method:'POST',
            url : "{{ url('save-affiliate') }}",
            data : data,
            processData : false,
            cache : false,
            contentType: false,
            dataType : 'json',
            beforeSend: function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
                // $(".error").hide();
            },
            success : function(result)
            {
                if(result.success == 1)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-success">Referral code generated</div>');
                    $("#div-affiliate-link").html('{{ Lang::get("custom.generate.your") }} : <a href="" target="_blank" id="custom-link-show">'+result.referral_link+'</a> <span id="btn-copy-custom-link" class="btn-copy" data-link="'+result.referral_link+'"><svg class="svg-inline--fa fa-file fa-w-12" aria-hidden="true" style="width: 0.75em;" focusable="false" data-prefix="fas" data-icon="file" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg=""><path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm160-14.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path></svg></span>');
                }
                else if(result.success == 2)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>')
                }
                else if(result.success == 'err')
                {
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error.id") }}</div>')
                }
            },
            error : function(xhr)
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    });
}

</script>
@endsection