<?php
 namespace App\Repositories\Bucket;
 use App\Models\Bucket;
 use Aws\S3\S3Client;
 use Illuminate\Support\Str;

 class BucketRepository implements BucketInterface
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
 			$buckets = Bucket::query();
 			return $buckets;
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function store($request)
	{
	    try {
	        $visibility = $request->status;
	        $bucketName = Str::slug($request->bucket_name);

	        // Try to create the bucket
	        try {
	            $this->s3Client->createBucket([
	                'Bucket' => $bucketName,
	            ]);
	        } catch (\Aws\Exception\AwsException $e) {
	            return response()->json([
	                'status' => false,
	                'code' => $e->getCode(),
	                'message' => $e->getAwsErrorMessage() ?? $e->getMessage()
	            ], 500);
	        }

	        // Wait until bucket exists
	        try {
	            $this->s3Client->waitUntil('BucketExists', [
	                'Bucket' => $bucketName,
	            ]);
	        } catch (\Aws\Exception\AwsException $e) {
	            return response()->json([
	                'status' => false,
	                'code' => $e->getCode(),
	                'message' => $e->getAwsErrorMessage() ?? $e->getMessage(),
	            ], 500);
	        }

	        // If public, configure access and policies
	        if ($visibility === 'Public') {
	            $this->s3Client->putPublicAccessBlock([
	                'Bucket' => $bucketName,
	                'PublicAccessBlockConfiguration' => [
	                    'BlockPublicAcls' => false,
	                    'IgnorePublicAcls' => false,
	                    'BlockPublicPolicy' => false,
	                    'RestrictPublicBuckets' => false,
	                ],
	            ]);

	            $this->s3Client->putBucketPolicy([
	                'Bucket' => $bucketName,
	                'Policy' => json_encode([
	                    'Version' => '2012-10-17',
	                    'Statement' => [
	                        [
	                            'Sid' => 'PublicReadForPublicFiles',
	                            'Effect' => 'Allow',
	                            'Principal' => '*',
	                            'Action' => 's3:GetObject',
	                            'Resource' => "arn:aws:s3:::{$bucketName}/*",
	                            'Condition' => [
	                                'StringEquals' => [
	                                    's3:ExistingObjectTag/Public' => 'true',
	                                ]
	                            ]
	                        ],
	                        [
	                            'Sid' => 'DenyAllPublicAccessExceptPublicReadFiles',
	                            'Effect' => 'Deny',
	                            'Principal' => '*',
	                            'Action' => 's3:GetObject',
	                            'Resource' => "arn:aws:s3:::{$bucketName}/*",
	                            'Condition' => [
	                                'StringNotEquals' => [
	                                    's3:ExistingObjectTag/Public' => 'true',
	                                ]
	                            ]
	                        ]
	                    ]
	                ]),
	            ]);

	            $this->s3Client->putBucketOwnershipControls([
	                'Bucket' => $bucketName,
	                'OwnershipControls' => [
	                    'Rules' => [
	                        [
	                            'ObjectOwnership' => 'ObjectWriter',
	                        ],
	                    ],
	                ],
	            ]);
	        }

	        // Save to DB
	        $bucket = Bucket::create([
	            'user_id' => user()->id,
	            'bucket_name' => $request->bucket_name,
	            'bucket_slug' => $bucketName,
	            'status' => $visibility
	        ]);

	        return response()->json([
	            'status' => true,
	            'message' => 'Bucket created successfully!',
	            'bucket' => $bucketName,
	            'visibility' => $visibility,
	        ]);

	    } catch (\Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}


 	public function delete($bucket)
 	{
 		try
 		{
 			//
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}
 }