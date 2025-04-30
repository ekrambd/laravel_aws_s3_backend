@extends('admin_master')

@section('content')
 <main class="app-main">
  <div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">All Bucket</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Bucket</li>
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
               <div class="card-header bg-primary text-light"><div class="card-title">All Bucket</div></div>
                <div class="card-body">
               
                  <div class="table-responsive">
                    <a href="{{route('buckets.create')}}" class="btn btn-success float-end"><i class="fa fa-plus"></i> Add New Bucket</a><br/><br/>
                  	<table class="table table-striped table-bordered bg-info" id="bucket-table">
                  	<thead>
                      <tr>
                       <th>Bucket Name</th>
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
  	var bucketTable = $('#bucket-table').DataTable({
            searching: true,
            processing: true,
            serverSide: true,
            ordering: false,
            responsive: true,
            stateSave: true,
            ajax: {
                url: "{{ route('buckets.index') }}"
            },
            columns: [
                { data: 'bucket_name', name: 'bucket_name' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
    });

  	$(document).on('click', '.delete-bucket', function(e) {
        e.preventDefault();
        bucket_id = $(this).data('id');
        if (confirm('Do you want to delete this bucket?')) {
            $.ajax({
                url: "{{ url('/buckets') }}/" + bucket_id,
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

  });	
 </script>
@endpush