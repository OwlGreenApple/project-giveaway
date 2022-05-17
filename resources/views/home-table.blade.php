<table id="dashboard_table" class="display responsive nowrap" style="width:100%">
    <thead>
        <th>{{ Lang::get('table.title') }}</th>
        <th>{{ Lang::get('table.contestant') }}</th>
        <th>{{ Lang::get('table.entry') }}</th>
        <th>{{ Lang::get('table.status') }}</th>
        <th>{{ Lang::get('table.act') }}</th>
    </thead>
    <tbody>
        @if($data->count() > 0)
            @foreach($data as $row)
            <tr>
                <td class="align-middle"><a href="{{ url('edit-event') }}/{{ $row->id }}" target="_blank" rel="noopener noreferer" class="main-color">{{ $row->title }}</a><br/>{{ $row->award }}</td>
                <td class="align-middle"><a class="main-color" target="_blank" href="{{ url('list-contestants') }}/{{ $row->id }}">{{ $row->total_contestant }}</a></td>
                <td class="align-middle">{{ $row->total_entries }}</td>
                <td class="align-middle">
                    @if($row->status == 1)
                        @if($carbon::now($row->timezone)->gte($carbon::parse($row->start)->toDateTimeString())) 
                            <span class="badge rounded-pill bg-custom">{{ Lang::get('table.run') }}</span>
                        @else 
                            <span class="badge rounded-pill bg-danger">{{ Lang::get('table.start.not') }}</span> 
                        @endif
                    @elseif($row->status == 2) 
                        <a href="{{ url('contestant-winner') }}/{{ $row->id }}" class="btn btn-success award">{{ Lang::get('table.award') }}</a> 
                    @else 
                        <span class="badge rounded-pill bg-secondary">{{ Lang::get('table.award.done') }}</span> 
                    @endif
                </td>
                <td class="align-middle">
                    <div class="input-group">
                        <a href="{{ url('promo') }}/{{ $row->url_link }}" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-bullhorn"></i>&nbsp;{{ Lang::get('custom.promo') }}</a>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span role="button" class="visually-hidden">{{ Lang::get('custom.dropdown') }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-secondary" href="{{ url('edit-event') }}/{{ $row->id }}"><i class="far fa-edit"></i>&nbsp;{{ Lang::get('custom.edit') }}</a></li>
                            <li><a href="{{ url('c') }}/{{ $row->url_link }}" target="_blank" class="dropdown-item text-secondary"><i class="far fa-eye"></i>&nbsp;{{ Lang::get('custom.view') }}</a></li>
                            <li><a href="{{ url('message-list') }}/{{ $row->id }}" target="_blank" class="dropdown-item text-secondary"><i class="fas fa-envelope"></i>&nbsp;{{ Lang::get('custom.message') }}</a></li>
                            <li><a id="{{ $row->id }}" class="dropdown-item text-secondary duplicate"><i class="far fa-clone"></i>&nbsp;{{ Lang::get('custom.duplicate.btn') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a id="{{ $row->id }}" class="dropdown-item text-danger del_ev"><i class="far fa-trash-alt"></i>&nbsp;{{ Lang::get('table.del') }}</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
