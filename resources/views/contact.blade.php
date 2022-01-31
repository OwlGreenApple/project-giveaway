@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Contact Us</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8 bg-white px-4 py-4">
            <span id="msg"><!-- --></span>
            <form id="contact_admin">
                <div class="form-group mb-3">
                    <label>Your Message:<span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-lg" name="message"></textarea>
                </div> 

                <button type="submit" class="btn bg-custom btn-lg text-white">Send</button>
            </form>
        <!-- end col -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        contact_admin()
    });

    function contact_admin()
    {
        $("#contact_admin").submit(function(e){
            e.preventDefault();

            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method : 'POST',
                url : '{{ url("contact-admin") }}',
                data : $(this).serialize(),
                dataType : 'json',
                beforeSend : function()
                {
                    $('#loader').show();
                    $('.div-loading').addClass('background-load');
                },
                success : function(result)
                {
                    if(result.err == 0)
                    {
                        $("#msg").html("<div class='alert alert-success'>{{ Lang::get('custom.success') }}</div>");
                    }
                },
                error : function()
                {
                    $("#msg").html("<div class='alert alert-danger'>{{ Lang::get('custom.error') }}</div>");
                },
                complete: function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
    }
</script>

@endsection