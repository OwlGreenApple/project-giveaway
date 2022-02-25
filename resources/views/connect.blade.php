@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Connect WA</h1>
        </div>

        <!-- FORM -->
        <div class="col-md-8">
            <form id="connect">
                <div class="card px-5 py-5">
                    <div class="card-body p-0">
                    <div class="iti-wrapper">
                        <div class="input-group">
                            <input type="text" id="phone" name="phone" class="form-control form-control-lg" required/>
                            <span class="error phone"></span>
                            <button type="submit" class="btn bg-custom btn-lg text-white">Enter</button>
                        </div>
                    </div>

                    <table class="table table-striped mt-4 mb-0">
                        <thead>
                            <tr>
                                <th>WA Number</th>
                                <th>Status</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>62811111</td>
                                <td><span class="text-success">Connected</span></td>
                                <td><span class="text-danger"><i class="far fa-trash-alt"></i></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <!--  -->
                    </div>
                </div>
            </form>
        <!-- end col -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        connect();
    });

    function connect()
    {
        $("#connect").submit(function(e){
            e.preventDefault();
            var data = $(this).serializeArray();
            data.push({name : 'code', value : $(".iti__selected-flag").attr('data-code') });

            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method : 'POST',
                data : data,
                url : '{{ url("connect") }}',
                dataType : 'json',
                beforeSend : function()
                {
                    $('#loader').show();
                    $('.div-loading').addClass('background-load');
                },
                success : function(result)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                },
                error: function(xhr){
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        })
    }
</script>

@endsection