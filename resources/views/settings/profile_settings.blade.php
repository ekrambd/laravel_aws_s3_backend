@extends('admin_master')

@section('content')
 <main class="app-main">
  <div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Profile Settings</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
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
               <div class="card-header bg-success text-light"><div class="card-title">Profile Settings</div></div>
              <form action="{{url('settings-profile')}}" method="POST">
                @csrf
                <div class="card-body">
                  <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="required">*</span></label>
                    <input
                      type="text"
                      class="form-control"
                      name="name"
                      id="name"
                      placeholder="Name"
                      required=""
                      value="{{old('name',user()->name)}}"
                    />
                    @error('name')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div> 


                  <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                    <input
                      type="email"
                      class="form-control"
                      name="email"
                      id="email"
                      placeholder="Email"
                      required=""
                      value="{{old('email',user()->email)}}"
                    />
                    @error('email')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>


                  <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input
                      type="text"
                      class="form-control"
                      name="phone"
                      id="phone"
                      placeholder="Phone"
                      value="{{old('phone',user()->phone)}}"
                    />
                    @error('phone')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input
                      type="file"
                      class="form-control"
                      name="image"
                      id="image"
                    />
                    @error('image')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <img src="{{URL::to(user()->image)}}" width="60" height="60" class="img-fluid"/>
                  </div>

                  <div class="mb-3">
                    <button type="submit" class="btn btn-success">Save Changes</button>
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
