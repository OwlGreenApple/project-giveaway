<table id="contestant" class="cell-border responsive fs-6 w-100" border="0">
    <thead>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Membership</th>
        <th>Total Giveaway</th>
        <th>Total Peserta</th>
        <th>Akhir Membership</th>
        <th>Tanggal Bergabung</th>
        <th>Status</th>
    </thead>
    <tbody>
    @if(count($users) > 0)
        @foreach($users as $row)
            <tr>
                <td class="align-middle"><span class="main-color">{{ $no++ }}</span></td>
                <td class="align-middle">{{ $row['name'] }}</td>
                <td class="align-middle">{{ $row['email'] }}</td>
                <td class="align-middle">{{ $row['membership'] }}</td>
                <td class="align-middle">{{ $row['total_giveaway'] }}</td>
                <td class="align-middle">{{ $row['total_ct'] }}</td>
                <td class="align-middle">{{ $row['end_membership'] }}</td>
                <td class="align-middle">{{ $row['created_at'] }}</td> 
                <td class="align-middle">@if($row['status'] > 0) <button id="{{ $row['id'] }}" type="button" class="btn btn-danger ban btn-sm">Ban</button> @else <span class="text-danger">Banned</span> @endif</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>