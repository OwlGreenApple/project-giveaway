{{-- display contestants according on name --}}
@if($data->count() > 0)
<div class="mt-2">
    @foreach ($data as $row)
        <div role="button" class="contestant_data mb-1 border-bottom p-1" data_phone="{{$row->wa_number}}" data_name="{{$row->c_name}}" data_id="{{$row->id}}">{{$row->c_name}} -- {{$row->wa_number}}</div>
    @endforeach
</div>
@endif
