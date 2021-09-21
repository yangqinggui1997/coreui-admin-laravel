@extends('dashboard.base')

@section('css')
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <form action="{{ isset($group) && $group ? route('group.update') : route('group.create') }}" method="post" enctype='multipart/form-data'>
                    @if(isset($group) && $group) @method('PUT') @endif
                    @csrf
                    <input type="hidden" name="id" value="{{ old('id') ? old('id') : (isset($group) && $group ? $group->id : '') }}">
                    <div class="card">
                        <div class="card-header">{{ __('User') }}</div>
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
                                        <input class="form-control" name="name" id="name" type="text" placeholder="Enter group name" value="{{ old('name') ? old('name') : (isset($group) && $group ? $group->name : '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-success" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset" id="reset"> Reset</button>
                            <a href="{{ route('group.index') }}" class="btn btn-sm btn-primary">{{ __('Return') }}</a>
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
<script src="{{ asset('js/group-user/form.js') }}"></script>
@endsection