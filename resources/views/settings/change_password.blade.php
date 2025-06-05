@extends('admin_master')

@section('content')
 <main class="app-main">
  <div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Change Password</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Change Password</li>
          </ol>
        </div>
      </div>
      <!--end::Row-->
    </div>
    <!--end::Container-->

    <!--begin::App Content-->
    <div class="app-content">
      <!--begin::Container-->
       <div class="container-fluid">
         <!--begin::Row-->
          <div class="row g-4">
            <div class="col-md-12">
             <div class="card">
               <div class="card-header bg-success text-light"><div class="card-title">Change Password</div></div>
              <form action="{{url('password-change')}}" method="POST">
                @csrf
                <div class="card-body">
                  <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password <span class="required">*</span></label>
                    <input
                      type="password"
                      class="form-control"
                      name="current_password"
                      id="current_password"
                      placeholder="Current Password"
                      required=""
                      value="{{old('current_password')}}"
                    />
                    @error('current_password')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div> 


                  <div class="mb-3">
                    <label for="new_password" class="form-label">New Password <span class="required">*</span></label>
                    <input
                      type="password"
                      class="form-control"
                      name="new_password"
                      id="new_password"
                      placeholder="New Password"
                      required=""
                      value="{{old('new_password')}}"
                    />
                    @error('new_password')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>


                  <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
                    <input
                      type="password"
                      class="form-control"
                      name="confirm_password"
                      id="confirm_password"
                      placeholder="Confirm Password"
                      required=""
                      value="{{old('confirm_password')}}"
                    />
                    @error('confirm_password')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <button type="submit" class="btn btn-success">Change Password</button>
                  </div>

                </div>
              </form>
             </div> 
              
            </div>
          </div>
        <!--end::Row--> 
       </div>
      <!--end::Container-->
    </div>

  </div>
 </main>
@endsection
