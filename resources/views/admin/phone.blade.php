<table id="phone_admin" class="cell-border" border="0">
    <thead>
        <th>NO</th>
        <th>WA</th>
        <th>API KEY</th>
        <th>SERVICE</th>
        <th>WABLAS SERVER</th>
        <th>TANGGAL DAFTAR</th>
        <th>ACT</th>
    </thead> 
    <tbody>
    @if($data->count() > 0)
        @foreach($data as $row)
            <tr>
                <td class="align-middle"><span class="main-color">{{ $no++ }}</span></td>
                <td class="align-middle">{{ $row->number }}</td>
                <td class="align-middle">{{ $row->device_key }}</td> 
                <td class="align-middle">@if($row->service_id == 1) WABLAS @else WAFONNTE @endif</td> 
                <td class="align-middle">@if($row->service_id == 1){{ $ct::get_wablas()[$row->device_id] }} @else - @endif</td>
                <td class="align-middle">{{ $row->created_at }}</td>
                <td class="align-middle"><a role="button" data-id="{{ $row->id }}" data-phone="{{ $row->number }}" data-api="{{ $row->device_key }}" data-service="{{ $row->service_id }}" data-wablas="{{ $row->device_id }}" class="btn btn-primary btn-sm me-1 edit">Ubah</a><a data-id="{{ $row->id }}" role="button" class="btn btn-danger btn-sm del">Hapus</a></td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>