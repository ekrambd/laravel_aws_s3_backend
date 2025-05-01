<?php
 namespace App\Repositories\Folder;
 use App\Models\Folder;
 use Aws\S3\S3Client;
 use Illuminate\Support\Str;
 use Illuminate\Support\Facades\Storage;

 class FolderRepository implements FolderInterface
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
 			$folders = Folder::query();
 			return $folders;
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function store($request)
	{
	    try
	    {   
	        $folderName = str_replace(" ", "", strtolower($request->folder_name));
	        $bucket = bucket($request->bucket_id);
	        $bucketName = $bucket->bucket_slug;

	        if (substr($folderName, -1) !== '/') {
	            $folderName .= '/';
	        }

	        // Create empty "folder" object
	        $this->s3Client->putObject([
	            'Bucket' => $bucketName,
	            'Key'    => $folderName,
	            'Body'   => '',
	        ]);


	        Folder::create([
	            'user_id'     => user()->id,
	            'bucket_id'   => $request->bucket_id,
	            'folder_name' => $request->folder_name,
	            'folder_slug' => $folderName,
	            'status'      => $request->status,
	        ]);

	        return response()->json(['status' => true, 'message' => 'Folder created successfully']);

	    } catch (\Aws\Exception\AwsException $e) {
	        return response()->json([
	            'status' => false,
	            'code'   => $e->getStatusCode() ?? 500,
	            'message'=> $e->getAwsErrorMessage() ?? $e->getMessage()
	        ], 500);
	    } catch (Exception $e) {
	        return response()->json([
	            'status'=>false,
	            'code'  =>$e->getCode(),
	            'message'=>$e->getMessage()
	        ], 500);
	    }
	}


 	public function statusUpdate($request)
 	{
 		try
        {   

        	$folder = $this->fetch()->findorfail($request->folder_id);
        	$folder->status = $request->status;
        	$folder->update();
	        return response()->json(['status' => true, 'message' => "Successfully the folder's status has been updated"]);

        }catch (\Aws\Exception\AwsException $e) {
            return response()->json([
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getAwsErrorMessage() ?? $e->getMessage()
            ], 500);
        }catch(Exception $e){
			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function delete($folder)
 	{
 		try {
	        
	        $folderName = $folder->folder_slug;

	        $bucketName = $folder->bucket->bucket_slug;

	        $objects = $this->s3Client->listObjectsV2([
	            'Bucket' => $bucketName,
	            'Prefix' => $folderName,
	        ]);

	        if (empty($objects['Contents'])) {
	            return response()->json(['message' => 'No objects found under this folder'], 404);
	        }

	        $deleteKeys = array_map(function ($object) {
	            return ['Key' => $object['Key']];
	        }, $objects['Contents']);

	        $this->s3Client->deleteObjects([
	            'Bucket' => $bucketName,
	            'Delete' => [
	                'Objects' => $deleteKeys,
	            ],
	        ]);

	        $folder->delete();

	        return response()->json(['message' => 'Folder deleted successfully']);

	    }catch (\Aws\Exception\AwsException $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getStatusCode() ?? 500,
	            'message' => $e->getMessage(),
	        ], 500);
	    }catch (Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage(),
	        ], 500);
	    }
 	}
 }