<?php
 namespace App\Repositories\Folder;

 interface FolderInterface
 {
 	public function fetch();
 	public function store($request);
 	public function statusUpdate($request);
 	public function delete($folder);
 }