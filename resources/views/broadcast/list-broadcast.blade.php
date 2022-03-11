@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">List Broadcast</h1>
        </div>

        <div class="col-12 col-sm-12 col-md-8 col-lg-9 table-responsive">
            <div id="msg"><!-- --></div>
            <table class="table table-bordered mb-5">
                <thead>
                    <tr class="table-success">
                        <th scope="col">Title</th>
                        <th scope="col">Message</th>
                        <th scope="col">Date Send</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($broadcasts as $broadcast)
                    <tr>
                        <td>{{ $broadcast->title }}</td>
                        <td>{{ $broadcast->message }}</td>
                        <td>{{ $broadcast->date_send }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm text-white btn-view" data-id="{{$broadcast->id}}">Messages</button>
                            <button type="button" class="btn btn-warning btn-sm text-white btn-edit" data-id="{{$broadcast->id}}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm text-white btn-delete" data-id="{{$broadcast->id}}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>        <!-- end col -->
        </div>

    </div>
</div>



<script>
$(function() {
    //datetimepicker();
    deleteBroadcast();
    editBroadcast();
});


function datetimepicker()
{
    var date, tdate;
    var ndate = new Date();
    var date_1 = $('.datetimepicker_1').val();

    (date_1.length == 0)?date = ndate : date = moment(date_1);


    var format_date = 'YYYY-MM-DD HH:mm';

    $('.datetimepicker_1').datetimepicker({
        format : format_date,
        minDate : date
    });

}

function editBroadcast(){
    $(document).on('click','.btn-edit',function(e) {
        id = $(this).attr('data-id');
        window.location.href = "<?php echo url('edit-broadcast').'/'; ?>"+id;
    });
}

function deleteBroadcast(){
    $(document).on('click','.btn-delete',function(e) {
        var cf = confirm('{{ Lang::get("custom.del") }}');

        if(cf == false)
        {
            return false;
        }

        id = $(this).attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method:'POST',
            url : "{{ url('delete-broadcast') }}",
            data : {
                'id':id
            },
            dataType : 'json',
            beforeSend: function()
            {
                $('#loader').show();
                $('.div-loading').addClass('background-load');
            },
            success : function(result)
            {
                if(result.success == 1)
                {
                    location.href="{{ url('broadcast') }}";
                }
                else if(result.success == 0)
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error.id") }}</div>')
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                    $("#msg").html('<div class="alert alert-danger">{{ Lang::get("custom.error") }}</div>')
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
<script src="{{ asset('assets/js/counting.js') }}" type="text/javascript"></script>
@endsection
