@extends('admin_master')

@section('content')
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Edit File</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Edit File</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row g-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-success text-light">
                <div class="card-title">Edit File</div>
              </div>

              <form action="{{ route('files.update',$file->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="card-body">
                  <div class="row">
                    {{-- Title --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="required">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Title" required readonly value="{{ old('title',$file->title) }}">
                        @error('title')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>

                    {{-- Storage Class --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="storage_class" class="form-label">Select Storage Class <span class="required">*</span></label>
                        <select class="form-control select2bs4" name="storage_class" id="storage_class" required disabled>
                          <option value="" disabled selected>Select Storage Class</option>
                          <option value="STANDARD" <?php if($file->storage_class == 'STANDARD'){echo "selected";} ?>>STANDARD</option>
                          <option value="INTELLIGENT_TIERING" <?php if($file->storage_class == 'INTELLIGENT_TIERING'){echo "selected";} ?>>INTELLIGENT_TIERING</option>
                          <option value="ONEZONE_IA" <?php if($file->storage_class == 'ONEZONE_IA'){echo "selected";} ?>>ONEZONE_IA</option>
                          <option value="GLACIER" <?php if($file->storage_class == 'GLACIER'){echo "selected";} ?>>GLACIER</option>
                          <option value="DEEP_ARCHIVE" ?php if($file->storage_class == 'DEEP_ARCHIVE'){echo "selected";} ?>>DEEP_ARCHIVE</option>
                        </select>
                        @error('storage_class')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>

                    {{-- Bucket --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="bucket_id" class="form-label">Select Bucket <span class="required">*</span></label>
                        <select class="form-control select2bs4" name="bucket_id" id="bucket_id" disabled required>
                          <option value="" disabled selected>Select Bucket</option>
                          @foreach (buckets() as $bucket)
                            <option value="{{ $bucket->id }}" <?php if($file->bucket_id == $bucket->id){echo "selected";} ?>>{{ $bucket->bucket_name }}</option>
                          @endforeach
                        </select>
                        @error('bucket_id')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>

                    {{-- Folder --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="folder_id" class="form-label">Select Folder</label>
                        <select class="form-control select2bs4" name="folder_id" id="folder_id" disabled>
                          <option value="" disabled selected>Select Folder</option>
                          @foreach(folders() as $folder)
                            <option value="{{$folder->id}}" <?php if($file->folder_id == $folder->id){echo "selected";} ?>>{{$folder->folder_name}}</option>
                          @endforeach
                        </select>
                        @error('folder_id')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="required">*</span></label>
                        <select class="form-control select2bs4" name="status" id="status" required>
                          <option value="" disabled selected>Select Status</option>
                          <option value="Public" <?php if($file->status == 'Public'){echo "selected";} ?>>Public</option>
                          <option value="Private" <?php if($file->status == 'Private'){echo "selected";} ?>>Private</option>
                        </select>
                        @error('status')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>

                    {{-- Importance --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="file_importance" class="form-label">Importance Level <span class="required">*</span></label>
                        <select class="form-control select2bs4" name="file_importance" id="importance_level" required>
                          <option value="" disabled selected>Choose Option</option>
                          <option value="Low" <?php if($file->file_importance == 'Low'){echo "selected";} ?>>Low</option>
                          <option value="Medium" <?php if($file->file_importance == 'Medium'){echo "selected";} ?>>Medium</option>
                          <option value="High" <?php if($file->file_importance == 'High'){echo "selected";} ?>>High</option>
                        </select>
                        @error('file_importance')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>
                  </div>

                  {{-- Buttons --}}
                  <div class="mb-3">
                    <button type="submit" class="btn btn-success w-100">Save Changes</button>
                  </div>
                </div>
              </form>
            </div> 
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  $(document).ready(function () {
    $('#bucket_id').on('change', function () {
      const bucketId = $(this).val();
      $('#folder_id').html('<option value="" selected disabled>Loading folders...</option>');

      $.ajax({
        url: "{{ url('/bucket-folders') }}",
        type: "POST",
        data: {
          bucket_id: bucketId,
        },
        dataType: "json",
        success: function (response) {
          $('#folder_id').empty().append('<option value="" selected disabled>Select Folder</option>');
          response.folders.forEach(folder => {
            $('#folder_id').append(`<option value="${folder.id}">${folder.folder_name}</option>`);
          });
        },
        error: function () {
          $('#folder_id').html('<option value="" selected disabled>Error loading folders</option>');
        }
      });
    });


    // Back button
    $('#backButton').on('click', function (e) {
      e.preventDefault();
      window.history.back();
    });
  });
</script>
@endpush
