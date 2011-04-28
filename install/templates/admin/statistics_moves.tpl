{config_load file=admin.conf section=statistics}
<script type="text/javascript" src="/js/floatmessage.js"></script>
<script type="text/javascript" src="/js/raphael.js"></script>
<script type="text/javascript" src="/js/statistics/moves.js"></script>
<script type="text/javascript">
{strip}
var froms=
	{ldelim}
		{foreach from=$data.FROMS item=oItem key=oKey name=froms}
			"{$oKey}":"{$oItem}"{if not $smarty.foreach.froms.last},{/if}
		{/foreach}
	{rdelim};
var tos=
	[
		{foreach from=$data.TOS item=oItem key=oKey name=tos}
			{ldelim}"code":{$oKey},"text":"{$oItem}"{rdelim}{if not $smarty.foreach.tos.last},{/if}
		{/foreach}
	];
var moves=
	[
		{foreach from=$data.WAYS item=oItem key=oKey name=tos}
			{ldelim}"from":"{$oItem.move_from}","to":"{$oItem.move_to}","hits":"{$oItem.num_out}"{rdelim}{if not $smarty.foreach.tos.last},{/if}
		{/foreach}
	];
var usermoves=
	[
		{foreach from=$data.USER_WAYS item=oItem key=oKey name=tos}
			{ldelim}"from":"{$oItem.move_from}","to":"{$oItem.move_to}","url":"
				{if $smarty.foreach.tos.last}{$oItem.statistics_refers_url}{else}{$oItem.statistics_acceptors_url}{/if}","hits":"{$oItem.statistics_refers_hits}","length":"{$oItem.LENGTH}"{rdelim}{if not $smarty.foreach.tos.last},{/if}
		{/foreach}
	];
{/strip}
</script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="/admin.php?module=statistics"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="/admin.php?module=statistics&page=moves"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_moves#}</span></a></li>
</ul>

<h1>{#title_moves#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>Просмотр маршрутов движения по сайту</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{ksTabs NAME=moves head_class=tabs2 title_class=bold}
	{ksTab NAME="Таблица" selected=	1}
	{include file="admin/navigation_pagecounter.tpl" pages=$pages}
	<div class="users">
	    <input type="hidden" name="ACTION" value="common">
	    <table class="layout">
	    <tr>
    		<th width="10%">
    			<a href="{get_url _CLEAR="PAGE" order=date dir=$order.newdir}">Дата</a>{if $order.field=='date'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="40%">
				Со страницы
    		</th>
    		<th width="40%">
    			На страницу
    		</th>
    		<th width="10%">
    			<a href="{get_url _CLEAR="PAGE" order="statistics_sessions.id" dir=$order.newdir}">Пользователь</a>{if $order.field=='statistics_sessions.id'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    	</tr>
 		{if $data.ITEMS!=0}
			{foreach from=$data.ITEMS item=oItem key=oKey name=fList}
			    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			    	<td>
			    		{$oItem.date|date_format:"%d.%m.%Y %H:%M"}
			    	</td>
			    	<td>
			    		<a href="{get_url _CLEAR="ffstatistics_refers.url" fm=GET filter=1}&ffstatistics_refers.url={$oItem.statistics_refers_url|escape:'url'}">{$oItem.statistics_refers_url}</a>
			    	</td>
					<td>
						<a href="{get_url _CLEAR="ffstatistics_sessions.id" fm=GET filter=1}&ffstatistics_acceptors.url={$oItem.statistics_acceptors_url|escape:'url'}">{$oItem.statistics_acceptors_url}</a></td>
					<td><a href="{get_url _CLEAR="ffstatistics_sessions.id" fm=GET filter=1}&ffstatistics_sessions.id={$oItem.statistics_sessions_id}" title="{#hint_filterguest#}">{$oItem.statistics_sessions_id}</a>
					{if $oItem.statistics_sessions_user_id>0}
						[<a href="/admin.php?module=main&modpage=users&ACTION=edit&id={$oItem.statistics_sessions_user_id}" title="{#hint_viewuser#}">{$oItem.statistics_sessions_user_id}</a>]
					{else}
						[Гость №{$oItem.statistics_sessions_id}]
					{/if}
					</td>
				</tr>
			{/foreach}
		{else}
			<tr><td colspan="4">Маршрутов движения не надено</td></tr>
		{/if}
    </table>
</div>
{include file="admin/navigation_pagecounter.tpl" pages=$pages}
{/ksTab}
{if $data.WAYS!=0}
{ksTab NAME="Схема" onActivate="drawChartMoves(froms,tos,moves);"}
<h1>Схема переходов с указанного адреса</h1>
<div id="chart" style="width:100%;height:500px;">
</div>
{/ksTab}
{/if}
{if $data.USER_WAYS!=0}
{ksTab NAME="Схема маршрута пользователя" onActivate="drawChartUserMoves(usermoves);"}
<h1>Схема маршрутов указанного пользователя</h1>
<div id="chart2" style="width:100%;height:500px;">
</div>
{/ksTab}
{/if}
{/ksTabs}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/statistics.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_moves#}</dt>
	<dd>{#hint_moves#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}  

