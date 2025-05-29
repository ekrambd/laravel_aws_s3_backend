<?php
 namespace App\Repositories\User;

 interface UserInterface
 {
 	public function fetch();
 	public function store($request);
 	public function update($request,$user);
 	public function delete($user);
 	public function statusUpdate($request);
 }