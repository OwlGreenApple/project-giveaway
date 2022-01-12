<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Banners;
use App\Models\User;
use App\Models\Events;

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
        $banners = $preloaded = null;
        return view('create',['data'=>$banners,'preloaded'=>$preloaded]);
    }

    public function edit_event()
    {
        $banners = Banners::where('event_id',1)->get();
        $data = $preloaded = null;


        if($banners->count() > 0)
        {
            foreach($banners as $row)
            {
                $data[$row->id] = Storage::disk('local')->get($row->url);
            }
            $preloaded = 'preloaded'; //keyname of jquery image-upload
        }

        dd($data);
        return view('create',['data'=>$data,'preloaded'=>$preloaded]);
    }

    public function save_events(Request $request)
    {
        // dd($request->all());

        $images = $request->file('images');
        $preload = $request->file('preload');

        foreach($images as $index=>$file):
            $newfile = 'banner/'.Date('Y-m-d-h-i-s-').$index.".jpg";
            Storage::disk('local')->put($newfile,file_get_contents($file));
            $banners = new Banners;
            $banners->event_id = 1;
            $banners->url = $newfile;
            $banners->save();
        endforeach;
        
        //Storage::disk('s3')->delete($filename);
        //Storage::disk('s3')->put($dir."/".$filename,$imageUpload, 'public');
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
