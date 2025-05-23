<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Repositories\File\FileInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use DataTables;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $file;

    public function __construct(FileInterface $file)
    {   
        $this->middleware('auth_check');
        $this->file = $file;
    }

    public function index(Request $request)
    {
        if($request->ajax()){
                $files = $this->file->fetch()->where('user_id',user()->id)->latest();
                return DataTables::of($files)
                    ->addIndexColumn()
                    ->addColumn('folder', function ($row) {
                        return $row->folder_id == NULL?"-":$row->folder->folder_slug;
                    })
                    ->addColumn('bucket', function ($row) {
                        return $row->bucket->bucket_slug;
                    })

                    ->addColumn('size', function ($row) {
                        return $row->file_size." MB";
                    })
                    ->addColumn('action', function ($row) {
                        $btn = ""; 
                        $btn .= '&nbsp;';
                        $btn .= ' <a href="' . route('files.show', $row->id) . '" class="btn btn-primary btn-sm action-button edit-product-file"><i class="fa fa-edit"></i></a>';
                        $btn .= '&nbsp;';
                        
                        $btn .= ' <button type="button" class="btn btn-danger btn-sm delete-file action-button" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';

                        $btn .= '&nbsp;';
                        
                        $btn .= ' <a href="'.$row->bucket_url.'" target="__blank" class="btn btn-success btn-sm view-file action-button"><i class="fa fa-eye"></i></a>';

                        return $btn;
                    })
                    ->rawColumns(['action','bucket','folder','size']) 
                    ->make(true);
        }
        return view('files.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('files.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = $this->file->store($request);
        $data = $response->getData(true);
        if($data['status'])
        {
            $notification = array(
                'messege'=>$data['message'],
                'alert-type'=>'success'
            );

            return redirect('/add-file/'.$data['file_id'])->with($notification); 
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
    public function show(File $file)
    {   
        return view('files.edit', compact('file'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        $response = $this->file->update($request,$file);
        $data = $response->getData(true);
        if($data['status'])
        {
            $notification = array(
                'messege'=>$data['message'],
                'alert-type'=>'success'
            );

            return redirect()->route('files.index')->with($notification); 
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
    public function destroy(File $file)
    {
        $delete = $this->file->delete($file);
        return $delete;
    }

    public function addFile($id)
    {   
        if(checkFile($id))
        {
            return redirect('/files');
        }
        return view('files.add_file',compact('id'));
    }
}
