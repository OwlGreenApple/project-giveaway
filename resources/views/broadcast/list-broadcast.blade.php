@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">List Broadcast</h1>
        </div>

        <div class="col-md-8">
            <div id="msg"><!-- --></div>
            <table class="table table-bordered mb-5">
                <thead>
                    <tr class="table-success">
                        <th scope="col">Title</th>
                        <th scope="col">Message</th>
                        <th scope="col">Date Send</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($broadcasts as $broadcast)
                    <tr>
                        <td>{{ $broadcast->title }}</td>
                        <td>{{ $broadcast->message }}</td>
                        <td>{{ $broadcast->date_send }}</td>
                        <td>{{ $broadcast->status }}</td>
                        <td>
                            <button type="button" class="btn btn-warning btn-lg text-white">Edit</button>
                            <button type="button" class="btn btn-danger btn-lg text-white">Delete</button>
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
    datetimepicker();
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

</script>
<script src="{{ asset('assets/js/counting.js') }}" type="text/javascript"></script>
@endsection
