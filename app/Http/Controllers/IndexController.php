<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function indexPage()
    {
        return view('admin_login');
    }
}
