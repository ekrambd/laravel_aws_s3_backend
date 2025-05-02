<?php
 namespace App\Repositories\File;
 use App\Models\File;

 class FileRepository implements FileInterface
 {
 	public function fetch()
 	{
 		try
 		{
 			$files = File::query();
 			return $files;
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500)
 		}
 	}

 	public function store($request)
 	{
 		try
 		{
 			//
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500)
 		}
 	}
 }