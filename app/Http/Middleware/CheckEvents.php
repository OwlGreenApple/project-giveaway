<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use App\Rules\CheckDate;
use App\Rules\CheckNumber;
use App\Rules\CheckDescription;

class CheckEvents
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        $rules = [
            'title'=>['required','max:45'],
            'start'=>['required'],
            'end'=>['required',new CheckDate('end',$request->start)],
            'award'=>['required',new CheckDate('award',$request->end)],
            'winner'=>['required','numeric','min:1','max:100'],
            'timezone'=>['required', new CheckDate('timezone',null)],
            'owner_name'=>['required','max:45'],
            'owner_url'=>['required','url'],
            'prize_name'=>['required','max:100'],
            'prize_amount'=>['required',new CheckNumber(null)],
            'desc'=>[new CheckDescription],
        ];

        if($request->media_option !== null)
        {
            $rules['youtube_url'] = ['required','url'];
        }

        if($request->images == null && $request->media_option == null && $request->preloaded == null)
        {
            $rules['images'] = ['required'];
        }

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();
        $errors = array();

        if($validator->fails() == true)
        {
            $errors = [
                'success'=>'err',
                0=>[$err->first('title'),'title'],
                1=>[$err->first('start'),'start'],
                2=>[$err->first('end'),'end'],
                3=>[$err->first('award'),'award'],
                4=>[$err->first('winner'),'winner'],
                5=>[$err->first('timezone'),'timezone'],
                6=>[$err->first('owner_name'),'owner_name'],
                7=>[$err->first('owner_url'),'owner_url'],
                8=>[$err->first('prize_name'),'prize_name'],
                9=>[$err->first('prize_amount'),'prize_amount'],
                10=>[$err->first('images'),'images'],
                11=>[$err->first('youtube_url'),'youtube_url'],
                12=>[$err->first('desc'),'desc'],
            ];

            // return response()->json($errors);
        }

        $req = $request->all();

        //BONUS ENTRIES
        //CREATE
        if(isset($req['new_text_fb']))
        {
           $err_fb = $this->filter_validator($req,'fb','new');
           if(count($err_fb) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_fb'] = self::fix_lang('new','fb',$err_fb);
           }
        }

        if(isset($req['new_text_ig']))
        {
           $err_ig = $this->filter_validator($req,'ig','new');
           if(count($err_ig) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_ig'] = self::fix_lang('new','ig',$err_ig);
           }
        }

        if(isset($req['new_text_tw']))
        {
           $err_tw = $this->filter_validator($req,'tw','new');
           if(count($err_tw) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_tw'] = self::fix_lang('new','tw',$err_tw);
           }
        }

        if(isset($req['new_text_yt']))
        {
           $err_yt = $this->filter_validator($req,'yt','new');
           if(count($err_yt) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_yt'] = self::fix_lang('new','yt',$err_yt);
           }
        }
        
        if(isset($req['new_text_pt']))
        {
           $err_pt = $this->filter_validator($req,'pt','new');
           if(count($err_pt) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_pt'] = self::fix_lang('new','pt',$err_pt);
           }
        }

        if(isset($req['new_text_de']))
        {
           $err_de = $this->filter_validator($req,'de','new');
           if(count($err_de) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_de'] = self::fix_lang('new','de',$err_de);
           }
        }

        if(isset($req['new_text_cl']))
        {
           $err_cl = $this->filter_validator($req,'cl','new');
           if(count($err_cl) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_cl'] = self::fix_lang('new','cl',$err_cl);
           }
        }

        if(isset($req['new_text_wyt']))
        {
           $err_wyt = $this->filter_validator($req,'wyt','new');
           if(count($err_wyt) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_cl'] = self::fix_lang('new','wyt',$err_wyt);
           }
        }

        //EDIT

        if(isset($req['edit_text_fb']))
        {
           $err_fb = $this->filter_validator($req,'fb','edit');
           if(count($err_fb) > 0)
           {
                $errors['success'] = 'err';
                $errors['err_edit_fb'] = self::fix_lang('edit','fb',$err_fb);
           }
        }

        if(isset($req['edit_text_ig']))
        {
           $err_ig = $this->filter_validator($req,'ig','edit');
           if(count($err_ig) > 0)
           {
                $errors['success'] = 'err';
                $errors['err_edit_ig'] = self::fix_lang('edit','ig',$err_ig);
           }
        }

        if(isset($req['edit_text_tw']))
        {
           $err_tw = $this->filter_validator($req,'tw','edit');
           if(count($err_tw) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_edit_tw'] = self::fix_lang('edit','tw',$err_tw);
           }
        }

        if(isset($req['edit_text_yt']))
        {
           $err_yt = $this->filter_validator($req,'yt','edit');
           if(count($err_yt) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_edit_yt'] = self::fix_lang('edit','yt',$err_yt);
           }
        }
        
        if(isset($req['edit_text_pt']))
        {
           $err_pt = $this->filter_validator($req,'pt','edit');
           if(count($err_pt) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_edit_pt'] = self::fix_lang('edit','pt',$err_pt);
           }
        }

        if(isset($req['new_text_de']))
        {
           $err_de = $this->filter_validator($req,'de','edit');
           if(count($err_de) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_edit_de'] = self::fix_lang('edit','de',$err_de);
           }
        }

        if(isset($req['edit_text_cl']))
        {
           $err_cl = $this->filter_validator($req,'cl','edit');
           if(count($err_cl) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_edit_cl'] = self::fix_lang('edit','cl',$err_cl);
           }
        }

        if(isset($req['new_text_wyt']))
        {
           $err_wyt = $this->filter_validator($req,'wyt','edit');
           if(count($err_wyt) > 0)
           {
               $errors['success'] = 'err';
               $errors['err_edit_cl'] = self::fix_lang('edit','wyt',$err_wyt);
           }
        }

        //END BONUS ENTRIES

        if(count($errors) > 0)
        {
            return response()->json($errors);
        }

        return $next($request);
    }

    //EXTRACT MULTIPLE DATA
    public function filter_validator($req,$init,$protocol)
    {
        $rules = [
            $protocol.'_text_'.$init.'.*'=>['required','max:30'],
            $protocol.'_entries_'.$init.'.*'=>['required','min:1','max:100'],
        ];

        if($init !== 'de' || $init !== 'wyt')
        {
            $rules[$protocol.'_url_'.$init.'.*'] = ['required','url'];
        }

        if($init == 'wyt')
        {
            $rules[$protocol.'_url_'.$init.'.*'] = ['required','max:30'];
        }

        if($init == 'ig' || $init == 'tw')
        {
            $rules[$protocol.'_url_'.$init.'.*'] = ['required','max:50'];
        }

        $validator = Validator::make($req,$rules);
       
        if($validator->fails() == true)
        {
            return $validator->messages()->all();
        }
        else
        {
            return array();
        }
    }
    
    private static function fix_lang($type,$ent,$err_wyt)
    {
        $replace = [$type.'_text_'.$ent,$type.'_url_'.$ent,$type.'_entries_'.$ent,"."];
        $target = [Lang::get('cvalidation.act_'.$ent),Lang::get('cvalidation.url_'.$ent),Lang::get('cvalidation.ent_'.$ent)," : "];
        return str_replace($replace,$target,$err_wyt);
    }

/**/ 
}