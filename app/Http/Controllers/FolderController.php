<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use App\Repositories\Folder\FolderInterface;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use DataTables;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $folder;

    public function __construct(FolderInterface $folder)
    {   
        $this->middleware('auth_check');
        $this->folder = $folder;
    }

    public function index(Request $request)
    {
        if($request->ajax()){
                $folders = $this->folder->fetch()->where('user_id',user()->id)->latest();
                return DataTables::of($folders)
                    ->addIndexColumn()

                    ->addColumn('action', function ($row) {
                        $btn = "";
                        $btn .= ' <a href="' . route('folders.show', $row->id) . '" class="btn btn-primary btn-sm action-button edit-product-folder"><i class="fa fa-edit"></i></a>';
                        $btn .= '&nbsp;';
                        $btn .= ' <button type="button" class="btn btn-danger btn-sm delete-folder action-button" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                        return $btn;
                    })
                    ->rawColumns(['action']) 
                    ->make(true);
        }
        return view('folders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('folders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFolderRequest $request)
    {
        $response = $this->folder->store($request);
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
    public function show(Folder $folder)
    {
        return view('folders.edit', compact('folder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Folder $folder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        $response = $this->folder->update($request,$folder);
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
    public function destroy(Folder $folder)
    {
        $delete = $this->folder->delete();
        return $delete;
    }
}
