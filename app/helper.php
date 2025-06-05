<?php
 
 use App\Models\Bucket;	
 use App\Models\Folder;
 use App\Models\File;

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

 function fileRepository()
 {
 	$fileRepository = app(\App\Repositories\File\FileRepository::class);
 	return $fileRepository;
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

 function getFile($id)
 {
 	$file = fileRepository()->fetch()->with('folder','bucket')->findorfail($id);
 	return $file;
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

 function checkBucket($request,$bucket_id)
 {
 	$bucket = bucket($bucket_id);
    if($bucket->status == 'Private' && $request->status == 'Public')
    {
        return true;
    }
    return false;
 }

 function checkFile($id)
 {
    $check = File::where('id',$id)->where('upload_status','Pending')->first();
    if($check)
    {
      return true;
    }
    return false;
 }

 