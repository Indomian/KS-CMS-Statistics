{config_load file=admin.conf section=statistics}
<script type="text/javascript" src="/js/floatmessage.js"></script>
<script type="text/javascript" src="/js/raphael.js"></script>
<script type="text/javascript">
function checkAll(oForm, checked)
{ldelim}
for (var i=0; i < oForm.length; i++)
{ldelim}
     oForm[i].checked = checked;
{rdelim}
{rdelim}
</script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="/admin.php?module=statistics"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="/admin.php?module=statistics&page=guests"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_guests#}</span></a></li>
</ul>

<h1>{#title_guests#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>Список посетителей сайта</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{include file="admin/navigation_pagecounter.tpl" pages=$pages}
	<div class="users">
	    <input type="hidden" name="ACTION" value="common">
	    <table class="layout">
	    <tr>
	    	<th>
	    		ID
	    	</th>
    		<th width="20%">
    			<a href="{get_url _CLEAR="PAGE" order=last_active dir=$order.newdir}">Последняя активность</a>{if $order.field=='last_active'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="20%">
    			<a href="{get_url _CLEAR="PAGE" order=first_in dir=$order.newdir}">Первое посещение</a>{if $order.field=='first_in'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="20%">
	    		<a href="{get_url _CLEAR="PAGE" order=hits dir=$order.newdir}">Хитов</a>{if $order.field=='hits'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="20%">
    			<a href="{get_url _CLEAR="PAGE" order=user_id dir=$order.newdir}">Пользователь</a>{if $order.field=='user_id'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="20%">
    			<a href="{get_url _CLEAR="PAGE" order=user_ip dir=$order.newdir}">IP адрес</a>{if $order.field=='user_ip'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="30%">
    			
    		</th>
    	</tr>
 		{if $data.ITEMS!=0}
			{foreach from=$data.ITEMS item=oItem key=oKey name=fList}
			    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			    	<td>
			    		{$oItem.id}
			    	</td>
			    	<td>
			    		{$oItem.last_active|date_format:"%d.%m.%Y %H:%M"}
			    	</td>
			    	<td>
			    		{$oItem.first_in|date_format:"%d.%m.%Y %H:%M"}
			    	</td>
			    	<td>
			    		{$oItem.hits}
			    	</td>
					<td>
						{if $oItem.users_title!=''}
							<a href="/admin.php?module=main&modpage=users&ACTION=edit&id={$oItem.user_id}">{$oItem.users_title}</a>[{$oItem.user_id}]
						{else}
							Гость №{$oItem.id}
						{/if}
					</td>
					<td>
						{$oItem.user_ip}
					</td>
					<td>
						<a href="/admin.php?module=statistics&page=moves&fm=GET&filter=1&ffstatistics_sessions.id={$oItem.id}" title="Просмотреть маршруты пользователя"><img src="{#images_path#}/icons2/view.gif" alt="Маршруты" border="0"/></a>
					</td>
				</tr>
			{/foreach}
		{/if}
    </table>
</div>
{include file="admin/navigation_pagecounter.tpl" pages=$pages}

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/statistics.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_guests#}</dt>
	<dd>{#hint_guests#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}  

