@extends($activeTemplate.'layouts.common_auth')
@php
    $content = getContent('merchant_login.content',true)->data_values;
@endphp
@section('content') 
    <section class="account-section" >
       <div class="left">
         <div class="left-inner">
            <div class="text-center">
              <a class="site-logo" href="{{route('home')}}"><img src="{{getImage(getFilePath('logoIcon') .'/dark_logo.png')}}" alt="@lang('logo')"></a>
            </div>
            <form class="account-form mt-4" method="POST" action="{{ route('merchant.login')}}">
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
                <button type="submit" class="btn btn--base w-100">@lang('Login')</button>
              </div>
            </form>
            
         </div>
       </div>
       <div class="right bg_img" style="background-image: url('{{getImage('assets/images/frontend/merchant_login/'.@$content->background_image,'1920x1280')}}');"></div>
    </section>
@endsection
