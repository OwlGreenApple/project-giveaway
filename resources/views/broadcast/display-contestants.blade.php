{{-- display contestants according on name --}}
@if(count($data) > 0)
<div class="mt-2">
    @foreach ($data as $row)
        <div role="button" class="contestant_data mb-1 border-bottom p-1" data_phone="{{$row['wa']}}" data_name="{{ $row['name'] }}" data_id="{{$row['id']}}">{{$row['name']}} -- {{$row['wa']}}</div>
    @endforeach
</div>
@endif
