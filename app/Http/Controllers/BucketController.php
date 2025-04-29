<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBucketRequest;
use App\Http\Requests\UpdateBucketRequest;
use App\Repositories\Bucket\BucketInterface;

class BucketController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $bucket;

    public function __construct(BucketInterface $bucket)
    {   
        $this->middleware('auth_check');
        $this->bucket = $bucket;
    }

    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('buckets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBucketRequest $request)
    {
        $response = $this->bucket->store($request);
        $data = $response->getData(true);
        if($data['status'])
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

    /**
     * Display the specified resource.
     */
    public function show(Bucket $bucket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bucket $bucket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBucketRequest $request, Bucket $bucket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bucket $bucket)
    {
        //
    }
}
