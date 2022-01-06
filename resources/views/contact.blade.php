@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Contact Us</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8">
            <form>
                <div class="form-group mb-3">
                    <label>Name:<span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" name="user" />
                </div> 

                <div class="form-group mb-3">
                    <label>Method:</label>
                    <select class="form-select form-select-lg" name="method">
                        <option value="1">Email</option>
                        <option value="2">Whatsapp</option>
                    </select>
                </div>

                <div class="form-group col-md-6 col-lg-6 mb-3">
                    <label>
                        <span class="em">Your email <span class="text-danger">*</span></span> 
                        <span class="wa d-none">Whatsapp number<span class="text-danger">*</span>
                        <div><small>Make sure you phone has connected with us</small></div></span>
                    </label>
                    <input type="text" class="form-control form-control-lg" name="contact" />
                </div> 
                
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
    $(document).ready(function(){
        options_sel();
    });

    function options_sel()
    {
        $("select[name='method']").change(function(){
            var val = $(this).val();
           
            if(val == 1)
            {
                $(".wa").addClass('d-none');
                $(".em").removeClass('d-none');
            }
            else{
                $(".em").addClass('d-none');
                $(".wa").removeClass('d-none');
            }
        });
    }
</script>

@endsection