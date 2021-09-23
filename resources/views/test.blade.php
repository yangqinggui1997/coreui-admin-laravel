@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Handler detect and extract text from image') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('emailResetPassword') }}" enctype="multipart/form-data" id="formReadTextImage" onsubmit="(e) => {e.preventDefault()}">
                        @csrf
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Choose image') }}</label>

                            <div class="col-md-6">
                                <input id="image" type="file" class="form-control" name="email">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-primary" id="uploadBtn">
                                    {{ __('Upload') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    $(function(){
        $('#uploadBtn').on('click', function(){
            let data = new FormData()
            if($('#image').prop('files').length)
            {
                const file = $('#image').prop('files')[0]
                data.append('image', file)
                $.ajax({
                    method: 'POST',
                    url: "{{route('readTextImage')}}",
                    data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        console.log(data)
                    },
                    error: (jqXHR, textStatus, errorThrown) => {
                        console.log(jqXHR, textStatus, errorThrown)
                    }
                })
            }
                
        })
    });
</script>
@endsection
