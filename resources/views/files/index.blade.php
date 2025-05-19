@extends('admin_master')

@section('content')
 <main class="app-main">
  <div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">All Files</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Files</li>
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
               <div class="card-header bg-primary text-light"><div class="card-title">All Files</div></div>
                <div class="card-body">
               
                  <div class="table-responsive">
                    <a href="{{route('files.create')}}" class="btn btn-success float-end"><i class="fa fa-plus"></i> Add New File</a><br/><br/>
                  	<table class="table table-striped table-bordered bg-info" id="file-table">
                  	<thead>
                      <tr>
                       <th>Title</th>
                       <th>Importance Level</th>
                       <th>Bucket</th>
                       <th>Folder</th>
                       <th>Size</th>
                       <th>File Type</th>
                       <th>Publication Status</th>
                       <th>Upload Status</th>	
                       <th>Action</th>
                      </tr>		
                  	</thead>
                  	<tbody class="conts"></tbody>
                  </table>
                  </div>

                </div>
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
  	let file_id;
  	var fileTable = $('#file-table').DataTable({
            searching: true,
            processing: true,
            serverSide: true,
            ordering: false,
            responsive: true,
            stateSave: true,
            ajax: {
                url: "{{ route('files.index') }}"
            },
            columns: [
                { data: 'title', name: 'title' },
                { data: 'file_importance', name: 'file_importance' },
                { data: 'bucket', name: 'bucket' },
                { data: 'folder', name: 'folder' },
                { data: 'size', name: 'size' },
                { data: 'extension', name: 'extension' },
                { data: 'status', name: 'status' },
                { data: 'upload_status', name: 'upload_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
    });

  	$(document).on('click', '.delete-file', function(e) {
        e.preventDefault();
        file_id = $(this).data('id');
        if (confirm('Do you want to delete this folder?')) {
            $.ajax({
                url: "{{ url('/files') }}/" + file_id,
                type: "DELETE",
                dataType: "json",
                success: function(data) {
                	console.log(data);
                    fileTable.ajax.reload(null, false);
                    toastr.success(data.message);
                }
            });
        }
    });

    $(document).on('click', '#status-file-update', function(){
       file_id = $(this).data('id');
       var isFilechecked = $(this).prop('checked');
       status_val = isFilechecked ? 'Active' : 'Inactive'; 
       $.ajax({

          url: "{{url('file-status-update')}}",
          type:"POST",
          data:{'file_id':file_id, 'status':status_val},
          dataType:"json",
          success:function(data) {
              console.log(data);
              toastr.success(data.message);

              $('.data-table').DataTable().ajax.reload(null, false);

          },
                          
      });
    });

  });	
 </script>
@endpush