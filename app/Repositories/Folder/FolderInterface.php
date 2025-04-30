<?php
 namespace App\Repositories\Folder;

 interface FolderInterface
 {
 	public function fetch();
 	public function store($request);
 	public function update($request,$folder);
 	public function delete($folder);
 }