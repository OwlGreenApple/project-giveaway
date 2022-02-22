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
                <td class="align-middle"><a class="main-color" target="_blank" href="{{ url('list-contestants') }}/{{ $row->id }}">{{ $row->total_contestant }}</a></td>
                <td class="align-middle">{{ $row->total_entries }}</td>
                <td class="align-middle">@if($row->status == 1) <span class="badge rounded-pill bg-custom">Running</span> @elseif($row->status == 2) <a href="{{ url('contestant-winner') }}/{{ $row->id }}" class="btn btn-success award">Award</a> @else <span class="badge rounded-pill bg-secondary">Awarded</span> @endif</td>
                <td class="align-middle">
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary">Broadcast</button>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ url('edit-event') }}/{{ $row->id }}">Edit</a></li>
                            <li><a id="{{ $row->id }}" class="dropdown-item duplicate">Duplicate</a></li>
                            <li><a href="{{ url('c') }}/{{ $row->url_link }}" target="_blank" class="dropdown-item">View</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a id="{{ $row->id }}" class="dropdown-item text-danger del_ev">Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>