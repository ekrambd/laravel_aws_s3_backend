<?php
 namespace App\Repositories\File;
 use App\Models\File;
 use App\Models\Bucket;
 use Aws\S3\S3Client;
 use Illuminate\Support\Str;
 use Validator;

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
 			//$file->delete();
            return response()->json(['status'=>true, 'message'=>'Successfully the file has been deleted']);
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
 		}
 	}

    public function fileUpload($file)
    {
        //
    }
 }