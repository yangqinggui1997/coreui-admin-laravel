@extends('dashboard.base')


@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{ asset('css/dropdowntree.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <form action="{{ isset($post) && $post ? route('post.update') : route('post.create') }}" method="post" enctype='multipart/form-data'>
                    @if(isset($post) && $post) @method('PUT') @endif
                    @csrf
                    <input type="hidden" name="id" value="{{ old('id') ? old('id') : (isset($post) && $post ? $post->id : '') }}">
                    <div class="card">
                        <div class="card-header">{{ __('Post') }}</div>

                        <div class="card-body">
                            @if(Session::has('errors'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> {{session()->get('errors')}}
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input class="form-control" name="title" id="title" type="text" placeholder="Enter post title" value="{{ old('title') ? old('title') : (isset($post) && $post ? $post->title : '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="link">Link (option)</label>
                                        <input class="form-control" name="link" id="link" type="text" placeholder="Enter post link" value="{{ old('link') ? old('link') : (isset($post) && $post ? $post->link : '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="author">Author (option)</label>
                                        <input class="form-control" name="author" id="author" type="text" placeholder="Enter post author" value="{{ old('author') ? old('author') : (isset($post) && $post ? $post->author : '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="pageLinkName">Page link name (option)</label>
                                        <input class="form-control" name="pageLinkName" id="pageLinkName" type="text" placeholder="Enter page link name" value="{{ old('pageLinkName') ? old('pageLinkName') : (isset($post) && $post ? $post->page_link_name : '') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="categoryParent">Post category</label>
                                        <input type="hidden" name="categoryParent" id="categoryParent" value="{{ isset($post) && $post ? $post->category_id : old('categoryParent') }}">
                                        <input type="hidden" name="categoryParentName" id="categoryParentName" value="{{ old('categoryParentName') ? old('categoryParentName') : (isset($post) && $post ? $post->category_name : '') }}">
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
                                        <img id="thumbImage" src="{{ isset($post) && $post && $post->thumbnail ? $post->thumbnail : asset('assets/img/thumb.svg') }}" alt="Thumbnail" style="height: 80px;" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="content">Content</label>
                                        <textarea class="form-control" name="content" id="content" placeholder="Enter post content" rows="10" cols="80">{!! isset($post) && $post ? $post->content : old('content') !!}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="status">Status</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                @if(!old('status') && (!isset($post) || !$post))
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="publishSatus" value="published" checked>
                                                    <label class="form-check-label" for="publishSatus">
                                                        Publish
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="createdStatus" value="created">
                                                    <label class="form-check-label" for="createdStatus">
                                                        Unpublish
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="lockStatus" value="locked">
                                                    <label class="form-check-label" for="lockStatus">
                                                        Lock
                                                    </label>
                                                </div>
                                                @else
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="publishSatus" value="published" {{old('status') && old('status') === 'published' ? 'checked' : (isset($post) && $post && $post->status === 'published' ? 'checked' : '')}}>
                                                    <label class="form-check-label" for="publishSatus">
                                                        Publish
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="createdStatus" value="created" {{old('status') && old('status') === 'created' ? 'checked' : (isset($post) && $post && $post->status === 'created' ? 'checked' : '')}}>
                                                    <label class="form-check-label" for="createdStatus">
                                                        Unpublish
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="lockStatus" value="locked" {{old('status') && old('status') === 'locked' ? 'checked' : (isset($post) && $post && $post->status === 'locked' ? 'checked' : '')}}>
                                                    <label class="form-check-label" for="lockStatus">
                                                        Lock
                                                    </label>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-12 col-form-label" for="groupUserSelect">Select group user see this post</label>
                                <div class="col-md-12">
                                    @if (old('groupUser'))
                                        @foreach (old('groupUser') as $group)
                                            <input type="hidden" name="groupUser[]" value="{{$group}}">
                                        @endforeach
                                    @elseif (isset($post) && $post->groupUser)
                                        @foreach ($post->groupUser as $group)
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
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-success" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset" id="reset"> Reset</button>
                            <a href="{{ route('post.index') }}" class="btn btn-sm btn-primary">{{ __('Return') }}</a>
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
<script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/29.1.0/classic/ckeditor.js"></script>
<script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>
<script>
    const ckEditorUploadUrl = "{{ route('uploadCKeditor', ['_token' => csrf_token() ]) }}";
    const data = {!!json_encode($categories) !!};
    const thumbUrl = "{{asset('assets/img/thumb.svg')}}";
    const groupUser = {!!json_encode(old('groupUser') ? old('groupUser') : (isset($post) && $post->groupUser ? $post->groupUser : [])) !!}
</script>
<script src="{{ asset('js/post/form.js') }}"></script>
@endsection