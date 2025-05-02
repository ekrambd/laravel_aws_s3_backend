@extends('admin_master')

@section('content')
 <main class="app-main">
  <div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Add File</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add File</li>
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
               <div class="card-header bg-primary text-light"><div class="card-title">Add File</div></div>
              <form action="{{route('files.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                  <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="required">*</span></label>
                    <input
                      type="text"
                      class="form-control"
                      name="title"
                      id="title"
                      placeholder="Title"
                      required=""
                      value="{{old('title')}}"
                    />
                    @error('title')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div> 


                  <div class="mb-3">
                    <label for="storage_class" class="form-label">Select Storage Class <span class="required">*</span></label> 
                    <select class="form-control select2bs4" name="storage_class" id="storage_class" required>
                      <option value="" selected="" disabled="">Select Storage Class</option>
                      <option value="STANDARD">STANDARD</option>
                      <option value="INTELLIGENT_TIERING">INTELLIGENT_TIERING</option>
                      <option value="ONEZONE_IA">ONEZONE_IA</option>
                      <option value="GLACIER">GLACIER</option>
                      <option value="DEEP_ARCHIVE">DEEP_ARCHIVE</option>
                    </select>
                    @error('storage_class')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="bucket_id" class="form-label">Select Bucket <span class="required">*</span></label>
                    <select class="form-control select2bs4" name="bucket_id" id="bucket_id" required>
                      <option value="" selected="" disabled="">Select Bucket</option>
                      @foreach(buckets() as $bucket)
                       <option value="{{$bucket->id}}">{{$bucket->bucket_name}}</option>
                      @endforeach
                    </select>
                    @error('bucket_id')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="folder_id" class="form-label">Select Folder <span class="required">*</span></label>
                    <select class="form-control select2bs4" name="folder_id" id="folder_id" required>
                      <option value="" selected="" disabled="">Select Folder</option>

                    </select>
                    @error('folder_id')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                  	<label for="file">File <span class="required">*</span></label>
                  	<input type="file" name="file" id="file" class="form-control" required>
                  	@error('file')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>	


                  <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="required">*</span></label>
                    <select class="form-control select2bs4" name="status" id="status" required>
                      <option value="" selected="" disabled="">Select Status</option>
                      <option value="Public">Public</option>
                      <option value="Private">Private</option>
                    </select>
                    @error('status')
                      <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <button type="submit" class="btn btn-success">Submit</button>
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

@push('scripts')
 <script>
   $(document).ready(function(){
   	 $(document).on('change', '#bucket_id', function(){
   	 	$('#folder_id').html('<option value="" selected="" disabled="">Select Folder</option>');
   	 	let bucket_id = $(this).val();
   	 	$.ajax({
            url: "{{ url('bucket-folders') }}",
            type: "POST",
            data: {'bucket_id': bucket_id},
            dataType: "json",
            success: function(data) {
            	$(data).each(function(idx,val){
            		let html = `<option value=${val.id}>${val.folder_name}</option>`;
            		$('#folder_id').append(html);
            	});
            }
        }); 
   	 });
   });	
 </script>
@endpush
