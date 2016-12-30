    <!-- Sidebar -->
        <div id="left-sidebar-wrapper" class="hide-phone">
        <ul class="sidebar-nav">
            @foreach([
                'dashboard',
                'clients',
                'products',
                'invoices',
                'payments',
                'recurring_invoices',
                'credits',
                'quotes',
                'tasks',
                'expenses',
                'vendors',
            ] as $option)
            @if (in_array($option, ['dashboard', 'settings'])
                || Auth::user()->can('view', substr($option, 0, -1))
                || Auth::user()->can('create', substr($option, 0, -1)))
                @include('partials.navigation_option')
            @endif
        @endforeach
        @if ( ! Utils::isNinjaProd())
            @foreach (Module::all() as $module)
                @include('partials.navigation_option', [
                    'option' => $module->getAlias(),
                    'icon' => $module->get('icon', 'th-large'),
                ])
            @endforeach
        @endif
        @include('partials.navigation_option', ['option' => 'settings'])
            <li style="width:100%">
                <div class="nav-footer">
                    <a href="{{ url(NINJA_CONTACT_URL) }}" target="_blank" title="{{ trans('texts.contact_us') }}">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <a href="{{ url(NINJA_FORUM_URL) }}" target="_blank" title="{{ trans('texts.support_forum') }}">
                        <i class="fa fa-list-ul"></i>
                    </a>
                    <a href="javascript:showKeyboardShortcuts()" target="_blank" title="{{ trans('texts.keyboard_shortcuts') }}">
                        <i class="fa fa-question-circle"></i>
                    </a>
                    <a href="{{ url(SOCIAL_LINK_FACEBOOK) }}" target="_blank" title="Facebook">
                        <i class="fa fa-facebook-square"></i>
                    </a>
                    <a href="{{ url(SOCIAL_LINK_TWITTER) }}" target="_blank" title="Twitter">
                        <i class="fa fa-twitter-square"></i>
                    </a>
                    <a href="{{ url(SOCIAL_LINK_GITHUB) }}" target="_blank" title="GitHub">
                        <i class="fa fa-github-square"></i>
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <!-- /#left-sidebar-wrapper -->