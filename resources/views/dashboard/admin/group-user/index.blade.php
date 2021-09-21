@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>
                      <label>{{ __('Group users') }}</label>
                      <a href="{{ route('group.getById', 'create') }}" class="btn btn-success">Add</a>
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
                            <th>Name</th>
                            <th>Number of members</th>
                            <th></th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($groups as $group)
                            <tr>
                              <td>{{ $group->name }}</td>
                              <td>{{ $group->numberOfMembers }}</td>
                              <td>
                              @if($group->type > 1)
                                <a href="{{ route('group.getById', $group->id) }}" class="btn btn-block btn-primary">Edit</a>
                                @endif
                              </td>
                              <td>
                                @if($group->type > 1)
                                <form action="{{ route('group.delete')}}" method="POST">
                                    <input type="hidden" value="{{ $group->id }}" name="id" />
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

