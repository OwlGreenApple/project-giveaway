<table id="contestant" class="cell-border" border="0">
    <thead>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Membership</th>
        <th>Akhir Membership</th>
        <th>Status</th>
    </thead>
    <tbody>
    @if($users->count() > 0)
        @foreach($users as $row)
            <tr>
                <td class="align-middle"><span class="main-color">{{ $no++ }}</span></td>
                <td class="align-middle">{{ $row->name }}</td>
                <td class="align-middle">{{ $row->email }}</td>
                <td class="align-middle">{{ $row->membership }}</td>
                <td class="align-middle">{{ $row->end_membership }}</td>
                <td class="align-middle">@if($row->status > 0) <button id="{{ $row->id }}" type="button" class="btn btn-danger ban">Ban</button> @else <span class="text-danger">Banned</span> @endif</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>