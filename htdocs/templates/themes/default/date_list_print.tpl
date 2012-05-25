{extends file="room_list_print.tpl"}

{block name=room_list_header}
	
	<table width="100%" cellpadding="2" cellspacing="0" class="print_table_border">
		<thead>
			<tr>
				<td class="table_head"></td>
				<td class="table_head">
					{if $list.sorting_parameters.sort_title == "up"}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_title_link}" id="sort_up"><strong>___COMMON_TITLE___</strong></a></h3>
            		{elseif $list.sorting_parameters.sort_title == "down"}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_title_link}" id="sort_down"><strong>___COMMON_TITLE___</strong></a></h3>
            		{else}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_title_link}" class="sort_none">___COMMON_TITLE___</a></h3>
            		{/if}
				</td>
				<td class="table_head">
					{if $list.sorting_parameters.sort_time == "up"}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_time_link}" id="sort_up"><strong>___DATES_TIME___</strong></a></h3>
            		{elseif $list.sorting_parameters.sort_time == "down"}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_time_link}" id="sort_down"><strong>___DATES_TIME___</strong></a></h3>
            		{else}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_time_link}" class="sort_none">___DATES_TIME___</a></h3>
            		{/if}
				</td>
				<td class="table_head">
    				{if $list.sorting_parameters.sort_place== "up"}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_place_link}" id="sort_up"><strong>___DATES_PLACE___</strong></a></h3>
            		{elseif $list.sorting_parameters.sort_place == "down"}
            		 	<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_place_link}" id="sort_down"><strong>___DATES_PLACE___</strong></a></h3>
            		{else}
            	 		<h3><a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct={$environment.function}&{$list.sorting_parameters.sort_place_link}" class="sort_none">___DATES_PLACE___</a></h3>
            		{/if}
            	</td>
			</tr>
		</thead>
		<tbody>
			{foreach $date.list_content.items as $item }
				<tr>
					<td class="{if $item@iteration is odd}row_odd{else}row_even{/if}">
    					{if $item.noticed.show_info}
    						<img title="" class="new_item_2" src="{$basic.tpl_path}img/flag_neu_a.gif" alt="*" />
    					{/if}
    				</td>
					<td class="{if $item@iteration is odd}row_odd{else}row_even{/if}">
						<p>
        					{if $item.activated}
        						<a href="commsy.php?cid={$environment.cid}&mod={$environment.module}&fct=detail&{$environment.params}&iid={$item.iid}">{$item.title}</a>
        					{else}
        						{$item.title}</br>___COMMON_NOT_ACTIVATED___
        					{/if}
        				</p>
					</td>
					<td class="{if $item@iteration is odd}row_odd{else}row_even{/if} print_border">
						<p>
        					{$item.date}{if !empty($item.time) && $item.show_time}, {$item.time}{/if}
        				</p>
					</td>
					<td class="{if $item@iteration is odd}row_odd{else}row_even{/if} print_border">
						<div class="print_title">
            				<p>
            					{$item.place}
            				</p>
            			</div>
            			{if !empty($item.color)}
            				<div class="print_files_icon">
            					<p>
            						<span class="date_list_color" style="background-color:{$item.color}">&nbsp;</span>
            					</p>
            				</div>
            				<div class="clear"></div>
            			{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{/block}

