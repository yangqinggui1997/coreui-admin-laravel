@extends('dashboard.authBase')

@section('content')

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card mx-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('user.processRegister') }}">
                    @csrf
                    @if (isset($lineId) && $lineId)
                        <input type="hidden" name="lineId" value="{{ $lineId }}"/>
                    @endif
                    <h1>{{ __('Register') }}</h1>
                    <p class="text-muted">Create your account</p>
                    @if(Session::has('errors'))
                      <div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> {{session()->get('errors')}}
                          <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                      </div>
                    @elseif (Session::has('success'))
                      <div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> {{session()->get('success')}}
                          <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                      </div>
                    @endif
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <svg class="c-icon">
                              <use xlink:href="../assets/icons/coreui/free-symbol-defs.svg#cui-user"></use>
                            </svg>
                          </span>
                        </div>
                        <input class="form-control" type="text" placeholder="{{ __('Your full name (required)') }}" name="name" id="name" value="{{ old('name') }}" required autofocus>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <svg class="c-icon">
                              <use xlink:href="../assets/icons/coreui/free-symbol-defs.svg#cui-screen-smartphone"></use>
                            </svg>
                          </span>
                        </div>
                        <input class="form-control" type="number" placeholder="{{ __('Phone number (required)') }}" name="phone" id="phone" value="{{ old('phone') }}" required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <svg class="c-icon">
                              <use xlink:href="../assets/icons/coreui/free-symbol-defs.svg#cui-envelope-open"></use>
                            </svg>
                          </span>
                        </div>
                        <input class="form-control" type="email" placeholder="{{ __('E-Mail Address (option)') }}" name="email" id="email" value="{{ old('email') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <svg class="c-icon">
                              <use xlink:href="../assets/icons/coreui/free-symbol-defs.svg#cui-credit-card"></use>
                            </svg>
                          </span>
                        </div>
                        <input class="form-control" type="text" placeholder="{{ __('Car plate (required)') }}" name="carPlate" id="carPlate" value="{{ old('carPlate') }}" required>
                    </div>
                    <button class="btn btn-block btn-success" type="submit">{{ __('Register') }}</button>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('javascript')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/user/register.js') }}"></script>
@endsection