@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>
                      <label>{{ __('Users') }}</label>
                      <a href="{{ route('user.edit', 'create') }}" class="btn btn-success">Add</a>
                      @if(Session::has('errors'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> {{session()->get('errors')}}
                            <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div>
                      @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-striped" style="display: block; overflow: auto">
                        <thead>
                          <tr>
                            <th>Username</th>
                            <th>E-mail</th>
                            <th>Phone number</th>
                            <th>Car Plate</th>
                            <th>Line Id</th>
                            <th>Chatbot verified</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Email verified at</th>
                            <th>Last login</th>
                            <th></th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($users as $user)
                            <tr>
                              <td>{{ $user->displayName }}</td>
                              <td>{{ $user->email }}</td>
                              <td>{{ $user->phoneNumber }}</td>
                              <td>{{ $user->carPlate }}</td>
                              <td>{{ $user->lineId }}</td>
                              <td>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="chatbotVerified" disabled {{$user->chatbotVerified ? "checked" : ""}}>
                                </div>
                              </td>
                              <td>{{ $user->role }}</td>
                              <td>{!! $user->status ? '<label class="badge bg-success">Active</label>' : '<label class="badge bg-dark text-white">Locked</label>' !!}</td>
                              <td>{{ $user->emailVerifiedAt }}</td>
                              <td>{{ $user->lastLogin }}</td>
                              <td>
                                @if( $user->role !== 'master' )
                                <a href="{{ route('user.edit', $user->uid) }}" class="btn btn-block btn-primary">Edit</a>
                                @endif
                              </td>
                              <td>
                                @if( $you->sub !== $user->uid && $user->role !== 'master' )
                                <form action="{{ route('user.delete') }}" method="POST">
                                  <input type="hidden" value="{{ $user->uid }}" name="id" />
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-block btn-danger" data-type="btnDelete">Delete</button>
                                </form>
                                @endif
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
<script src="{{ asset('js/user/index.js') }}"></script>
@endsection

