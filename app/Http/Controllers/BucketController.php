<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBucketRequest;
use App\Http\Requests\UpdateBucketRequest;
use App\Repositories\Bucket\BucketInterface;
use DataTables;

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
        if($request->ajax()){
                $buckets = $this->bucket->fetch()->where('user_id',user()->id)->latest();
                return DataTables::of($buckets)
                    ->addIndexColumn()

                    ->addColumn('action', function ($row) {
                        $btn = "";
                        $btn .= ' <a href="' . route('buckets.show', $row->id) . '" class="btn btn-primary btn-sm action-button edit-product-bucket"><i class="fa fa-edit"></i></a>';
                        $btn .= '&nbsp;';
                        $btn .= ' <button type="button" class="btn btn-danger btn-sm delete-bucket action-button" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                        return $btn;
                    })
                    ->rawColumns(['action']) 
                    ->make(true);
        }
        return view('buckets.index'); 
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
        return view('buckets.edit', compact('bucket'));
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
        $response = $this->bucket->update($request,$bucket);
        $data = $response->getData(true);
        if($data['status'])
        {
            $notification = array(
                'messege'=>$data['message'],
                'alert-type'=>'success'
            );

            return redirect()->route('buckets.index')->with($notification); 
        }

        $notification = array(
            'messege'=>$data['message'],
            'alert-type'=>'error'
        );

        return redirect()->back()->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bucket $bucket)
    {
        $delete = $this->bucket->delete($bucket);
        return $delete;
    }
}
