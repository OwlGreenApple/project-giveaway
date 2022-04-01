@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">{{ Lang::get('title.broadcast') }}</h1>
        </div>

        <div class="col-12 col-sm-12 col-md-8 col-lg-9 table-responsive">
            <div id="msg"><!-- --></div>
            <table class="table table-bordered mb-5">
                <thead>
                    @if(isset($msg))
                        @if($msg->count() > 0)
                            <tr class="table-success">
                                <th scope="col">{{ Lang::get('table.recipient') }}</th>
                                <th scope="col">{{ Lang::get('table.message') }}</th>
                                <th scope="col">{{ Lang::get('table.cd') }}</th>
                                <th scope="col">{{ Lang::get('table.act') }}</th>
                            </tr>
                        @endif
                    @else
                        <tr class="table-success">
                            <th scope="col">{{ Lang::get('table.title') }}</th>
                            <th scope="col">{{ Lang::get('table.message') }}</th>
                            <th scope="col">{{ Lang::get('table.date.send') }}</th>
                            <th scope="col">{{ Lang::get('table.act') }}</th>
                        </tr>
                    @endif
                </thead>

                <tbody>
                    @if(isset($msg))
                        @if($msg->count() > 0)
                            @foreach($msg as $row)
                            <tr>
                                <td>{{ $row->receiver }}</td>
                                <td>{{ $row->message }}</td>
                                <td>{{ $row->created_at }}</td>
                                <td>
                                    @if($row->status == 0)
                                        <button type="button" class="btn btn-danger btn-sm text-white btn-delete" msg="1" data-id="{{$row->id}}">{{ Lang::get('table.del') }}</button>
                                    @else
                                        <span class="@if($row->status == 1) text-primary @elseif($row->status == 2) text-success @elseif($row->status == 3) text-info @else text-danger @endif">{{ $row->status_msg }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    @else
                        @foreach($broadcasts as $broadcast)
                        <tr>
                            <td>{{ $broadcast->title }}</td>
                            <td>{{ $broadcast->message }}</td>
                            <td>{{ $broadcast->date_send }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm text-white btn-view" data-id="{{$broadcast->id}}">{{ Lang::get('table.message') }}</button>
                                <button type="button" class="btn btn-warning btn-sm text-white btn-edit" data-id="{{$broadcast->id}}">{{ Lang::get('table.edit') }}</button>
                                <button type="button" class="btn btn-danger btn-sm text-white btn-delete" data-id="{{$broadcast->id}}">{{ Lang::get('table.del') }}</button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
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
    messagesBroadcast();
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

function messagesBroadcast()
{
    $(document).on('click','.btn-view',function(e) {
        id = $(this).attr('data-id');
        window.location.href = "{{ url('message-broadcast') }}/"+id;
    });
}

function deleteBroadcast(){
    $(document).on('click','.btn-delete',function(e) {
        var url,red;
        var cf = confirm('{{ Lang::get("custom.del") }}');

        if(cf == false)
        {
            return false;
        }

        var id = $(this).attr('data-id');
        var msg = $(this).attr('msg');
        var bc_id = "@if(isset($bc_id)) {{ $bc_id }} @endif"

        if(msg == undefined)
        {
            url = "{{ url('delete-broadcast') }}";
            red = "{{ url('broadcast') }}"
        }
        else
        {
            url = "{{ url('delete-message') }}";
            red = "{{ url('message-broadcast') }}/"+bc_id;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method:'POST',
            url : url,
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
                    location.href=red;
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
