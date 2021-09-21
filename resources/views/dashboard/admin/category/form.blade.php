@extends('dashboard.base')

@section('css')
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{ asset('css/dropdowntree.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <form action="{{ isset($category) && $category ? route('category.update') : route('category.create') }}" method="post" enctype='multipart/form-data'>
                    @if(isset($category) && $category) @method('PUT') @endif
                    @csrf
                    <input type="hidden" name="id" value="{{ old('id') ? old('id') : (isset($category) && $category ? $category->id : '') }}">
                    <div class="card">
                        <div class="card-header">{{ __('Category') }}</div>
                        <div class="card-body">
                            @if(Session::has('errors'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> {{session()->get('errors')}}
                                    <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input class="form-control" name="name" id="name" type="text" placeholder="Enter category name" value="{{ old('name') ? old('name') : (isset($category) && $category ? $category->name : '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="categoryParent">Category parent (option)</label>
                                        <input type="hidden" name="categoryParent" id="categoryParent" value="{{ old('categoryParent') ? old('categoryParent') : (isset($category) && $category ? $category->parent_id : '') }}">
                                        <input type="hidden" name="categoryParentName" id="categoryParentName" value="{{ old('categoryParentName') }}">
                                        <div class="dropdown dropdown-tree" id="categoryParentDropdown"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="thumbnail">Thumbnail</label>
                                        <input class="form-control" type="file" name="thumbnail" id="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <img id="thumbImage" src="{{ isset($category) && $category && $category->thumbnail ? $category->thumbnail : asset('assets/img/thumb.svg') }}" alt="Thumbnail" style="height: 80px;"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-success" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset" id="reset"> Reset</button>
                            <a href="{{ route('category.index') }}" class="btn btn-sm btn-primary">{{ __('Return') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/dropdowntree.js') }}"></script>
<script>
    let data = {!! json_encode($categories) !!};
</script>
<script src="{{ asset('js/category/form.js') }}"></script>
@endsection