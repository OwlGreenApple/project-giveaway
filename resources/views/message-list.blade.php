@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center px-5">
        <div class="col-md-12 mb-3">
            <h1 class="big-theme" align="center">Message Status</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="message" class="table table-striped mt-4 mb-0">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Date Created</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($data->count() > 0)
                            @foreach($data as $phone)
                            <tr>
                                <td>{{ $phone->receiver }}</td>
                                <td>{{ $phone->created_at }}</td>
                                <td>
                                    @if($phone->status == 1) <span class="text-primary">Sent</span>
                                    @elseif($phone->status == 2) <span class="text-success">Delivered</span>
                                    @elseif($phone->status == 3) <span class="text-info">Read</span>
                                    @elseif($phone->status == 4) <span class="text-danger">Failed</span>
                                    @else <span class="text-dark">Queue</span></span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                {{-- end card body --}}
            </div>
        </div>
        {{-- end --}}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        datatable();
    });

    function datatable()
    {
        $("#message").dataTable();
    }

</script>

@endsection
