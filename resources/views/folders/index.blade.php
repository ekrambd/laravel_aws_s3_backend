@extends('admin_master')

@section('content')
 <main class="app-main">
  <div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">All Folder</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Folder</li>
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
               <div class="card-header bg-primary text-light"><div class="card-title">All Folder</div></div>
                <div class="card-body">
               
                  <div class="table-responsive">
                    <a href="{{route('folders.create')}}" class="btn btn-success float-end"><i class="fa fa-plus"></i> Add New Folder</a><br/><br/>
                  	<table class="table table-striped table-bordered bg-info" id="folder-table">
                  	<thead>
                      <tr>
                       <th>Folder Name</th>
                       <th>Folder Path</th>
                       <th>Bucket</th>
                       <th>Status</th>	
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
  	let bucket_id;
  	var bucketTable = $('#folder-table').DataTable({
            searching: true,
            processing: true,
            serverSide: true,
            ordering: false,
            responsive: true,
            stateSave: true,
            ajax: {
                url: "{{ route('folders.index') }}"
            },
            columns: [
                { data: 'folder_name', name: 'folder_name' },
                { data: 'folder_slug', name: 'folder_slug' },
                { data: 'bucket', name: 'bucket' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
    });

  	$(document).on('click', '.delete-folder', function(e) {
        e.preventDefault();
        folder_id = $(this).data('id');
        if (confirm('Do you want to delete this folder?')) {
            $.ajax({
                url: "{{ url('/folders') }}/" + folder_id,
                type: "DELETE",
                dataType: "json",
                success: function(data) {
                	console.log(data);
                    bucketTable.ajax.reload(null, false);
                    toastr.success(data.message);
                }
            });
        }
    });

    $(document).on('click', '#status-folder-update', function(){
       folder_id = $(this).data('id');
       var isFolderchecked = $(this).prop('checked');
       status_val = isFolderchecked ? 'Active' : 'Inactive'; 
       $.ajax({

          url: "{{url('folder-status-update')}}",
          type:"POST",
          data:{'folder_id':folder_id, 'status':status_val},
          dataType:"json",
          success:function(data) {

              toastr.success(data.message);

              $('.data-table').DataTable().ajax.reload(null, false);

          },
                          
      });
    });

  });	
 </script>
@endpush