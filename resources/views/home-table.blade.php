<table id="dashboard_table" class="table">
    <thead>
        <th>Titles</th>
        <th>Contestants</th>
        <th>Entries</th>
        <th>Status</th>
        <th>Actions</th>
    </thead>
    <tbody>
        @if($data->count() > 0)
            @foreach($data as $row)
            <tr>
                <td class="align-middle"><span class="main-color">{{ $row->title }}</span><br/>{{ $row->award }}</td>
                <td class="align-middle">{{ $row->total_contestant }}</td>
                <td class="align-middle">{{ $row->total_entries }}</td>
                <td class="align-middle">@if($row->status == 1) <span class="badge rounded-pill bg-custom">Running</span> @elseif($row->status == 2) <button type="button" class="btn btn-success award">Award</button> @else <span class="badge rounded-pill bg-secondary">Done</span> @endif</td>
                <td class="align-middle">
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary">Broadcast</button>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Edit</a></li>
                            <li><a class="dropdown-item">Duplicate</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger">Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            @endforeach
        @else
            <tr><td colspan="4" class="text-center"><div class="alert alert-info">{{ Lang::get('custom.no_data') }}</div></tr> 
        @endif
    </tbody>
</table>