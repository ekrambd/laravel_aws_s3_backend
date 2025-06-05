<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $user;

    public function __construct(UserInterface $user)
    {   
        $this->middleware('auth_check');
        $this->user = $user;
    }

    public function index(Request $request)
    {
        if($request->ajax()){
                $users = $this->user->fetch()->latest();
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('bucket', function ($row) {
                        return $row->bucket->bucket_name;
                    })
                    ->addColumn('status', function($row){
                        return '<label class="switch"><input class="' . ($row->status == 'Active' ? 'active-user' : 'decline-user') . '" id="status-user-update"  type="checkbox" ' . ($row->status == 'Active' ? 'checked' : '') . ' data-id="'.$row->id.'"><span class="slider round"></span></label>';
                    })
                    ->addColumn('action', function ($row) {
                        $btn = "";
                        $btn .= ' <button type="button" class="btn btn-danger btn-sm delete-user action-button" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                        return $btn;
                    })
                    ->rawColumns(['action','bucket','status']) 
                    ->make(true);
        }
        return view('folders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $response = $this->user->store($request);
        $data = $response->getData(true);
        if($data['status'] == true)
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
    public function show(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $response = $this->user->update($request,$user);
        $data = $response->getData(true);
        if($data['status'] == true)
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

        return redirect('/users')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $delete = $this->user->delete($user);
        return $delete;
    }
}
