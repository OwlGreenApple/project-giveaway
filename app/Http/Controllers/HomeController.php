<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Banners;
use App\Models\Bonus;
use App\Models\User;
use App\Models\Events;
use Illuminate\Database\QueryException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function create_giveaway()
    {
        $banners = $bonuses = array();
        $preloaded = null;
        return view('create',['data'=>$banners,'preloaded'=>$preloaded, 'bonus'=>$bonuses]);
    }

    public function edit_event()
    {
        $banners = Banners::where('event_id',1)->get();
        $bonuses = Bonus::where('event_id',1)->get()->toArray();
        $data = $preloaded = null;


        if($banners->count() > 0)
        {
            foreach($banners as $row)
            {
                $data[$row->id] = asset('storage/app/'.$row->url);
            }
            $preloaded = 'preloaded'; //keyname of jquery image-upload
        }

        //dd($data);
        return view('create',['data'=>$data,'preloaded'=>$preloaded, 'bonus'=>$bonuses]);
    }

    public function save_events(Request $request)
    {
        // dd($request->all());
        $images = $request->file('images');
        $preload = $request->preloaded;
        $lists = $request->list;

        if($lists !== null)
        {
            self::delete_banner($lists,$preload);
        }

        if(isset($images)):
            foreach($images as $index=>$file):
                $newfile = 'banner/'.Date('Y-m-d-h-i-s-').$index.".jpg";
                Storage::disk('local')->put($newfile,file_get_contents($file));
                $banners = new Banners;
                $banners->event_id = 1;
                $banners->url = $newfile;
                $banners->save();
            endforeach;
        endif;
        
        //Storage::disk('s3')->delete($filename);
        //Storage::disk('s3')->put($dir."/".$filename,$imageUpload, 'public');
    }

    public static function delete_banner($lists,$preloaded)
    {
        $del = array();

        if($preloaded == null)
        {
            $preloaded = array();
        }

        $count_delete = array_diff($lists,$preloaded);

        if(count($count_delete) > 0)
        {
            foreach($count_delete as $banner_id):
                $banners = Banners::find($banner_id);
                $url_image = $banners->url;
                $del[] = $url_image;
            endforeach;

            try
            {
                Banners::whereIn('id',$count_delete)->delete();
            }
            catch(QueryException $e)
            {
                // print($e->getMessage());
            }

            Storage::disk('local')->delete($del);
        }
    }

    public function accounts()
    {
        return view('account');
    }

    public function contact()
    {
        return view('contact');
    }

    public function connect_wa()
    {
        return view('connect');
    }

/* end of class */
}
