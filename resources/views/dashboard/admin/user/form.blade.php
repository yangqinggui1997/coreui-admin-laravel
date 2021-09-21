@extends('dashboard.base')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <form action="{{ isset($user) && $user ? route('user.update') : route('user.create') }}" method="post" enctype='multipart/form-data'>
                    @if(isset($user) && $user) @method('PUT') @endif
                    @csrf
                    <input type="hidden" name="id" value="{{ old('id') ? old('id') : (isset($user) && $user ? $user->uid : '') }}">
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
                                        <input class="form-control" name="name" id="name" type="text" placeholder="Enter user name" value="{{ old('name') ? old('name') : (isset($user) && $user ? $user->displayName : '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input class="form-control" name="email" id="email" type="email" placeholder="Enter email" value="{{ old('email') ? old('email') : (isset($user) && $user ? $user->email : '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="phone">Phone number</label><br>
                                        <input type="hidden" name="phone" value="{{ old('phone') ? old('phone') : (isset($user) && $user ? $user->phoneNumber : '')}}" id="inputPhone">
                                        <input class="form-control" id="phone" type="number" placeholder="Enter phone number"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="status">Status (check for active, uncheck for lock)</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="c-switch c-switch-label c-switch-success">
                                                    <input class="c-switch-input" type="checkbox" name="status" id="status" {{old('status') ? "checked" : (isset($user) && $user && $user->status ? "checked" : "")}}><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (isset($user) && $user && $user->role !== "admin" && $user->role !== "master")
                            <div class="form-group row">
                                <label class="col-md-12 col-form-label" for="groupUserSelect">Select group user see this post</label>
                                <div class="col-md-12">
                                    @if (old('groupUser'))
                                        @foreach (old('groupUser') as $group)
                                            <input type="hidden" name="groupUser[]" value="{{$group}}">
                                        @endforeach
                                    @elseif ($user->groupUser)
                                        @foreach ($user->groupUser as $group)
                                            <input type="hidden" name="groupUser[]" value="{{$group}}">
                                        @endforeach
                                    @endif
                                    <select id="groupUserSelect" name="groupUserSelect" multiple>
                                        @foreach ($groupUser as $group)
                                            <option value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-success" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset" id="reset"> Reset</button>
                            <a href="{{ route('user.index') }}" class="btn btn-sm btn-primary">{{ __('Return') }}</a>
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
<script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
    const country = "{{ $country ? $country : 'tw' }}";
    const oldPhone = "{{ old('phone') ? old('phone') : (isset($user) && $user ? $user->phoneNumber : '')}}";
    @if (isset($user) && $user && $user->role !== "admin" && $user->role !== "master")
        const groupUser = {!!json_encode(old('groupUser') ? old('groupUser') : ($user->groupUser ? $user->groupUser : [])) !!}
    @endif
</script>
<script src="{{ asset('js/user/form.js') }}"></script>
@endsection