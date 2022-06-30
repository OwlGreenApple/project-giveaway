<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use App\Rules\CheckDate;
use App\Rules\CheckNumber;
use App\Rules\CheckDescription;
use App\Rules\CheckMessage;
use App\Rules\ProfileRules;
use App\Rules\CheckValidPhone;
use App\Models\Events;
use App\Helpers\Custom;

class CheckEvents
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public static function total_events($membership)
    {
        $package = new Custom;
        $cond = [['user_id',Auth::id()],['status',1]];

        if($membership == 'starter' || $membership =='starter-3-month' || $membership == 'starter-yearly')
        {
            $limit = $package->get_price()[1]['campaign'];
        }
        elseif($membership == 'gold' || $membership =='gold-3-month' || $membership == 'gold-yearly')
        {
            $limit = $package->get_price()[4]['campaign'];
        }
        elseif($membership == 'platinum' || $membership =='platinum-3-month' || $membership == 'platinum-yearly')
        {
            $limit = $package->get_price()[7]['campaign']; 
        }
        else
        {
            // $cond = [['user_id',Auth::id()]];
            $limit = $package->get_price()[0]['campaign'];
        }

        $ev = Events::where($cond)->get()->count();

        if($ev >= $limit)
        {
            $err = [
                'success'=>'err_package',
                'package'=>Lang::get('custom.membership')
            ];
            return $err;
        }

        return array();
    }

    public function handle(Request $request, Closure $next)
    {
        $evt = Events::where([['id',$request->edit],['user_id',Auth::id()]])->select('status')->first();

        if(($evt == null || $evt->status >= 2) && $request->edit!==null )
        {
            return response()->json(['success'=>'err_end','message'=>Lang::get('custom.ev.end')]);
        }

        $ev = self::total_events(Auth::user()->membership);

        if(count($ev) > 0 && $request->edit == null)
        {
            return response()->json($ev);
        }

        $rules = [
            'title'=>['required','max:40'],
            'start'=>['required',new CheckDate('start',null,$request->timezone,$request->edit)],
            'end'=>['required',new CheckDate('end',$request->start)],
            'award'=>['required',new CheckDate('award',$request->end)],
            'winner'=>['required','numeric','min:1','max:50'],
            'timezone'=>['required', new CheckDate('timezone',null)],
            'owner_name'=>['required','max:45'],
            'owner_url'=>['required','url'],
            'prize_name'=>['required','max:100'],
            'currency'=>['required',new ProfileRules('cur')],
            'prize_amount'=>['required',new CheckNumber(null)],
            'desc'=>[new CheckDescription],
            'message'=>['required','max:65000',new CheckMessage],
            'media'=>['bail','mimes:jpeg,jpg,png','max:1024'],
            'message_winner'=>['required','max:65000',new CheckMessage],
        ];

        if($request->edit == null)
        {
            $rules['phone']=['required','min:6', new CheckValidPhone($request->pcode)];
        }
        else
        {
            if($request->phone !== null)
            {   
                $rules['phone']=['min:6', new CheckValidPhone($request->pcode)];
            }
        }

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
                'title'=> $err->first('title'),
                'start'=>$err->first('start'),
                'end'=>$err->first('end'),
                'award'=>$err->first('award'),
                'winner'=>$err->first('winner'),
                'timezone'=>$err->first('timezone'),
                'owner_name'=>$err->first('owner_name'),
                'owner_url'=>$err->first('owner_url'),
                'prize_name'=>$err->first('prize_name'),
                'prize_amount'=>$err->first('prize_amount'),
                'images'=>$err->first('images'),
                'youtube_url'=>$err->first('youtube_url'),
                'desc'=>$err->first('desc'),
                'message'=>$err->first('message'),
                'media'=>$err->first('media'),
                'message_winner'=>$err->first('message_winner'),
                'currency'=>$err->first('currency'),
                'phone'=>$err->first('phone'),
            ];

            // return response()->json($errors);
        }

        $req = $request->all();

        //BONUS ENTRIES
        //CREATE
        if(isset($req['new_text_fb']))
        {
           $type = 'fb';
           $protocol = 'new';
           $err_fb = $this->filter_validator($req,$type,$protocol);
           
            if(count($err_fb) > 0)
            {
                $errors['success'] = 'err';
                foreach($err_fb as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
                endforeach;
            }
        }

        if(isset($req['new_text_ig']))
        {
           $type = 'ig';
           $protocol = 'new';
           $err_ig = $this->filter_validator($req,$type,$protocol);

           if(count($err_ig) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_ig as $key=> $msg_err):
                   $key = str_replace(".","_",$key);
                   $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['new_text_tw']))
        {
           $type = 'tw';
           $protocol = 'new';
           $err_tw = $this->filter_validator($req,$type,$protocol);

           if(count($err_tw) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_tw as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['new_text_yt']))
        {
           $type = 'yt';
           $protocol = 'new';
           $err_yt = $this->filter_validator($req,$type,$protocol);

           if(count($err_yt) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_yt as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['new_text_pt']))
        {
            $type = 'pt';
           $protocol = 'new';
           $err_pt = $this->filter_validator($req,$type,$protocol);

           if(count($err_pt) > 0)
           {
               $errors['success'] = 'err';
                foreach($err_pt as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
                endforeach;
           }
        }

        if(isset($req['new_text_de']))
        {
            $type = 'de';
            $protocol = 'new';
           $err_de = $this->filter_validator($req,$type,$protocol);

           if(count($err_de) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_de as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['new_text_cl']))
        {
            $type = 'cl';
            $protocol = 'new';
           $err_cl = $this->filter_validator($req,$type,$protocol);

           if(count($err_cl) > 0)
           {
               $errors['success'] = 'err';
                foreach($err_cl as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
                endforeach;
           }
        }

        if(isset($req['new_text_wyt']))
        {
            $type = 'wyt';
            $protocol = 'new';
           $err_wyt = $this->filter_validator($req,$type,$protocol);
           if(count($err_wyt) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_wyt as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        //EDIT

        if(isset($req['edit_text_fb']))
        {
            $type = 'fb';
            $protocol = 'edit';
            $err_fb = $this->filter_validator($req,$type,$protocol);
 
            if(count($err_fb) > 0)
            {
                $errors['success'] = 'err';
                foreach($err_fb as $key=> $msg_err):
                     $key = str_replace(".","_",$key);
                     $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
                endforeach;
            }
        } 

        if(isset($req['edit_text_ig']))
        {
            $type = 'ig';
            $protocol = 'edit';
           $err_ig = $this->filter_validator($req,$type,$protocol);

           if(count($err_ig) > 0)
           {
                $errors['success'] = 'err';
                foreach($err_ig as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
                endforeach;
           }
        }

        if(isset($req['edit_text_tw']))
        {
           $type = 'tw';
           $protocol = 'edit';
           $err_tw = $this->filter_validator($req,$type,$protocol);

           if(count($err_tw) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_tw as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['edit_text_yt']))
        {
           $type = 'yt';
           $protocol = 'edit';
           $err_yt = $this->filter_validator($req,$type,$protocol);

           if(count($err_yt) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_yt as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['edit_text_pt']))
        {  
           $type = 'pt';
           $protocol = 'edit';
           $err_pt = $this->filter_validator($req,$type,$protocol);

           if(count($err_pt) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_pt as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['new_text_de']))
        {
           $type = 'de';
           $protocol = 'edit';
           $err_de = $this->filter_validator($req,$type,$protocol);

           if(count($err_de) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_de as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['edit_text_cl']))
        {
           $type = 'cl';
           $protocol = 'edit';
           $err_cl = $this->filter_validator($req,$type,$protocol);

           if(count($err_cl) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_cl as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
           }
        }

        if(isset($req['new_text_wyt']))
        {
           $type = 'wyt';
           $protocol = 'edit';
           $err_wyt = $this->filter_validator($req,$type,$protocol);

           if(count($err_wyt) > 0)
           {
               $errors['success'] = 'err';
               foreach($err_wyt as $key=> $msg_err):
                    $key = str_replace(".","_",$key);
                    $errors[$key] = self::fix_lang($protocol,$type,$msg_err);
               endforeach;
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
            $protocol.'_entries_'.$init.'.*'=>['bail','required','numeric','min:1','max:100'],
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

        $messages = [
            'url' => Lang::get("cvalidation.invalid.url"),
            'numeric' => Lang::get("cvalidation.invalid.numeric"),
        ];

        $validator = Validator::make($req,$rules,$messages);

        if($validator->fails() == true)
        {
            return $validator->errors()->toArray();
        }
        else
        {
            return array();
        }
    }

    private static function fix_lang($type,$ent,$err_wyt)
    {
        $replace = [$type.'_text_'.$ent,$type.'_url_'.$ent,$type.'_entries_'.$ent,"."];
        $target = [Lang::get('cvalidation.act_'.$ent),Lang::get('cvalidation.url_'.$ent)." ",Lang::get('cvalidation.ent_'.$ent)];
        return str_replace($replace,$target,$err_wyt);
    }

/**/
}
