@extends('admin_master')

@section('content')
<div class="container-fluid" id="container-wrapper">
  
  <!-- Page Header -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Bucket</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add Bucket</li>
    </ol>
  </div>

  <!-- Form Card -->
  <div class="card mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Add Bucket</h6>
    </div>
    <div class="card-body">
      <form action="{{ route('buckets.store') }}" method="POST">
        @csrf

        <!-- Category Name -->
        <div class="form-group">
          <label for="bucket_name">Bucket Name <span class="text-danger">*</span></label>
          <input 
            type="text" 
            name="bucket_name" 
            id="bucket_name" 
            class="form-control" 
            placeholder="Enter Bucket Name" 
            value="{{ old('bucket_name') }}" 
            required
          >
          @error('bucket_name')
            <div class="alert alert-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <!-- Status -->
        <div class="form-group">
          <label for="status">Status <span class="text-danger">*</span></label>
          <select name="status" id="status" class="form-control" required>
            <option value="" disabled selected>Select Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
          @error('status')
            <div class="alert alert-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <!-- Submit Button -->
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        
      </form>
    </div>
  </div>
</div>
@endsection
