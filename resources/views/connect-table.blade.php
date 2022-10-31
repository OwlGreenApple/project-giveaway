@if($phone->count() > 0)
<table class="table table-striped mt-4 mb-0">
    <thead>
        <tr>
            <th>{{ Lang::get('table.wa') }}</th>
            <th>{{ Lang::get('table.status') }}</th>
            <th>{{ Lang::get('table.pair') }}</th>
            <!-- <th>{{ Lang::get('table.quota') }}</th> -->
            <th>{{ Lang::get('table.expire') }}</th>
            @if($user->is_admin == 1)<th>{{ Lang::get('table.del') }}</th>@endif
        </tr>
    </thead>
    <tbody>
        @foreach($phone as $col)
        <tr>
            <td>{{ $col->number }}</td> 
            <td>@if(($col->status == 1 || $col->status == 3) && $col->service_id == 0) <span class="badge bg-info">{{ Lang::get('table.connected') }}</span> @elseif($col->service_id > 0) <span class="badge bg-success"> WAfonnte </span> @else <span class="badge bg-warning text-dark">{{ Lang::get('table.disconnected') }}</span></span> @endif</span></td>
            <td>
            @if($col->status == 1 || $col->status == 3 || $col->service_id > 0 || $user->status == 3 )
                -
            @else
                <span class="text-info"><a class="btn btn-outline-primary btn-sm scanqr" role="button" href="{{ url('qrconnect') }}/{{ $col->id }}"><i class="fas fa-qrcode"></i>&nbsp;{{ Lang::get('table.pair.scan') }}</a></span>
            @endif
            </td>
            <!-- <td><span class="text-info counter"> Auth::user()->counter_send_message_daily </span></td> -->
            <td><small>{{ Date('d-m-Y H:i:s',strtotime($user->end_membership)) }}</small></td>
            @if($user->is_admin == 1)
            <td>
                @if($col->service_id == 0)
                    <a role="button" id="del-{{ $col->id }}" class="del btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>
                @endif
            </td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
@endif
