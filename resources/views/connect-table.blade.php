@if($phone->count() > 0)
<table class="table table-striped mt-4 mb-0">
    <thead>
        <tr>
            <th>{{ Lang::get('table.wa') }}</th>
            <th>{{ Lang::get('table.status') }}</th>
            <th>{{ Lang::get('table.pair') }}</th>
            <!-- <th>{{ Lang::get('table.quota') }}</th> -->
            <th>{{ Lang::get('table.del') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($phone as $col)
        <tr>
            <td>{{ $col->number }}</td>
            <td><span class="text-success">@if($col->status == 1 || $col->status == 3) <span class="text-success">{{ Lang::get('table.connected') }}</span></span> @else <span class="text-danger">{{ Lang::get('table.disconnected') }}</span></span> @endif</span></td>
            <td>
            @if($col->status == 1 || $col->status == 3)
                -
            @else
                <span class="text-info"><a class="btn btn-outline-success btn-sm scanqr" role="button" href="{{ url('qrconnect') }}/{{ $col->id }}"><i class="fas fa-qrcode"></i>&nbsp;{{ Lang::get('table.pair.scan') }}</a></span>
            @endif
            </td>
            <!-- <td><span class="text-info counter"> Auth::user()->counter_send_message_daily </span></td> -->
            <td><a role="button" id="del-{{ $col->id }}" class="del text-danger"><i class="far fa-trash-alt"></i></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
