@extends($activeTemplate .'layouts.frontend')
@php
    $content = @getContent('login.content', true)->data_values;
@endphp
@section('content')
    <section class="pt-100 pb-100 d-flex flex-wrap align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="account-wrapper">
                        <div 
                            class="left bg_img" 
                            style="background-image: url('{{ getImage('assets/images/frontend/login/' . @$content->background_image, '768x1200') }}');"
                        >
                        </div>
                        <div class="right">
                            <div class="inner">
                                <div class="text-center">
                                    <h2 class="title">{{ __($pageTitle) }}</h2>
                                    <p class="font-size--14px mt-1 fw-bold">@lang('Welcome to') {{ __($general->site_name) }}</p>
                                </div>
                                <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha account-form mt-5">
                                    @csrf
                                    <div class="form-group">
                                        <label>@lang('Username or Email')</label>
                                        <input 
                                            type="text" 
                                            name="username" 
                                            placeholder="@lang('Enter username or email address')"
                                            class="form--control" 
                                            required 
                                            value="{{ old('username') }}"
                                        >
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('Password')</label>
                                        <input 
                                            type="password" 
                                            name="password" 
                                            placeholder="@lang('Enter password')"
                                            class="form--control" 
                                            required
                                        >
                                    </div>

                                    <x-captcha />

                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Login')</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection