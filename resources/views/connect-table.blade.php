@if(!is_null($phone))
<table class="table table-striped mt-4 mb-0">
    <thead>
        <tr>
            <th>WA Number</th>
            <th>Status</th>
            <th>Quota</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $phone->number }}</td>
            <td><span class="text-success">@if($phone->status == 1) <span class="text-success">Connected</span></span> @else <span class="text-danger">Disconnected</span></span> @endif</span></td>
            <td><span class="text-info">{{ Auth::user()->counter_send_message_daily }}</span></td>
            <td><a id="{{ $phone->id }}" class="del text-danger"><i class="far fa-trash-alt"></i></a></td>
        </tr>
    </tbody>
</table>
@endif