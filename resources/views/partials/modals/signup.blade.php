<div class="modal fade" id="signUpModal" tabindex="-1" role="dialog" aria-labelledby="signUpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">{{ trans('texts.sign_up') }}</h4>
      </div>

      <div style="background-color: #fff; padding-right:20px" id="signUpDiv" onkeyup="validateSignUp()" onclick="validateSignUp()" onkeydown="checkForEnter(event)">
        <br/>

        {!! Former::open('signup/submit')->addClass('signUpForm')->autocomplete('on') !!}

        @if (Auth::check())
        {!! Former::populateField('new_first_name', Auth::user()->first_name) !!}
        {!! Former::populateField('new_last_name', Auth::user()->last_name) !!}
        {!! Former::populateField('new_email', Auth::user()->email) !!}
        @endif

        <div style="display:none">
          {!! Former::text('path')->value(Request::path()) !!}
          {!! Former::text('go_pro') !!}
        </div>


        <div class="row signup-form">
            <div class="col-md-11 col-md-offset-1">
                {!! Former::checkbox('terms_checkbox')->label(' ')->text(trans('texts.agree_to_terms', ['terms' => '<a href="'.URL::to('terms').'" target="_blank">'.trans('texts.terms_of_service').'</a>']))->raw() !!}
                <br/>
            </div>
            @if (Utils::isOAuthEnabled())
                <div class="col-md-4 col-md-offset-1">
                    <h4>{{ trans('texts.sign_up_using') }}</h4><br/>
                    @foreach (App\Services\AuthService::$providers as $provider)
                    <a href="{{ URL::to('auth/' . $provider) }}" class="btn btn-primary btn-block"
                        onclick="setSocialLoginProvider('{{ strtolower($provider) }}')" id="{{ strtolower($provider) }}LoginButton">
                        <i class="fa fa-{{ strtolower($provider) }}"></i> &nbsp;
                        {{ $provider }}
                    </a>
                    @endforeach
                </div>
                <div class="col-md-1">
                    <div style="border-right:thin solid #CCCCCC;height:110px;width:8px;margin-bottom:10px;"></div>
                    {{ trans('texts.or') }}
                    <div style="border-right:thin solid #CCCCCC;height:110px;width:8px;margin-top:10px;"></div>
                </div>
                <div class="col-md-6">
            @else
                <div class="col-md-12">
            @endif
                {{ Former::setOption('TwitterBootstrap3.labelWidths.large', 1) }}
                {{ Former::setOption('TwitterBootstrap3.labelWidths.small', 1) }}

                {!! Former::text('new_first_name')
                        ->placeholder(trans('texts.first_name'))
                        ->autocomplete('given-name')
                        ->label(' ') !!}
                {!! Former::text('new_last_name')
                        ->placeholder(trans('texts.last_name'))
                        ->autocomplete('family-name')
                        ->label(' ') !!}
                {!! Former::text('new_email')
                        ->placeholder(trans('texts.email'))
                        ->autocomplete('email')
                        ->label(' ') !!}
                {!! Former::password('new_password')
                        ->placeholder(trans('texts.password'))
                        ->label(' ') !!}

                {{ Former::setOption('TwitterBootstrap3.labelWidths.large', 4) }}
                {{ Former::setOption('TwitterBootstrap3.labelWidths.small', 4) }}
            </div>

            <div class="col-md-11 col-md-offset-1">
                @if (Utils::isNinja())
                    <div style="padding-top:20px;padding-bottom:10px;">{{ trans('texts.trial_message') }}</div>
                @endif
            </div>
        </div>

        {!! Former::close() !!}



        <center><div id="errorTaken" style="display:none">&nbsp;<br/>{{ trans('texts.email_taken') }}</div></center>
        <br/>

      </div>

      <div style="padding-left:40px;padding-right:40px;display:none;min-height:130px" id="working">
        <h3>{{ trans('texts.working') }}...</h3>
        <div class="progress progress-striped active">
          <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
        </div>
      </div>

      <div style="background-color: #fff; padding-right:20px;padding-left:20px; display:none" id="signUpSuccessDiv">
        <br/>
        <h3>{{ trans('texts.success') }}</h3>
        @if (Utils::isNinja())
          {{ trans('texts.success_message') }}
        @endif
        <br/>&nbsp;
      </div>

      <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
        <button type="button" class="btn btn-default" id="closeSignUpButton" data-dismiss="modal">{{ trans('texts.close') }} <i class="glyphicon glyphicon-remove-circle"></i></button>
        <button type="button" class="btn btn-primary" id="saveSignUpButton" onclick="validateServerSignUp()" disabled>{{ trans('texts.save') }} <i class="glyphicon glyphicon-floppy-disk"></i></button>
      </div>
    </div>
  </div>
</div>