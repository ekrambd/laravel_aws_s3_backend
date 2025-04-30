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
	            'ACL'    => 'private', // Always private
	        ]);

	        // Tag the folder object if Public is needed
	        if ($request->status == 'Public') {
	            $this->s3Client->putObjectTagging([
	                'Bucket' => $bucketName,
	                'Key'    => $folderName,
	                'Tagging' => [
	                    'TagSet' => [
	                        [
	                            'Key'   => 'Public',
	                            'Value' => 'true',
	                        ],
	                    ],
	                ],
	            ]);
	        }

	        Folder::create([
	            'user_id'     => user()->id,
	            'bucket_id'   => $request->bucket_id,
	            'folder_name' => $folderName,
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


 	public function update($request,$folder)
 	{
 		try
        {   

        	$folderName = $folder->folder_name;
        	$bucketName = $folder->bucket->bucket_slug;

        	if (substr($folderName, -1) !== '/') {
	            $folderName .= '/';
	        }

	        // Upload empty object with public-read ACL
	        $s3Client->putObject([
	            'Bucket' => $bucketName,
	            'Key'    => $folderName,
	            'Body'   => '',
	            'ACL'    => $request->status == 'Public'?'public-read':'private', // <-- public access
	        ]);

	        $folder->status = $request->status;
	        $folder->update();

	        return response()->json(['status'=>true,'message' => 'Successfully the file has been updated']);

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
	        
	        $folderName = $folder->folder_name;

	        $folderName = rtrim($folderName, '/') . '/';

	        $bucketName = $folder->bucket->bucket_slug;

	        $objects = $s3Client->listObjectsV2([
	            'Bucket' => $bucketName,
	            'Prefix' => $folderName,
	        ]);

	        if (empty($objects['Contents'])) {
	            return response()->json(['message' => 'No objects found under this folder'], 404);
	        }

	        $deleteKeys = array_map(function ($object) {
	            return ['Key' => $object['Key']];
	        }, $objects['Contents']);

	        $s3Client->deleteObjects([
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