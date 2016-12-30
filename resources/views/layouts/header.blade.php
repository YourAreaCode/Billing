@extends('layouts.master')


@section('head')

  <link href="{{ asset('css/built.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>
  <style type="text/css">
    @if (Auth::check() && Auth::user()->dark_mode)
        body {
            background: #000 !important;
            color: white !important;
        }

        .panel-body {
            background: #272822 !important;
            /*background: #e6e6e6 !important;*/
        }

        .panel-default {
            border-color: #444;
        }
    @endif

  </style>

<script type="text/javascript">

  @if (!Auth::check() || !Auth::user()->registered)
  function validateSignUp(showError)
  {
    var isFormValid = true;
    $(['first_name','last_name','email','password']).each(function(i, field) {
      var $input = $('form.signUpForm #new_'+field),
      val = $.trim($input.val());
      var isValid = val && val.length >= (field == 'password' ? 6 : 1);
      if (isValid && field == 'email') {
        isValid = isValidEmailAddress(val);
      }
      if (isValid) {
        $input.closest('div.form-group').removeClass('has-error').addClass('has-success');
      } else {
        isFormValid = false;
        $input.closest('div.form-group').removeClass('has-success');
        if (showError) {
          $input.closest('div.form-group').addClass('has-error');
        }
      }
    });

    if (!$('#terms_checkbox').is(':checked')) {
      isFormValid = false;
    }

    $('#saveSignUpButton').prop('disabled', !isFormValid);

    return isFormValid;
  }

  function validateServerSignUp()
  {
    if (!validateSignUp(true)) {
      return;
    }

    $('#signUpDiv, #signUpFooter').hide();
    $('#working').show();

    $.ajax({
      type: 'POST',
      url: '{{ URL::to('signup/validate') }}',
      data: 'email=' + $('form.signUpForm #new_email').val(),
      success: function(result) {
        if (result == 'available') {
          submitSignUp();
        } else {
          $('#errorTaken').show();
          $('form.signUpForm #new_email').closest('div.form-group').removeClass('has-success').addClass('has-error');
          $('#signUpDiv, #signUpFooter').show();
          $('#working').hide();
        }
      }
    });
  }

  function submitSignUp() {
    $.ajax({
      type: 'POST',
      url: '{{ URL::to('signup/submit') }}',
      data: 'new_email=' + encodeURIComponent($('form.signUpForm #new_email').val()) +
      '&new_password=' + encodeURIComponent($('form.signUpForm #new_password').val()) +
      '&new_first_name=' + encodeURIComponent($('form.signUpForm #new_first_name').val()) +
      '&new_last_name=' + encodeURIComponent($('form.signUpForm #new_last_name').val()) +
      '&go_pro=' + $('#go_pro').val(),
      success: function(result) {
        if (result) {
          handleSignedUp();
          NINJA.isRegistered = true;
          $('#signUpButton').hide();
          $('#myAccountButton').html(result);
        }
        $('#signUpSuccessDiv, #signUpFooter, #closeSignUpButton').show();
        $('#working, #saveSignUpButton').hide();
      }
    });
  }
  @endif

  function handleSignedUp() {
      localStorage.setItem('guest_key', '');
      fbq('track', 'CompleteRegistration');
      trackEvent('/account', '/signed_up');
  }

  function checkForEnter(event)
  {
    if (event.keyCode === 13){
      event.preventDefault();
      validateServerSignUp();
      return false;
    }
  }

  function logout(force)
  {
    if (force) {
      NINJA.formIsChanged = false;
    }

    if (force || NINJA.isRegistered) {
      window.location = '{{ URL::to('logout') }}';
    } else {
      $('#logoutModal').modal('show');
    }
  }

  function showSignUp() {
    $('#signUpModal').modal('show');
  }

  function hideSignUp() {
    $('#signUpModal').modal('hide');
  }

  function hideMessage() {
    $('.alert-info').fadeOut();
    $.get('/hide_message', function(response) {
      console.log('Reponse: %s', response);
    });
  }

  function setSignupEnabled(enabled) {
    $('.signup-form input[type=text]').prop('disabled', !enabled);
    if (enabled) {
        $('.signup-form a.btn').removeClass('disabled');
    } else {
        $('.signup-form a.btn').addClass('disabled');
    }
  }

  function setSocialLoginProvider(provider) {
    localStorage.setItem('auth_provider', provider);
  }

  window.loadedSearchData = false;
  function onSearchBlur() {
      $('#search').typeahead('val', '');
  }

  function onSearchFocus() {
    $('#search-form').show();

    if (!window.loadedSearchData) {
        window.loadedSearchData = true;
        trackEvent('/activity', '/search');
        var request = $.get('{{ URL::route('get_search_data') }}', function(data) {
          $('#search').typeahead({
            hint: true,
            highlight: true,
          }
          @if (Auth::check() && Auth::user()->account->custom_client_label1)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ Auth::user()->account->custom_client_label1 }}'], 'tokens'),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ Auth::user()->account->custom_client_label1 }}</span>'
            }
          }
          @endif
          @if (Auth::check() && Auth::user()->account->custom_client_label2)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ Auth::user()->account->custom_client_label2 }}'], 'tokens'),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ Auth::user()->account->custom_client_label2 }}</span>'
            }
          }
          @endif
          @foreach (['clients', 'contacts', 'invoices', 'quotes', 'navigation'] as $type)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ $type }}'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ trans("texts.{$type}") }}</span>'
            }
          }
          @endforeach
          ).on('typeahead:selected', function(element, datum, name) {
            window.location = datum.url;
          }).focus();
        });

        request.error(function(httpObj, textStatus) {
            // if the session has expried show login page
            if (httpObj.status == 401) {
                location.reload();
            }
        });
    }
  }

  $(function() {
    window.setTimeout(function() {
        $(".alert-hide").fadeOut();
    }, 3000);

    /* Set the defaults for Bootstrap datepicker */
    $.extend(true, $.fn.datepicker.defaults, {
        weekStart: {{ Session::get('start_of_week') }}
    });

    if (isStorageSupported()) {
      @if (Auth::check() && !Auth::user()->registered)
      localStorage.setItem('guest_key', '{{ Auth::user()->password }}');
      @endif
    }

    @if (!Auth::check() || !Auth::user()->registered)
    validateSignUp();

    $('#signUpModal').on('shown.bs.modal', function () {
      trackEvent('/account', '/view_sign_up');
      $(['first_name','last_name','email','password']).each(function(i, field) {
        var $input = $('form.signUpForm #new_'+field);
        if (!$input.val()) {
          $input.focus();
          return false;
        }
      });
    })
    @endif

    @if (Auth::check() && !Utils::isNinja() && !Auth::user()->registered)
      $('#closeSignUpButton').hide();
      showSignUp();
    @elseif(Session::get('sign_up') || Input::get('sign_up'))
      showSignUp();
    @endif

    $('ul.navbar-settings, ul.navbar-search').hover(function () {
        if ($('.user-accounts').css('display') == 'block') {
            $('.user-accounts').dropdown('toggle');
        }
    });

    @yield('onReady')

    @if (Input::has('focus'))
        $('#{{ Input::get('focus') }}').focus();
    @endif

    // Ensure terms is checked for sign up form
    @if (Auth::check() && !Auth::user()->registered)
        setSignupEnabled(false);
        $("#terms_checkbox").change(function() {
            setSignupEnabled(this.checked);
        });
    @endif

    // Focus the search input if the user clicks forward slash
    $('#search').focusin(onSearchFocus);
    $('#search').blur(onSearchBlur);

    // manage sidebar state
    function setupSidebar(side) {
        $("#" + side + "-menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled-" + side);

            var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';
            $.post('{{ url('save_sidebar_state') }}?show_' + side + '=' + toggled);

            if (isStorageSupported()) {
                localStorage.setItem('show_' + side + '_sidebar', toggled);
            }
        });

        if (isStorageSupported()) {
            var storage = localStorage.getItem('show_' + side + '_sidebar') || '0';
            var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';

            if (storage != toggled) {
                setTimeout(function() {
                    $("#wrapper").toggleClass("toggled-" + side);
                    $.post('{{ url('save_sidebar_state') }}?show_' + side + '=' + storage);
                }, 200);
            }
        }
    }

    @if ( ! Utils::isTravis())
        setupSidebar('left');
        setupSidebar('right');
    @endif

    // auto select focused nav-tab
    if (window.location.hash) {
        setTimeout(function() {
            $('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
        }, 1);
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href") // activated tab
        if (history.pushState) {
            history.pushState(null, null, target);
        }
    });

  });

</script>

@stop

@section('body')

@if ( ! Request::is('settings/account_management'))
  @include('partials.upgrade_modal')
@endif

@include('partials.blocks.top-nav')

<div id="wrapper" class='{!! session(SESSION_LEFT_SIDEBAR) ? 'toggled-left' : '' !!} {!! session(SESSION_RIGHT_SIDEBAR, true) ? 'toggled-right' : '' !!}'>

    @include('partials.blocks.leftside-nav')

    @include('partials.blocks.rightside-nav')

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">

			@include('partials.warn_session', ['redirectTo' => '/dashboard'])
			
			@if (Session::has('warning'))
				<div class="alert alert-warning">{!! Session::get('warning') !!}</div>
			@endif
			
			@if (Session::has('message'))
				<div class="alert alert-info alert-hide">
					{{ Session::get('message') }}
				</div>
			@elseif (Session::has('news_feed_message'))
				<div class="alert alert-info">
				{!! Session::get('news_feed_message') !!}
				<a href="#" onclick="hideMessage()" class="pull-right">{{ trans('texts.hide') }}</a>
				</div>
			@endif
			
			@if (Session::has('error'))
				<div class="alert alert-danger">{!! Session::get('error') !!}</div>
			@endif
			
			@if (!isset($showBreadcrumbs) || $showBreadcrumbs)
				{!! Form::breadcrumbs((isset($entity) && $entity->exists) ? $entity->present()->statusLabel : false) !!}
			@endif
			
			@yield('content')
		

        </div>
    </div>
</div>


@if (!Auth::check() || !Auth::user()->registered)
	@include('partials.modals.signup')
	@include('partials.modals.logout')
@endif

@include('partials.keyboard_shortcuts')

</div>

<p>&nbsp;</p>


@stop
