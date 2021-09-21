@extends('dashboard.base')

@section('content')
<div class="container-fluid">
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-sm-12 col-md-12">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i>
            <label>{{ __('Post') }}</label>
            <a href="{{ route('post.getById', 'create') }}" class="btn btn-success">Add</a>
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
                  <th>Thumbnail</th>
                  <th>Title</th>
                  <th>Post by</th>
                  <th>Link</th>
                  <th>Author</th>
                  <th>Category</th>
                  <th>Displays</th>
                  <th>Views</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Updated</th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="tbody">
                @foreach($posts as $k => $post)
                <tr>
                  <td>{{ $k + 1 }}</td>
                  <td>
                    <img src="{{ $post->thumbnail ? $post->thumbnail : asset('assets/img/thumb.svg') }}" alt="Thumbnail" style="height:80px;" />
                  </td>
                  <td>{{ $post->title }}</td>
                  <td>{{ $post->post_user_name }}</td>
                  <td>@if($post->link) <a href="{{ $post->link }}">{{ $post->link }}</a> @else {{ "None" }} @endif</td>
                  <td>{{ $post->author }}</td>
                  <td>{{ $post->category }}</td>
                  <td>{{ number_format($post->amount_of_display, 0, '', ' ') }}</td>
                  <td>{{ number_format($post->amount_of_view, 0, '', ' ') }}</td>
                  <td>
                    @if ($post->status === 'created')
                      <label class="badge bg-primary text-white">Created</label>
                    @elseif ($post->status === 'published')
                      <label class="badge bg-success">Published</label>
                    @else
                      <label class="badge bg-dark text-white">Locked</label>
                    @endif
                  </td>
                  <td>{{ $post->created_at }}</td>
                  <td>{{ $post->updated_at }}</td>
                  <td>
                    <a href="{{ route('post.getById', $post->id) }}" class="btn btn-block btn-primary">Edit</a>
                  </td>
                  <td>
                    <form action="{{ route('post.delete') }}" method="POST">
                      <input type="hidden" value="{{ $post->id }}" name="id" />
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
<script src="{{ asset('js/post/index.js') }}"></script>
@endsection