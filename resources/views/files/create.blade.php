@extends('admin_master')

@section('content')
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Add File</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Add File</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <div class="row g-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-primary text-light">
                <div class="card-title">Add File</div>
              </div>

              <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                  <div class="row">
                    {{-- Title --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="required">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Title" required value="{{ old('title') }}">
                        @error('title')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>

                    {{-- Storage Class --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="storage_class" class="form-label">Select Storage Class <span class="required">*</span></label>
                        <select class="form-control select2bs4" name="storage_class" id="storage_class" required>
                          <option value="" disabled selected>Select Storage Class</option>
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
                    </div>

                    {{-- Bucket --}}
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="bucket_id" class="form-label">Select Bucket <span class="required">*</span></label>
                        <select class="form-control select2bs4" name="bucket_id" id="bucket_id" required>
                          <option value="" disabled selected>Select Bucket</option>
                          @foreach (buckets() as $bucket)
                            <option value="{{ $bucket->id }}">{{ $bucket->bucket_name }}</option>
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
                        <label for="folder_id" class="form-label">Select Folder <span class="required">*</span></label>
                        <select class="form-control select2bs4" name="folder_id" id="folder_id" required>
                          <option value="" disabled selected>Select Folder</option>
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
                          <option value="Public">Public</option>
                          <option value="Private">Private</option>
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
                          <option value="Low">Low</option>
                          <option value="Medium">Medium</option>
                          <option value="High">High</option>
                        </select>
                        @error('file_importance')
                          <p class="alert alert-danger">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>
                  </div>

                  {{-- Buttons --}}
                  <div class="mb-3">
                    <button type="submit" class="btn btn-success w-100">Next Step <i class="fa fa-forward"></i></button>
                    <button type="button" class="btn btn-warning w-100 my-2 text-light" id="backButton">
                      <strong><i class="fa fa-backward"></i> Back to previous</strong>
                    </button>
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
    // Dynamically load folders for selected bucket
    $('#bucket_id').on('change', function () {
      const bucketId = $(this).val();
      $('#folder_id').html('<option value="" selected disabled>Loading folders...</option>');

      $.ajax({
        url: "{{ url('/bucket-folders') }}",
        type: "POST",
        data: {
          bucket_id: bucketId,
          _token: "{{ csrf_token() }}"
        },
        dataType: "json",
        success: function (folders) {
          $('#folder_id').empty().append('<option value="" selected disabled>Select Folder</option>');
          folders.forEach(folder => {
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
