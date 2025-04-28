<?php
 namespace App\Repositories\Bucket;
 use App\Models\Bucket;
 use App\Repositories\Bucket\BucketInterface;
 use DB;

 class BucketRepository implements BucketInterface
 {  

 	public function fetch()
 	{
 		try
 		{
 			$buckets = Bucket::query();
 			return $buckets;
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function store($request)
 	{
 		try
 		{
 			$awsKey = config('ses.aws.key');
		    $awsSecret = config('ses.aws.secret');
		    $awsRegion = config('ses.aws.region');
		    return response()->json([
		        'AWS_ACCESS_KEY_ID' => $awsKey,
		        'AWS_SECRET_ACCESS_KEY' => $awsSecret,
		        'AWS_DEFAULT_REGION' => $awsRegion
		    ]);
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function delete($bucket)
 	{
 		try
 		{
 			return $bucket;
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}
 }