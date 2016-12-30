@extends('layouts.auth')

@section('head')
	<link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('body')
	<div class="container">
	
	    @include('partials.warn_session', ['redirectTo' => '/login'])

        <!-- BEGIN LOGIN FORM -->
        {!! Former::open('login')
            ->rules(['email' => 'required|email', 'password' => 'required'])
            ->addClass('form-signin') !!}
            
	        {{ Former::populateField('remember', 'true') }}
            
            <h3 class="form-title font-green">Sign In</h3>
            
            
            @if (count($errors->all()))
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            @if (Session::has('warning'))
            <div class="alert alert-warning">{!! Session::get('warning') !!}</div>
            @endif

            @if (Session::has('message'))
            <div class="alert alert-info">{!! Session::get('message') !!}</div>
            @endif

            @if (Session::has('error'))
            <div class="alert alert-danger">{!! Session::get('error') !!}</div>
            @endif
            
            
            
            
            
            <div class="form-group">
                <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                <label>Email</label>
                {!! Former::text('email')->placeholder(trans('texts.email_address'))->raw() !!}
            </div>
            <div class="form-group">
                <label>Password</label>
                {!! Former::password('password')->placeholder(trans('texts.password'))->raw() !!}
            </div>
            <div class="form-actions">
            
                <button type='submit' class='btn btn-success btn-lg btn-block' id='loginButton'>Login</button>
                
                {!! Former::hidden('remember')->raw() !!}
                {!! link_to('/recover_password', trans('texts.recover_password'), ['class' => 'forget-password']) !!}
            </div>
            <div class="login-options">
                
                @if (Input::get('new_company') && Utils::allowNewAccounts())
	                {!! Former::hidden('link_accounts')->value('true') !!}
	                <center><p>- {{ trans('texts.or') }} -</p></center>
	                <p>{!! Button::primary(trans('texts.new_company'))->asLinkTo(URL::to('/invoice_now?new_company=true&sign_up=true'))->large()->submit()->block() !!}</p><br/>
	            @elseif (Utils::isOAuthEnabled())
	                <center><p>- {{ trans('texts.or') }} -</p></center>
	                <div class="row">
	                @foreach (App\Services\AuthService::$providers as $provider)
	                    <div class="col-md-6">
	                        <a href="{{ URL::to('auth/' . $provider) }}" class="btn btn-primary btn-block social-login-button" id="{{ strtolower($provider) }}LoginButton">
	                            <i class="fa fa-{{ strtolower($provider) }}"></i> &nbsp;
	                            {{ $provider }}
	                        </a><br/>
	                    </div>
	                @endforeach
	                </div>
	            @endif

            </div>
            <div class="create-account">
                <p>
                    <a href="javascript:;" id="register-btn" class="uppercase">Create an account</a>
                </p>
            </div>
            
        {!! Former::close() !!}
		
	</div>


    <script type="text/javascript">
        $(function() {
            if ($('#email').val()) {
                $('#password').focus();
            } else {
                $('#email').focus();
            }
        })
    </script>

@endsection
