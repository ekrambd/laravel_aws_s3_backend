<?php
 namespace App\Repositories\Setting;
 use App\Models\Setting;
 use App\Models\User;
 use Hash;
 use Auth;

 class SettingRepository implements SettingInterface
 {
 	public function passwordChange($request)
 	{
 		try
 		{   

 			$user = User::findorfail(Auth::user()->id);	    

			if (!Hash::check($request->current_password, $user->password)) {
		        return response()->json(['status'=>false, 'message'=>'The current password is not correct']);
		    }

		    $user->password = Hash::make($request->new_password);
		    $user->update();

 	 	  	   
 	 	  	return response()->json(['status'=>true, 'message'=>'Successfully your has been changed']);

 		}catch(Exception $e){

            $code = $e->getCode();           
            return response()->json(['message'=>'Something went wrong', 'execption_code'=>$code]);
        }
 	}

 	public function profileSetting($request)
 	{
 		try
 		{   

 			$user = user();

 			if($request->file('image'))
			{   
		        $file = $request->file('image');
		        $name = time().$user->id.$file->getClientOriginalName();
		        $file->move(public_path().'/uploads/users/', $name); 
		        if($user->image != 'defaults/profile.png') 
		        {
		        	unlink(public_path($user->image));
		        }
		        $path = 'uploads/users/'.$name;
			}else{
				$path = $user->image;
			}

 			$user->name = $request->name;
 			$user->email = $request->email;
 			$user->phone = $request->phone;
 			$user->image = $path;
 			$user->save();
 			return response()->json(['status'=>true, 'user_id'=>intval($user->id), 'message'=>'Successfully your profile info has been updated']); 
 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	} 

 	public function appSettings($request)
 	{
 		try
 		{   

 			$setting = setting();

 			if($request->file('app_logo'))
			{   
		        $file = $request->file('app_logo');
		        $name = time().user()->id.$file->getClientOriginalName();
		        $file->move(public_path().'/uploads/settings/', $name); 
		        if($setting->app_logo != NULL) 
		        {
		        	unlink(public_path($setting->app_logo));
		        }
		        $path = 'uploads/settings/'.$name;
			}else{
				$path = $setting->app_logo;
			}
 			
 			$setting->app_name = $request->app_name;
 			$setting->app_logo = $path;
 			$setting->update();

 			return response()->json(['status'=>true, 'message'=>'App Settings Successfully']);

 		}catch(Exception $e){
 			return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
 		}
 	}
 }
 