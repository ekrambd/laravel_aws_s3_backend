<?php
 
 use App\Models\Bucket;	
 use App\Models\Folder;

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

 function folderRepository()
 {
 	$folderRepository = app(\App\Repositories\Folder\FolderRepository::class);
 	return $folderRepository;
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

 function folders()
 {
 	$folders = folderRepository()->fetch()->where('user_id',user()->id)->where('status','Active')->latest()->get();
 	return $folders;
 }

 function folder($folder_id)
 {
 	$folder = Folder::findorfail($folder_id);
 	return $folder;
 }