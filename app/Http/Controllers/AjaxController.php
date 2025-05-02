<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Folder\FolderInterface;

class AjaxController extends Controller
{   

    protected $folder;

    public function __construct(
        FolderInterface $folder
    )
    {   
        $this->folder = $folder;
    }

    public function folderStatusUpdate(Request $request)
    {
        $statusUpdate = $this->folder->statusUpdate($request);
        return $statusUpdate;
    }

    public function bucketFolders(Request $request)
    {
        $folders = $this->folder->fetch()->where('bucket_id',$request->bucket_id)->where('status','Active')->get();
        return response()->json($folders);
    }
}
