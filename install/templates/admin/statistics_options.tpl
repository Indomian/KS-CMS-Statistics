{config_load file=admin.conf section=statistics}
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>
	<li><a href="/admin.php?module=statistics"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>      
    <li><a href="/admin.php?module=statistics&page=options"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_options#}</span></a></li>
</ul>
<h1>{#title_options#}</h1>
<form action="{get_url _CLEAR="ACTION"}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save">
	{ksTabs NAME=st_options head_class=tabs2 title_class=bold}
		{ksTab NAME=$smarty.config.tabs_common selected=1}{strip}
			<div class="form">
				<table class="layout">
	    			<tr>
    					<th width="30%">{#header_field#}</th>
    					<th width="70%">{#header_value#}</th>
    				</tr>
    				<tr>
    					<td>{Title field=active}</td>
    					<td><select name="active" class="form_input">
								<option value="1" {if $data.config.active==1} selected="selected"{/if}>{#active#}</option>
								<option value="0" {if $data.config.active==0} selected="selected"{/if}>{#inactive#}</option>
							</select></td>
					</tr>
    				<tr>
    					<td>{Title field=save_admin_path}</td>
    					<td><select name="save_admin_path" class="form_input">
								<option value="1" {if $data.config.save_admin_path==1} selected="selected"{/if}>{#yes#}</option>
								<option value="0" {if $data.config.save_admin_path==0} selected="selected"{/if}>{#no#}</option>
							</select></td>
					</tr>
    				<tr>
    					<td>{Title field=save_admin}</td>
    					<td><select name="save_admin" class="form_input">
								<option value="1" {if $data.config.save_admin==1} selected="selected"{/if}>{#yes#}</option>
								<option value="0" {if $data.config.save_admin==0} selected="selected"{/if}>{#no#}</option>
							</select></td>
    				</tr>
    				<tr>
    					<td>{Title field=countRobots}</td>
    					<td><select name="countRobots" class="form_input">
								<option value="1" {if $data.config.countRobots==1} selected="selected"{/if}>{#yes#}</option>
								<option value="0" {if $data.config.countRobots==0} selected="selected"{/if}>{#no#}</option>
							</select></td>
    				</tr>
    			</table>
    		</div>
    	{/strip}{/ksTab}
    	{ksTab NAME=$smarty.config.tabs_dataLifeTime}{strip}
    	<div class="form">
    		<table class="layout">
    			<tr><th width="30%">{#header_field#}</th><th width="70%">{#header_value#}</th></tr>
    			<tr>
    				<td>{Title field="sidLifeTime"}</td>
    				<td><input type="text" name="sc_sidLifeTime" value="{$data.config.sidLifeTime/3600|intval|default:48}" class="form_input"/></td>
    			</tr>
    			<tr>
    				<td>{Title field="sidCount"}</td>
    				<td><input type="text" name="sc_sidCount" value="{$data.config.sidCount|intval|default:10000}" class="form_input"/></td>
    			</tr>
    			<tr>
    				<td>{Title field="movesLifeTime"}</td>
    				<td><input type="text" name="sc_movesLifeTime" value="{$data.config.movesLifeTime/3600|floatval|default:48}" class="form_input"/></td>
    			</tr>
    			<tr>
    				<td>{Title field="movesCount"}</td>
    				<td><input type="text" name="sc_movesCount" value="{$data.config.movesCount|intval|default:10000}" class="form_input"/></td>
    			</tr>
    		</table>
    	</div>
    	{/strip}{/ksTab}
		{ksTab NAME=$smarty.config.tabs_access}{strip}
    	<div class="form">
			<table class="layout">
    			<tr>
					<th width="30%">{#header_group#}</th>
					<th width="70%">{#header_level#}</th>
				</tr>
				{foreach from=$data.access.groups item=oGroup}
				{assign var="checked" value=""}
				<tr>
					<td>{$oGroup.title}</td>
					<td>
						<ul class="levelSelector">
						{foreach from=$data.access.module key=oKey item=oItem}
							{if $data.access.levels[$oGroup.id].level==$oKey}{assign var="checked" value="checked=\"checked\""}{/if}
							<li {if $oKey<10}class="{if $checked!=""}access_available{else}access_denied{/if}{/if}">
							<label>
							<input type="checkbox" name="sc_groupLevel[{$oGroup.id}][]" value="{$oKey}" 
								{if $oKey==10}
									{if $data.access.levels[$oGroup.id].level==10}
										checked="checked"
									{/if}
								{else}
									{$checked}
								{/if}
							onclick="document.obAccessLevels.onClick(this);"/> {$oItem}</label></li>
						{/foreach}
						</ul>
					</td>
				</tr>
				{/foreach}
			</table>
		</div>
    	{/strip}{/ksTab}
	{/ksTabs}
	<div class="form_buttons">
    	<div>
    		<input type="submit" value="{#save#}" class="save"/>
    	</div>
   	</div>
</form>
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/settings.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_options#}</dt>
	<dd>{#hint_options#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}