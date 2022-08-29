@if(!is_null($phone))
<table class="table table-striped mt-4 mb-0">
    <thead>
        <tr>
            <th>{{ Lang::get('table.wa') }}</th>
            <th>{{ Lang::get('table.status') }}</th>
            <!-- <th>{{ Lang::get('table.quota') }}</th> -->
            <th>{{ Lang::get('table.del') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $phone->number }}</td>
            <td><span class="text-success">@if($phone->status == 1 || $phone->status == 3) <span class="text-success">{{ Lang::get('table.connected') }}</span></span> @else <span class="text-danger">{{ Lang::get('table.disconnected') }}</span></span> @endif</span></td>
            <!-- <td><span class="text-info counter"> Auth::user()->counter_send_message_daily </span></td> -->
            <td><a role="button" id="{{ $phone->id }}" class="del text-danger"><i class="far fa-trash-alt"></i></a></td>
        </tr>
    </tbody>
</table>
@endif
