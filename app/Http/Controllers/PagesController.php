<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(){

        $title = 'Welcome To Laravel';
        return view('pages.home')->with('title', $title);
    }

    public function about(){

        $title = 'About';
        return view('pages.about')->with('title', $title);
    }

    public function services(){
        $data = array(
            'title' => 'Services',
            'services' => ['Programming', 'Web Design', 'Networking']
        );

        return view('pages.services')->with($data);
    }
 
}
