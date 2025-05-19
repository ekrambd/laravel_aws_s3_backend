<?php
 namespace App\Repositories\File;
 use App\Models\File;
 use App\Models\Bucket;
 use Aws\S3\S3Client;
 use Illuminate\Support\Str;
 use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
 use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
 use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
 use App\Jobs\UploadFileToS3;
 use Validator;
 use Illuminate\Support\Facades\Storage;

 class FileRepository implements FileInterface
 {  

 	protected $awsKey;
 	protected $awsSecret;
 	protected $awsRegion;

 	public function __construct()
 	{
 		$this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('services.ses.region'),
            'credentials' => [
                'key' => config('services.ses.key'),
                'secret' => config('services.ses.secret'),
            ],
        ]);
 	}

 	public function fetch()
 	{
 		try 
 		{
 			$files = File::query();
 			return $files;
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
 		}
 	}

 	public function store($request)
 	{   
 		try
        {   
            if(checkBucket($request,$request->bucket_id))
            {
                return response()->json(['status'=>false, 'file_id'=>0, 'message'=>"Sorry you can't upload a public file in the private bucket"]);
            } 
            $file = new File();
            $file->user_id = user()->id; 
            $file->title = $request->title;
            $file->bucket_id = $request->bucket_id;
            $file->folder_id = $request->folder_id;
            $file->storage_class = $request->storage_class;
            $file->status = $request->status;
            $file->file_importance = $request->file_importance;
            $file->upload_status = 'Pending';
            $file->save();
            return response()->json(['status'=>true, 'file_id'=>intval($file->id), 'message'=>'Successfully complete first step']);
        }catch(Exception $e){
            return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
        }
 	}

    public function update($request,$file)
    {
        try
        {   

            if(checkBucket($request,$file->bucket_id))
            {
                return response()->json(['status'=>false, 'file_id'=>0, 'message'=>"Sorry you can't upload a public file in the private bucket"]);
            }

            $bucketName = $file->bucket->bucket_slug; 
            $fileKey = $file->file_key;

            if($request->status != $file->status)
            {
                if($request->status == 'Public')
                {
                    $this->makePublic($bucketName,$fileKey);
                }elseif($request->status == 'Private'){
                    $this->makePrivate($bucketName,$fileKey);
                }
            }
            $file->status = $request->status;
            $file->file_importance = $request->file_importance;
            $file->update();

            return response()->json(['status'=>true, 'file_id'=>intval($file->id), 'message'=>'Successfully the file has been updated']);

        }catch(Exception $e){
            return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
        }
    }

    public function makePublic($bucketName,$fileKey)
    {
        $this->s3Client->putObjectTagging([
            'Bucket' => $bucketName,
            'Key' => $fileKey,
            'Tagging' => [
                'TagSet' => [
                    [
                        'Key' => 'Public',
                        'Value' => 'true',
                    ],
                ],
            ],
        ]);
        $this->s3Client->putObjectAcl([
            'Bucket' => $bucketName,
            'Key' => $fileKey,
            'ACL' => 'public-read',
        ]);
    }

    public function makePrivate($bucketName,$fileKey)
    {
        $this->s3Client->putObjectTagging([
            'Bucket' => $bucketName,
            'Key' => $fileKey,
            'Tagging' => [
                'TagSet' => [
                    [
                        'Key' => 'Public',
                        'Value' => 'false',
                    ],
                ],
            ],
        ]);
        $this->s3Client->putObjectAcl([
            'Bucket' => $bucketName,
            'Key' => $fileKey,
            'ACL' => 'private',
        ]);
    }

 	public function statusUpdate($request)
 	{
 		try
 		{
 			//
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
 		}
 	}

 	public function delete($file)
 	{
 		try
 		{   

            $this->s3Client->deleteObject([
                'Bucket' => $file->bucket->bucket_slug,
                'Key'    => $file->file_key,
            ]);
 			$file->delete();
            return response()->json(['status'=>true, 'message'=>'Successfully the file has been deleted']);
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
 		}
 	}

    public function uploadFile($request)
    {
        try 
        {
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