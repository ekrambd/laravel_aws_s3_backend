<?php
 
 use App\Models\Bucket;	

 function user()
 {
 	$user = auth()->user();
 	return $user;
 }

 function bucketRepository()
 {
 	$bucketRepository = app(\App\Repositories\Bucket\BucketRepository::class);
 	return $bucketRepository;
 }

 function buckets()
 {  
 	$buckets = bucketRepository()->fetch()->where('user_id',user()->id)->latest()->get();
 	return $buckets;
 }

 function bucket($bucket_id)
 {
 	$bucket = bucketRepository()->fetch()->findorfail($bucket_id);
 	return $bucket;
 }