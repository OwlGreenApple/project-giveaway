<ul class="list-group">
    @if($ev->tw == 1)
        <li class="list-group-item clearfix">{!! $helper::get_marks(0,$ev->id,8,$ct_id) !!}<a class="task" data-type="8">
            <i class="fab fa-twitter"></i>&nbsp;{{ Lang::get('custom.tw.share') }}</a> 
            <div class="float-end prize bg_share_8">
                {{ $helper::share_prize(null) }}&nbsp;{{ Lang::get('custom.per') }}</div>
            </div>
        </li>
    @endif
    @if($ev->fb == 1)
        <li class="list-group-item clearfix">{!! $helper::get_marks(0,$ev->id,9,$ct_id) !!}
            <a class="task" data-type="9"><i class="fab fa-facebook-f"></i>&nbsp;{{ Lang::get('custom.fb.share') }}</a>
            <div class="float-end prize bg_share_9">
                {{ $helper::share_prize(null) }}&nbsp;{{ Lang::get('custom.per') }}</div>
            </div>
        </li>
    @endif
    @if($ev->wa == 1)
        <li class="list-group-item clearfix">{!! $helper::get_marks(0,$ev->id,10,$ct_id) !!}<a class="task" data-type="10"><i class="fab fa-whatsapp"></i>&nbsp;{{ Lang::get('custom.wa.share') }}</a> 
            <div class="float-end prize bg_share_10">
                {{ $helper::share_prize(null) }}&nbsp;{{ Lang::get('custom.per') }}</div>
            </div>
        </li>
    @endif
    @if($ev->ln == 1)
        <li  class="list-group-item clearfix">{!! $helper::get_marks(0,$ev->id,11,$ct_id) !!}
            <a class="task" data-type="11"><i class="fab fa-linkedin-in"></i>&nbsp;{{ Lang::get('custom.ln.share') }}</a> 
            <div class="float-end prize bg_share_11">
                {{ $helper::share_prize(null) }}&nbsp;{{ Lang::get('custom.per') }}</div>
            </div>
        </li>
    @endif
    @if($ev->mail == 1)
        <li class="list-group-item clearfix">{!! $helper::get_marks(0,$ev->id,12,$ct_id) !!}
            <a class="task" data-type="12"><i class="fas fa-at"></i>&nbsp;{{ Lang::get('custom.email.share') }}</a>
            <div class="float-end prize bg_share_12">
                {{ $helper::share_prize(null) }}&nbsp;{{ Lang::get('custom.per') }}</div>
            </div>
        </li>
    @endif
    @if($bonus->count() > 0)
        @foreach($bonus as $row)
            @php 
                $icon = null;
                if($row->type==0)
                {
                    $icon = '<i class="fab fa-facebook"></i>'; 
                } 
                elseif($row->type==1)
                {
                    $icon = '<i class="fab fa-instagram"></i>'; 
                }
                elseif($row->type==2)
                {
                    $icon = '<i class="fab fa-twitter-square"></i>'; 
                }
                elseif($row->type==3)
                {
                    $icon = '<i class="fab fa-youtube-square"></i>'; 
                }
                elseif($row->type==4)
                {
                    $icon = '<i class="fas fa-podcast"></i>'; 
                }
                elseif($row->type==6)
                {
                    $icon = '<i class="fas fa-link"></i>'; 
                }
            @endphp

            @if($row->type == 5)
                <li class="list-group-item clearfix">{!! $helper::get_marks($row->id,$ev->id,5,$ct_id) !!}
                    <a data-type="{{ $row->type }}" class="task" data-id="{{ $row->id }}"><i class="far fa-calendar-check"></i>&nbsp;{{ $row->title }}</a>
                    <div class="float-end prize bg_bonus_{{ $row->id }}">
                        +{{ $row->prize }}</div>
                    </div>
                </li>
            @elseif($row->type == 7)
                <li class="list-group-item clearfix">{!! $helper::get_marks($row->id,$ev->id,7,$ct_id) !!}<a data-type="{{ $row->type }}" class="task" data-url="{{ $row->url }}" data-id="{{ $row->id }}" data-bs-toggle="collapse" href="#collapse_{{ $row->id }}"><i class="fab fa-youtube"></i>&nbsp;{{ $row->title }}</a> 
                    <div class="float-end prize bg_bonus_{{ $row->id }}">
                        +{{ $row->prize }}</div>
                    </div>
                </li>
                <div id="collapse_{{ $row->id }}" class="collapse">
                    <div class="yt-box mt-1">
                        <div class="embed-responsive embed-responsive-16by9">
                            <div class="embed-responsive-item yt-iframe" id="yt_ifr_{{ $row->id }}"></div>
                        </div>
                    </div>
                </div>    
            @else
                <li class="list-group-item clearfix">{!! $helper::get_marks($row->id,$ev->id,$row->type,$ct_id) !!}<a data-type="{{ $row->type }}" data-id="{{ $row->id }}" class="task" >{!! $icon !!}&nbsp;{{ $row->title }}</a> 
                    <div class="float-end prize bg_bonus_{{ $row->id }}">
                        +{{ $row->prize }}</div>
                    </div>
                </li>
            @endif
        @endforeach
    @endif
</ul>