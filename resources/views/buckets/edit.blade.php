@extends('admin_master')

@section('content')
 <main class="app-main">
  <div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Edit Bucket</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Bucket</li>
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
               <div class="card-header bg-success text-light"><div class="card-title">Edit Bucket</div></div>
              <form action="{{route('buckets.update',$bucket->id)}}" method="POST">
                @csrf
                @method('PATCH')
                <div class="card-body">
                  <div class="mb-3">
                    <label for="bucket_name" class="form-label">Bucket Name <span class="required">*</span></label>
                    <input
                      type="text"
                      class="form-control"
                      name="bucket_name"
                      id="bucket_name"
                      placeholder="Bucket Name"
                      required=""
                      readonly=""
                      value="{{old('bucket_name',$bucket->bucket_name)}}"
                    />
                    @error('bucket_name')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="required">*</span></label>
                    <select class="form-control" name="status" id="status" required>
                      <option value="" selected="" disabled="">Select Status</option>
                      <option value="Public" <?php if($bucket->status == 'Public'){echo "selected";} ?>>Public</option>
                      <option value="Private" <?php if($bucket->status == 'Private'){echo "selected";} ?>>Private</option>
                    </select>
                    @error('status')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
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
