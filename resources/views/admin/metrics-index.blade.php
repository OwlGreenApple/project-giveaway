@extends('layouts.app')

@section('content')

<section id="tabs" class="col-md-10 offset-md-1 col-12 pl-0 pr-0 project-tab" style="margin-top:30px;margin-bottom: 120px;">
  <div class="container body-content-mobile main-cont">
    <div class="row">
        <div class="col-lg-11">
                <h2>Metrics Website</h2>
        </div>
    </div>
 
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <button class="btn btn-info" id="btn-generate">Generate</button>
    <a href="https://topleads.com/884532300.html" target="_blank"><button class="btn btn-info">Open</button></a>
 
    <!-- end container -->
  </div>

  

  
</section>
  <script type="text/javascript" src="{{ asset('/assets/canvasjs/canvasjs.min.js') }}"></script>
  <script type="text/javascript">

    $(document).ready(function() {
      submit_generate();
    });



    function submit_generate(){
      $("body").on("click","#btn-generate",function(){

        $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type : 'POST',
          url : "{{ url('generate-metrics-chart') }}",
          dataType : 'json',
          beforeSend: function()
          {
              $('#loader').show();
              $('.div-loading').addClass('background-load');
              $(".error").hide();
          },
          success : function(result)
          {
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');
              $("#div-message").show();
              $( window ).scrollTop( 0 );
              $("#div-message").html(result.message);

              if(result.err == 0)
              {
                  $("#div-message").addClass('alert-success');
                  $("#div-message").removeClass('alert-danger');
                  location.reload();
              }
              else
              {
                  $("#div-message").addClass('alert-danger');
                  $("#div-message").removeClass('alert-success');
              }
          },
          error : function()
          {
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');
          }
      });


      });
    }


  </script>    
@endsection