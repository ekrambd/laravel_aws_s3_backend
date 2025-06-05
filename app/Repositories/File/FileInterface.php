<?php
 namespace App\Repositories\File;

 interface FileInterface
 {
 	public function fetch();
 	public function store($request);
 	public function update($request,$file);
 	public function delete($file);
 	public function uploadFile($request);
 	public function cancelUpload($request);
 }