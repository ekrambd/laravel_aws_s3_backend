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
              <div class="card-body">
                <form id="uploadForm">
                  <div class="mb-3">
                    <label for="file" class="form-label"><b>Upload File</b></label><br/>
                    <input type="file" id="browseButton" style="display: none;">
                    <button type="button" id="browseTrigger" class="btn btn-success">Choose File</button>
                    <button type="button" id="cancelUpload" class="btn btn-danger d-none">Cancel Upload</button>
                  </div>

                  <div class="mb-3">
                    <div class="progress">
                      <div id="uploadProgress" class="progress-bar bg-info" role="progressbar"
                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                  </div>
                </form>
              </div>
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
    let file_id = "{{$id}}";
    var r = new Resumable({
        target: '{{ route("upload.file") }}',
        query: {
          _token: '{{ csrf_token() }}',
          file_id: file_id
        },
        chunkSize: 5 * 1024 * 1024, // 5MB
        simultaneousUploads: 3,
        testChunks: false,
        throttleProgressCallbacks: 1
    });

    r.assignBrowse($('#browseButton')[0]);

    $('#browseTrigger').click(function () {
        $('#browseButton').click();
    });

    $('#cancelUpload').click(function () {
        r.cancel(); // Stop uploading

        // Tell server to delete chunks
        $.ajax({
            url: '{{ route("upload.cancel") }}',
            type: 'POST',
            data: {
              _token: '{{ csrf_token() }}',
              file_id: file_id
            },
            success: function () {
                $('#uploadProgress')
                    .removeClass('bg-info bg-success bg-danger')
                    .addClass('bg-warning')
                    .text('Upload Canceled!')
                    .css('width', '100%');
            },
            error: function () {
                $('#uploadProgress')
                    .addClass('bg-danger')
                    .text('Error cleaning up chunks!');
            }
        });

        $('#cancelUpload').addClass('d-none');
    });

    r.on('fileAdded', function (file) {
        $('#uploadProgress')
            .css('width', '0%')
            .attr('aria-valuenow', 0)
            .text('0%')
            .removeClass('bg-success bg-danger')
            .addClass('bg-info');

        $('#cancelUpload').removeClass('d-none');
        r.upload();
    });

    r.on('fileProgress', function (file) {
        var percent = Math.floor(file.progress() * 100);
        $('#uploadProgress')
            .css('width', percent + '%')
            .attr('aria-valuenow', percent)
            .text(percent + '%');
    });

    r.on('fileSuccess', function (file, response) {
      let redirectURL = "{{url('/')}}/files";
        $('#uploadProgress')
            .removeClass('bg-info')
            .addClass('bg-success')
            .text('Upload Complete!');
            setTimeout(function(){
              window.location.href=`${redirectURL}`;
            }, 1000);
        $('#cancelUpload').addClass('d-none');
    });

    r.on('fileError', function (file, response) {
        $('#uploadProgress')
            .removeClass('bg-info')
            .addClass('bg-danger')
            .text('Upload Failed!');
        $('#cancelUpload').addClass('d-none');
    });
});
</script>
@endpush
