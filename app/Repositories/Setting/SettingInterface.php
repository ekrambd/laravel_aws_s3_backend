<?php
 namespace App\Repositories\Setting;

 interface SettingInterface
 {
 	public function passwordChange($request);
 	public function profileSetting($request);
 	public function appSettings($request);
 }