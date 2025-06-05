<?php
 namespace App\Repositories\User;
 use App\Models\User;
 use App\Models\Uploadlimit;
 use DB;

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
 		DB::beginTransaction();
 		try
 		{   
 			if($request->file('image'))
 			{
 				$file = $request->file('image');
 				$extension = $file->getClientOriginalExtension();
 				$name = time().user()->id.$extension;
 				$file->move(public_path('/uploads/users/'), $name);
		        $path = 'uploads/users/' . $name;
 			}else{
 				$path = NULL;
 			}
 			$user = User::create([
 				'name' => $request->name,
 				'role_id' => $request->role_id,
 				'email' => $request->email,
 				'phone' => $request->phone,
 				'password' => bcrypt('123456'),
 				'image' => $path,
 				'status' => $request->status,
 			]);
 			Uploadlimit::create([
 				'user_id' => $user->id,
 				'max_upload' => $request->max_upload
 			]);
 			DB::commit();
 			return response()->json(['status'=>true, 'user_id'=>intval($user->id), 'message'=>'Successfully an user has been added']);
 		}catch(Exception $e){
 			DB::rollback();
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function update($request,$user)
 	{   
 		DB::beginTransaction();
 		try
 		{   
 			if($request->file('image'))
 			{
 				$file = $request->file('image');
 				$extension = $file->getClientOriginalExtension();
 				$name = time().user()->id.$extension;
 				$file->move(public_path('/uploads/users/'), $name);
 				if($user->image != NULL)
 				{
 					unlink(public_path($user->image));
 				}
		        $path = 'uploads/users/' . $name;
 			}else{
 				$path = $user->image;
 			}
 			$user->email = $request->email;
 			$user->phone = $request->phone;
 			$user->image = $path;
 			$user->status = $request->status;
 			$user->update();
 			$limit = $user->uploadlimit;
 			$limit->max_upload = $request->max_upload;
 			$limit->update();
 			DB::commit();
 			return response()->json(['status'=>true, 'user_id'=>intval($user->id), 'message'=>'Successfully the user has been added']);
 		}catch(Exception $e){
 			DB::rollback();
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function delete($user)
 	{
 		try
 		{
 			if($user->image != NULL)
			{
				unlink(public_path($user->image));
			}
			$user->delete();
			return response()->json(['status'=>true, 'message'=>'Successfully the user has been deleted']);
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}

 	public function statusUpdate($request)
 	{
 		try
 		{
 			$user = $this->fetch()->findorfail($request->user_id);
 			$user->status = $request->status;
 			$user->update();
 			return response()->json(['status'=>true, 'message'=>"Successfully the user's status has been updated"]);
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}
 }