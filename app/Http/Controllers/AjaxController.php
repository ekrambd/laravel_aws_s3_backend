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
}
