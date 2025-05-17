<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Folder\FolderInterface;
use App\Repositories\File\FileInterface;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Jobs\UploadFileToS3;

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
        return response()->json($folders);
    }

    public function uploadFile(Request $request)
    {
        try
        {
            ini_set('max_execution_time', 3600);
            $receiver = new FileReceiver('file', $request, ResumableJSUploadHandler::class);

            if ($receiver->isUploaded() === false) {
                return response()->json(['message' => 'File not uploaded.'], 400);
            }

            $save = $receiver->receive();

            if ($save->isFinished()) {
                $file = $save->getFile();

                $extension = $file->getClientOriginalExtension();
                $sizeInBytes = $file->getSize(); // Get size in bytes
                $sizeInMB = round($sizeInBytes / (1024 * 1024), 2); // Convert to MB with 2 decimal places

                $fileName = uniqid() . auth()->user()->id . "." . $extension;
                $file->move(storage_path('app/public/uploads'), $fileName);

                $path = 'storage/uploads/' . $fileName;



                $getFile = getFile($request->file_id); 
                //return response()->json($getFile);
                $getFile->temp_file_path = $path;
                $getFile->extension = $extension;
                $getFile->file_size = $sizeInMB; // Save MB value 

                $getFile->update();


                UploadFileToS3::dispatch([
                    'file_id' => $getFile->id,
                    'key' => $fileName,
                    'local_path' => storage_path("app/public/uploads/{$fileName}"),
                ]);


                return response()->json([
                    'status' => true,
                    'path' => $path,
                    'name' => $fileName,
                    'size' => $sizeInMB . ' MB',
                    'extension' => $extension
                ]);

            }
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }
}
