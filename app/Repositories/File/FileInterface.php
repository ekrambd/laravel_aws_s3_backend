<?php
 namespace App\Repositories\File;

 interface FileInterface
 {
 	public function fetch();
 	public function store($request);
 	public function statusUpdate($request);
 	public function delete($file);
 }