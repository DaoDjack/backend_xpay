@extends($activeTemplate.'layouts.common_auth')
@php
    $content = @getContent('agent_login.content',true)->data_values;
@endphp
@section('content')
    <section class="account-section">
      <div class="left">
        <div class="left-inner w-100">
          <div class="text-center">
            <a class="site-logo" href="{{route('home')}}"><img src="{{getImage(getFilePath('logoIcon') .'/dark_logo.png')}}" alt="logo"></a>
          </div>
          <form class="account-form mt-4" method="POST" action="{{ route('agent.login')}}" onsubmit="return submitUserForm();">
              @csrf
            <div class="form-group">
              <label>@lang('Username Or Email')</label>
              <input type="text" name="username" placeholder="@lang('Enter username or email address')" class="form--control" required value="{{old('username')}}">
            </div>
            <div class="form-group">
              <label>@lang('Password')</label>
              <input type="password" name="password" placeholder="@lang('Enter password')" class="form--control" required>
            </div>

            <x-captcha></x-captcha>

            <div class="form-group">
              
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn--base w-100">@lang('Login')</button>
            </div>
          </form>
          
        </div>
      </div>
      <div class="right bg_img" style="background-image: url('{{getImage('assets/images/frontend/agent_login/'.@$content->background_image,'768x1200')}}');">
      </div>
    </section>
@endsection
