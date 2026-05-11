<?php

namespace App\Http\Controllers;

use App\Models\Photos;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //

    public function index(){
        $photos=Photos::orderBy('id','desc')->paginate(50);

        return view('home',[
            'photos'=>$photos
        ]);
    }
}
