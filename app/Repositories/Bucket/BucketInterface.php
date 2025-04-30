<?php
 namespace App\Repositories\Bucket;

 interface BucketInterface
 {
 	public function fetch();
 	public function store($request);
 	public function update($request,$bucket);
 	public function delete($bucket);
 }