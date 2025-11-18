@extends('layouts.app')

@section('content')

{{-- Fixed background + overlay --}}
<div class="auth-bg" aria-hidden="true"></div>

{{-- Fixed repeating landmark strip at the bottom --}}
<div class="auth-landmark" aria-hidden="true"></div>

<style>
  /* Full-page background with white overlay */
  .auth-bg{
    position: fixed;
    inset: 0; /* top:0; right:0; bottom:0; left:0 */
    background:
      linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9)),
      url('{{ asset('city_hall.jpg') }}') no-repeat center center fixed;
    background-size: cover;
    z-index: 0;
    pointer-events: none; /* don't block clicks */
  }

  /* Bottom landmark strip (repeat-x) */
  .auth-landmark{
    position: fixed;
    left: 0; right: 0; bottom: 0;
    height: 50px;            /* adjust strip height if needed */
    background: url('{{ asset('landmark.png') }}') repeat-x center bottom;
    background-size: auto 50px; /* scale height to 50px */
    z-index: 0;
    pointer-events: none;
  }

  /* Ensure the login card sits above the background */
  .login-box, .login-box .card { position: relative; z-index: 1; }
</style>

<div class="login-box">
    <div class="login-logo">
        <div class="login-logo">
            <a href="{{ route('admin.home') }}">
                {{ trans('panel.site_title') }}
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">
                {{ trans('global.login') }}
            </p>

            @if(session()->has('message'))
                <p class="alert alert-info">
                    {{ session()->get('message') }}
                </p>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required autocomplete="email" autofocus placeholder="{{ trans('global.login_email') }}" name="email" value="{{ old('email', null) }}">
                    @if($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="{{ trans('global.login_password') }}">
                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">{{ trans('global.remember_me') }}</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">
                            {{ trans('global.login') }}
                        </button>
                    </div>
                </div>
            </form>

            @if(Route::has('password.request'))
                <p class="mb-1">
                    <a href="{{ route('password.request') }}">
                        {{ trans('global.forgot_password') }}
                    </a>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
