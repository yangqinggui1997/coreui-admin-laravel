@extends('dashboard.base')

@section('content')
<div class="container-fluid">
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-sm-12 col-md-12">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i>
            <label>{{ __('Category') }}</label>
            <a href="{{ route('category.getById', 'create') }}" class="btn btn-success">Add</a>
            @if(Session::has('errors'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> {{session()->get('errors')}}
                  <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
              </div>
            @endif
          </div>
          <div class="card-body" id="card-body">
            <table class="table table-responsive-sm table-striped" id="table" style="display: block; overflow: auto">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Order</th>
                  <th>Parent Category</th>
                  <th>Thumbnail</th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="tbody">
                @foreach($category as $key => $cate)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $cate->name }}</td>
                    <td>{{ $cate->order }}</td>
                    <td>{{ $cate->parent }}</td>
                    <td>
                      <img src="{{ $cate->thumbnail ? $cate->thumbnail : asset('assets/img/thumb.svg') }}" alt="Thumbnail" style="height:80px;" />
                    </td>
                    <td>
                      <a href="{{ route('category.getById', $cate->id) }}" class="btn btn-block btn-primary">Edit</a>
                    </td>
                    <td>
                      <form action="{{ route('category.delete') }}" method="POST">
                        <input type="hidden" value="{{ $cate->id }}" name="id" />
                        @method('DELETE')
                        @csrf
                        <button class="btn btn-block btn-danger" data-type="btnDelete">Delete</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/category/index.js') }}"></script>
@endsection