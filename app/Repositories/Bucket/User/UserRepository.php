<?php
 namespace App\Repositories\User;
 use App\Models\User;
 
 class UserRepository implements UserInterface
 {
 	public function fetch()
 	{
 		try
 		{
 			$users = User::query();
 			return $users;
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function store($request)
 	{
 		//
 	}

 	public function update($request,$user)
 	{
 		//
 	}

 	public function delete($user)
 	{
 		//
 	}

 	public function statusUpdate($request)
 	{
 		//
 	}
 }