{extends file="room_html.tpl"}

{block name=room_site_actions}
	<a href="" title="___COMMON_LIST_PRINTVIEW___">
		<img src="{$basic.tpl_path}img/btn_print.gif" alt="___COMMON_LIST_PRINTVIEW___" />
	</a>
    <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct=edit&iid=NEW" title="___COMMON_NEW_ITEM___">
    	<img src="{$basic.tpl_path}img/btn_add_new.gif" alt="___COMMON_NEW_ITEM___" />
    </a>
{/block}

{block name=room_navigation_rubric_title}
	___COMMON_{$room.rubric|upper}_INDEX___
	<span>(___COMMON_ENTRIES___: {$list.page_text_fragments.count_entries})</span>
{/block}

{block name=room_list_footer}
	<div class="content_item"> <!-- Start content_item -->
		<div class="item_info">
			<div class="ii_left">
			 	<div id="item_action">
			 		<input type="checkbox" name="" value="" /> ___ALL___
			 		<select name="index_view_action" size="1">
				 		<option value="-1">Aktion w&auml;hlen</option>
				 		<option disabled="disabled">------------------------------</option>
				 		<option value="1">___COMMON_LIST_ACTION_MARK_AS_READ___</option>
				 		<option value="2">___COMMON_LIST_ACTION_COPY___</option>
				 		<option value="download">___COMMON_LIST_ACTION_DOWNLOAD___</option>
				 		<option disabled="disabled">------------------------------</option>
				 		<option disabled="disabled">___COMMON_LIST_ACTION_DELETE___</option>
				 	</select>
					 	<input type="image" src="{$basic.tpl_path}img/btn_go.gif" alt="___COMMON_LIST_ACTION_BUTTON_GO___" />
				 </div>
			</div>
				<div class="ii_right">
				<p>0 Eintr&auml;ge ausgew&auml;hlt</p>
			</div>
				<div class="clear"> </div>
		</div>
	</div> <!-- Ende content_item -->
	<div class="content_item"> <!-- Start content_item -->
		<div class="item_info">
			<div class="ii_left">
				<p>___COMMON_PAGE_ENTRIES___
					{if $list.list_entries_parameter.20 == 'disabled'}
						<a href=""><strong>20</strong></a>
					{else}
					   <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.list_entries_parameter.20}">20</a>
					{/if}
					|
					{if $list.list_entries_parameter.50 == 'disabled'}
						<a href=""><strong>50</strong></a>
					{else}
					   <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.list_entries_parameter.50}">50</a>
					{/if}
					|
					{if $list.list_entries_parameter.all == 'disabled'}
						<a href=""><strong>___COMMON_ALL_ENTRIES___</strong></a>
					{else}
					   <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.list_entries_parameter.all}">___COMMON_ALL_ENTRIES___</a>
					{/if}
				</p>
			</div>
			<div class="ii_right">
				<div id="item_navigation">
				    {if $list.browsing_parameters.browse_start != "disabled"}
					   <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.browsing_parameters.browse_start}"><img src="{$basic.tpl_path}img/btn_ar_start.gif" alt="Start" /></a>
					{else}
					   <a><img src="{$basic.tpl_path}img/btn_ar_start.gif" alt="Start" /></a>
					{/if}
				    {if $list.browsing_parameters.browse_left != "disabled"}
					   <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.browsing_parameters.browse_left}"><img src="{$basic.tpl_path}img/btn_ar_left.gif" alt="zur&uuml;ck" /></a>
					{else}
					   <a><img src="{$basic.tpl_path}img/btn_ar_left.gif" alt="zur&uuml;ck" /></a>
					{/if}
					___COMMON_PAGE___ {$list.browsing_parameters.actual_page_number} / {$list.browsing_parameters.page_numbers}
				    {if $list.browsing_parameters.browse_right != "disabled"}
					   <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.browsing_parameters.browse_right}"><img src="{$basic.tpl_path}img/btn_ar_right.gif" alt="weiter" /></a>
					{else}
					   <a><img src="{$basic.tpl_path}img/btn_ar_right.gif" alt="weiter" /></a>
					{/if}
				    {if $list.browsing_parameters.browse_end != "disabled"}
					   <a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.browsing_parameters.browse_end}"><img src="{$basic.tpl_path}img/btn_ar_end.gif" alt="Ende" /></a>
					{else}
					   <a><img src="{$basic.tpl_path}img/btn_ar_end.gif" alt="Ende" /></a>
					{/if}
				</div>
			</div>
			<div class="clear"> </div>
		</div>
		<div class="clear"> </div>
	</div> <!-- Ende content_item -->
{/block}


{block name=room_main_content}
	<div id="full_width_content">
		<div class="content_item"> <!-- Start content_item -->
			{block=room_list_header}{/block}
			{block=room_list_content}{/block}
		</div> <!-- Ende content_item -->
		{block name=room_list_footer}{/block}
	</div>
{/block}



{block name=room_right_portlets prepend}
	<div class="portlet_rc">
		<a href="" title="schlie&szlig;en" class="btn_head_rc"><img src="{$basic.tpl_path}img/btn_close_rc.gif" alt="close" /></a>
		<h2>Einschr&auml;nkungen der Liste</h2>

		<div class="clear"> </div>

		<a href="" title="bearbeiten" class="btn_body_rc"><img src="{$basic.tpl_path}img/btn_edit_rc.gif" alt="close" /></a>
		<div class="portlet_rc_body">
			<div class="change_view">
				Gruppe
				<select name="" size="1">
					<option>Gruppe w&auml;hlen</option>
				</select>
			</div>

			<div class="change_view">
				Thema
				<select name="" size="1">
					<option>Gruppe w&auml;hlen</option>
				</select>
			</div>
		</div>
	</div>
{/block}