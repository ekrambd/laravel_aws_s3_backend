<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SettingRequest;
use App\Http\Requests\ProfileSettingRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Repositories\Setting\SettingInterface;

class SettingController extends Controller
{   

    protected $setting;

    public function __construct(SettingInterface $setting)
    {   
        $this->middleware('auth_check');
        $this->setting = $setting;
    }

    public function changePassword()
    {
        return view('settings.change_password');
    }

    public function profileSettings()
    {
        return view('settings.profile_settings');
    }

    public function passwordChange(PasswordChangeRequest $request)
    {
        $response = $this->setting->passwordChange($request);
        $data = $response->getData(true);
        if($data['status'] == true)
        {
            $notification = array(
                'messege'=>$data['message'],
                'alert-type'=>'success'
            );

            return redirect()->back()->with($notification); 
        }
        $notification = array(
            'messege'=>$data['message'],
            'alert-type'=>'error'
        );

        return redirect()->back()->with($notification); 
    }

    public function settingsProfile(ProfileSettingRequest $request)
    {
        $response = $this->setting->profileSetting($request); 
        $data = $response->getData(true);
        if($data['status'] == true)
        {
            $notification = array(
                'messege'=>$data['message'],
                'alert-type'=>'success'
            );

            return redirect()->back()->with($notification); 
        }
        $notification = array(
            'messege'=>$data['message'],
            'alert-type'=>'error'
        );

        return redirect()->back()->with($notification);
    }
}
