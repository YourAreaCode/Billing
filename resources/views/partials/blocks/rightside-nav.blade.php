<div id="right-sidebar-wrapper" class="hide-phone" style="overflow-y:hidden">
	<ul class="sidebar-nav">
		{!! \App\Libraries\HistoryUtils::renderHtml(Auth::user()->account_id) !!}
	</ul>
</div>