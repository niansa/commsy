<div class="{literal}${baseClass}{/literal} widget_500">
	<div class="innerWidgetArea">
		<div class="widget_head">
			<div style="float:right; margin-right:10px;padding-top:5px;">
				___COMMON_SEARCH___
				<input data-dojo-attach-event="onkeyup:onChangeSearch"></input>
			</div>
			<h3 class="pop_widget_h3">___PRIVATEROOM_MY_ENTRIES_LIST_BOX___</h3>
		</div>
		<div class="widget_head">
			___COMMON_PAGE_ENTRIES___:
			<span class="cursor_pointer" data-dojo-attach-event="onclick:onClickPaging20" data-dojo-attach-point="paging20"><strong>20</strong></span> |
			<span class="cursor_pointer" data-dojo-attach-event="onclick:onClickPaging50" data-dojo-attach-point="paging50">50</span>
			
			<div class="float-right">
				___COMMON_PAGE___: <span data-dojo-attach-point="currentPageNode"></span> / <span  data-dojo-attach-point="maxPageNode"></span>
				<span class="cursor_pointer" data-dojo-attach-event="onclick:onClickPagingFirst">&lt;&lt;</span> |
				<span class="cursor_pointer" data-dojo-attach-event="onclick:onClickPagingPrev">&lt;</span> |
				<span class="cursor_pointer" data-dojo-attach-event="onclick:onClickPagingNext">&gt;</span> |
				<span class="cursor_pointer" data-dojo-attach-event="onclick:onClickPagingLast">&gt;&gt;</span>
			</div>
			
			<div class="clear"></div>
		</div>
		<div class="widget_body">
			<ul data-dojo-attach-point="itemList">
			</ul>
		</div>
	</div>
</div>