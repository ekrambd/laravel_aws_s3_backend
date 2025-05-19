<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Folder\FolderInterface;
use App\Repositories\File\FileInterface;


class AjaxController extends Controller
{   

    protected $folder;
    protected $file;
    public function __construct(
        FolderInterface $folder,
        FileInterface $file
    )
    {   
        $this->folder = $folder;
        $this->file = $file;
    }

    public function folderStatusUpdate(Request $request)
    {
        $statusUpdate = $this->folder->statusUpdate($request);
        return $statusUpdate;
    }

    public function bucketFolders(Request $request)
    {
        $folders = $this->folder->fetch()->where('bucket_id',$request->bucket_id)->where('status','Active')->get();
        $bucket = bucket($request->bucket_id);
        return response()->json(['status'=>true, 'bucket'=>$bucket, 'folders'=>$folders]);
    }

    public function uploadFile(Request $request)
    {
        $uploadFile = $this->file->uploadFile($request);
        return $uploadFile;
    }
}
