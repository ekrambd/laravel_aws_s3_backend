<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }

    public function Dashboard()
    {   
        $data = DB::table('buckets')
                    ->selectRaw('
                        (SELECT COUNT(*) FROM buckets) as totalBuckets,
                        (SELECT COUNT(*) FROM folders) as totalFolders,
                        (SELECT COUNT(*) FROM files) as totalFiles,
                        (SELECT COUNT(*) FROM files WHERE upload_status = ?) as pendingFiles
                    ', ['Pending'])
                    ->first();
        return view('layouts.app', compact('data'));
    }
}
