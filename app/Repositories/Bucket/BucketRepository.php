<?php
 namespace App\Repositories\Bucket;
 use App\Models\Bucket;
 use Aws\S3\S3Client;
 use Illuminate\Support\Str;
 use Illuminate\Support\Facades\Storage;

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

	public function update($request,$bucket)
	{
		try
		{   
			$bucketName = $bucket->bucket_slug;
			if($request->status == 'Public' && $bucket->status == 'Private')
			{
				try
				{
					// 3.1 Unblock all public access
		            $this->s3Client->putPublicAccessBlock([
		                'Bucket' => $bucketName,
		                'PublicAccessBlockConfiguration' => [
		                    'BlockPublicAcls' => false,
		                    'IgnorePublicAcls' => false,
		                    'BlockPublicPolicy' => false,
		                    'RestrictPublicBuckets' => false,
		                ],
		            ]);

		            // 3.2 Set a public bucket policy to allow public read, using tags to control visibility
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
		                                    's3:ExistingObjectTag/Public' => 'true', // Only allow public files
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
		                                    's3:ExistingObjectTag/Public' => 'true', // Deny all non-public files
		                                ]
		                            ]
		                        ]
		                    ]
		                ]),
		            ]);

		            // 3.3 Enable ACL ownership (ACLs allowed)
		            $this->s3Client->putBucketOwnershipControls([
		                'Bucket' => $bucketName,
		                'OwnershipControls' => [
		                    'Rules' => [
		                        [
		                            'ObjectOwnership' => 'ObjectWriter', // Allow ACLs
		                        ],
		                    ],
		                ],
		            ]);

		            $bucket->status = $request->status;
		            $bucket->update();

		            return response()->json([
		            	'status' => true,
		                'visibility' => 'Public',
		                'message' => 'Successfully the bucket public',
		            ]);

				}catch(\Aws\Exception\AwsException $e){
		 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
		 		}
			}elseif($request->status == 'Private' && $bucket->status == 'Public'){
				try
				{   
					$s3Client = new \Aws\S3\S3Client([
			            'version' => 'latest',
			            'region'  => 'ap-southeast-1',
			            'credentials' => [
			                'key'    => env('AWS_ACCESS_KEY_ID'),
			                'secret' => env('AWS_SECRET_ACCESS_KEY'),
			            ],
			        ]);

			        $s3Client->putPublicAccessBlock([
			            'Bucket' => $bucketName,
			            'PublicAccessBlockConfiguration' => [
			                'BlockPublicAcls' => true,
			                'IgnorePublicAcls' => true,
			                'BlockPublicPolicy' => true,
			                'RestrictPublicBuckets' => true,
			            ],
		            ]);

		            $bucket->status = $request->status;
		            $bucket->update();

		            return response()->json([
		            	'status' => true,
		                'visibility' => 'Private',
		                'message' => 'Successfully the bucket Private',
		            ]);

				}catch(\Aws\Exception\AwsException $e){
		 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
		 		}
			}

			return response()->json([
            	'status' => true,
                'visibility' => $request->status,
                'message' => "Successfully the bucket {$request->status}",
		    ]);
		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
	}


 	public function delete($bucket)
	{
	    try {
	        $bucketName = $bucket->bucket_slug;
	        // 1. List all objects
	        $objects = $this->s3Client->listObjectsV2([
	            'Bucket' => $bucketName,
	        ]);

	        // 2. Delete all objects (batch delete if multiple)
	        if (!empty($objects['Contents'])) {
	            $deleteKeys = [];
	            foreach ($objects['Contents'] as $object) {
	                $deleteKeys[] = ['Key' => $object['Key']];
	            }

	            $this->s3Client->deleteObjects([
	                'Bucket'  => $bucketName,
	                'Delete' => [
	                    'Objects' => $deleteKeys,
	                    'Quiet'   => true,
	                ],
	            ]);
	        }

	        // 3. Delete the bucket
	        $this->s3Client->deleteBucket([
	            'Bucket' => $bucketName,
	        ]);

	        // 4. Wait until the bucket is deleted
	        $this->s3Client->waitUntil('BucketNotExists', [
	            'Bucket' => $bucketName,
	        ]);

	        $bucket->delete();

	        return response()->json(['message' => 'Bucket deleted successfully']);

	    }catch(\Aws\Exception\AwsException $e){
		 	return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
		}catch (Exception $e) {
	        return response()->json([
	            'status' => false,
	            'code' => $e->getCode(),
	            'message' => $e->getMessage(),
	        ], 500);
	    }
	}
 }