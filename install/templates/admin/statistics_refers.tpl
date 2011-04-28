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
    <li><a href="/admin.php?module=statistics&page=refers"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_refers#}</span></a></li>
</ul>

<h1>{#title_refers#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>Просмотр адресов с которых заходят на сайт</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{ksTabs NAME=refers head_class=tabs2 title_class=bold}
	{ksTab NAME="Таблица" selected=	1}
	{include file="admin/navigation_pagecounter.tpl" pages=$pages}
	<div class="users">
	    <input type="hidden" name="ACTION" value="common">
	    <table class="layout">
	    <tr>
    		<th width="70%">
    			<a href="{get_url _CLEAR="PAGE" order=date dir=$order.newdir}">Адрес</a>{if $order.field=='date'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="30%">
	    		<a href="{get_url _CLEAR="PAGE" order=hits dir=$order.newdir}">Приходов</a>{if $order.field=='hits'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    	</tr>
 		{if $data.ITEMS!=0}
			{foreach from=$data.ITEMS item=oItem key=oKey name=fList}
			    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			    	<td>
			    		{$oItem.url}
			    	</td>
			    	<td>
			    		{$oItem.hits}
			    	</td>
			    </tr>
			{/foreach}
		{else}
			<tr><td colspan="2">По указанному фильтру ничего не найдено</td></tr>
		{/if}
    </table>
</div>
{include file="admin/navigation_pagecounter.tpl" pages=$pages}
{/ksTab}
{if $data.ITEMS!=0}
{ksTab NAME="График" onActivate="drawChart();"}
<h1>График распределения популярности реферов.</h1>
<div id="chart" class="users" style="width:100%;height:500px;background:white;">
</div>
<script type="text/javascript">
{literal}
var bChart=false;
function drawChart()
{
	if(bChart) return;
	bChart=true;
	// Grab the data
	var labels = [{/literal}{foreach from=$data.GRAPH item=oItem name=labels}'{$oItem.url} {$oItem.hits} хитов'{if not $smarty.foreach.labels.last},{/if}{/foreach}];
	var percents=[{foreach from=$data.GRAPH item=oItem name=labels}'{$oItem.PERCENT}%'{if not $smarty.foreach.labels.last},{/if}{/foreach}];
	var data=[{foreach from=$data.GRAPH item=oItem key=oKey name=hits}{$oItem.PERCENT}{if not $smarty.foreach.hits.last},{/if}{/foreach}];
	{literal}
	var chartDiv=document.getElementById('chart');
	// Draw
	
	var width = chartDiv.offsetWidth-20,
		paddingLeft=100,
		paddingRight=50,
		paddingTop=50,
		paddingBottom=50,
	    r=(width-paddingLeft-paddingRight)/4;
	    cx=paddingLeft+r,
	    cy=paddingTop+r,
	    height = r*2+paddingTop+paddingBottom,
	    stroke="#fff",
	    paper = Raphael("chart", width+20, height),
	    rad = Math.PI / 180,
	    boxWidth=10,
	    boxHeight=10,
        chart = paper.set();
        document.getElementById('chart').style.height=(height+20)+'px';
    function sector(paper,cx, cy, r, startAngle, endAngle, params) 
    {
    	if(startAngle==endAngle) startAngle-=1;
    	if(startAngle<0) startAngle+=360;
    	if(startAngle>=360)
    	{
    		startAngle=startAngle%360;
    	}
    	if(endAngle>360)
    	{
    		endAngle=endAngle%360;
    	}
    	if(startAngle==endAngle)
    	{
    		endAngle-=1;
    	}
    	if(endAngle<0)
    	{
    		endAngle+=360;
    	}
        var x1 = cx + r * Math.cos(-startAngle * rad),
            x2 = cx + r * Math.cos(-endAngle * rad),
            y1 = cy + r * Math.sin(-startAngle * rad),
            y2 = cy + r * Math.sin(-endAngle * rad);
        return paper.path(params, ["M", cx, cy, "L", x1, y1, "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2, "z"]);
    }
    var angle = 0,
        total = 0,
        start = 0,
        process = function (j) 
        {
            var value = data[j],
            	angleplus = 360 * value / total,
                popangle = angle + (angleplus / 2),
                color = "hsb(" + start + ", 0.6, 0.9)",
                ms = 1000,
                delta = 10,
                p = sector(paper,cx, cy, r, angle, angle + angleplus, {fill: color, stroke: stroke, "stroke-width": 1}),
                txt = paper.text(cx + (r + delta + 55) * Math.cos(-popangle * rad), cy + (r + delta + 25) * Math.sin(-popangle * rad), percents[j]).attr({fill: color, stroke: "none", opacity: 0, "font-family": 'Fontin-Sans, Arial', "font-size": "20px"}),
                txt2= paper.text(cx+r+delta+50+boxWidth,j*20+20,labels[j]),
                line= paper.path({"stroke":color,opacity:0}).moveTo(cx + (r) * Math.cos(-popangle * rad),cy + (r) * Math.sin(-popangle * rad)).cplineTo(cx+r+delta+50,j*20+20,50),
                rect= paper.rect(cx+r+delta+50-boxWidth/2,j*20+20-boxHeight/2,boxWidth,boxHeight).attr({"stroke":stroke,fill: color});
                bbox=txt2.getBBox();
                txt2.attr({x:(bbox.x+bbox.width)});
            p.mouseover(function () {
                p.animate({scale: [1.1, 1.1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 1}, ms, "elastic");
                line.animate({opacity:1}, ms);
                line.toFront();
            }).mouseout(function () {
                p.animate({scale: [1, 1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 0}, ms);
                line.animate({opacity:0}, ms);
            });
            rect.mouseover(function () {
                p.animate({scale: [1.1, 1.1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 1}, ms, "elastic");
                line.animate({opacity:1}, ms);
                line.toFront();
            }).mouseout(function () {
                p.animate({scale: [1, 1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 0}, ms);
                line.animate({opacity:0}, ms);
            });
            angle += angleplus;
            chart.push(p);
            chart.push(txt);
            start += 1/data.length;
        };
    for (var i = 0, ii = data.length; i < ii; i++) {
        total += data[i];
    }
    for (var i = 0; i < ii; i++) {
        process(i);
    }
}
{/literal}
</script>
{/ksTab}
{/if}
{ksTab NAME="Список хостов"}
<div class="users">
    <input type="hidden" name="ACTION" value="common">
    <table class="layout">
    <tr>
		<th width="70%">
			<a href="{get_url _CLEAR="PAGE" order=date dir=$order.newdir}">Хост</a>{if $order.field=='date'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
		</th>
		<th width="30%">
    		<a href="{get_url _CLEAR="PAGE" order=hits dir=$order.newdir}">Всего приходов</a>{if $order.field=='hits'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
		</th>
	</tr>
	{if $data.HOSTS!=0}
		{foreach from=$data.HOSTS item=oItem key=oKey name=fList}
		    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
		    	<td>
		    		{$oItem.host}
		    	</td>
		    	<td>
		    		{$oItem.hits}
		    	</td>
		    </tr>
		{/foreach}
	{else}
		<tr><td colspan="2">По указанному фильтру ничего не выбрано</td></tr>
	{/if}
    </table>
</div>
{/ksTab}
{if $data.HOSTS!=0}
{ksTab NAME="График хостов" onActivate="drawChart2();"}
<h1>График распределения популярности хостов.</h1>
<div id="chart2" style="width:100%;height:500px;">
</div>
<script type="text/javascript">
{literal}
var bChart2=false;
function drawChart2()
{
	if(bChart2) return;
	bChart2=true;
	// Grab the data
	var labels = [{/literal}{foreach from=$data.HOSTS item=oItem name=labels}'{$oItem.host} {$oItem.hits} хитов'{if not $smarty.foreach.labels.last},{/if}{/foreach}];
	var percents=[{foreach from=$data.HOSTS item=oItem name=labels}'{$oItem.PERCENT}%'{if not $smarty.foreach.labels.last},{/if}{/foreach}];
	var data=[{foreach from=$data.HOSTS item=oItem key=oKey name=hits}{$oItem.PERCENT}{if not $smarty.foreach.hits.last},{/if}{/foreach}];
	{literal}
	var chartDiv=document.getElementById('chart2');
	// Draw
	
	var width = chartDiv.offsetWidth-20,
		paddingLeft=100,
		paddingRight=50,
		paddingTop=50,
		paddingBottom=50,
	    r=(width-paddingLeft-paddingRight)/4;
	    cx=paddingLeft+r,
	    cy=paddingTop+r,
	    height = r*2+paddingTop+paddingBottom,
	    stroke="#fff",
	    paper = Raphael("chart2", width+20, height),
	    rad = Math.PI / 180,
	    boxWidth=10,
	    boxHeight=10,
        chart = paper.set();
        document.getElementById('chart2').style.height=(height+20)+'px';
    function sector(paper,cx, cy, r, startAngle, endAngle, params) 
    {
    	if(startAngle==endAngle) startAngle-=1;
    	if(startAngle<0) startAngle+=360;
    	if(startAngle>=360)
    	{
    		startAngle=startAngle%360;
    	}
    	if(endAngle>360)
    	{
    		endAngle=endAngle%360;
    	}
    	if(endAngle==360) endAngle-=1;
    	if(startAngle==endAngle)
    	{
    		endAngle-=1;
    	}
    	if(endAngle<0)
    	{
    		endAngle+=360;
    	}
        var x1 = cx + r * Math.cos(-startAngle * rad),
            x2 = cx + r * Math.cos(-endAngle * rad),
            y1 = cy + r * Math.sin(-startAngle * rad),
            y2 = cy + r * Math.sin(-endAngle * rad);
        return paper.path(params, ["M", cx, cy, "L", x1, y1, "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2, "z"]);
    }
    var angle = 0,
        total = 0,
        start = 0,
        process = function (j) 
        {
            var value = data[j],
            	angleplus = 360 * value / total,
                popangle = angle + (angleplus / 2),
                color = "hsb(" + start + ", .7, .9)",
                ms = 1000,
                delta = 10,
                p = sector(paper,cx, cy, r, angle, angle + angleplus, {fill: color, stroke: stroke, "stroke-width": 1}),
                txt = paper.text(cx + (r + delta + 55) * Math.cos(-popangle * rad), cy + (r + delta + 25) * Math.sin(-popangle * rad), percents[j]).attr({fill: color, stroke: "none", opacity: 0, "font-family": 'Fontin-Sans, Arial', "font-size": "20px"}),
                txt2= paper.text(cx+r+delta+50+boxWidth,j*20+20,labels[j]),
                line= paper.path({"stroke":color,opacity:0}).moveTo(cx + (r) * Math.cos(-popangle * rad),cy + (r) * Math.sin(-popangle * rad)).cplineTo(cx+r+delta+50,j*20+20,50),
                rect= paper.rect(cx+r+delta+50-boxWidth/2,j*20+20-boxHeight/2,boxWidth,boxHeight).attr({"stroke":stroke,fill: color});
                bbox=txt2.getBBox();
                txt2.attr({x:(bbox.x+bbox.width)});
            p.mouseover(function () {
                p.animate({scale: [1.1, 1.1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 1}, ms, "elastic");
                line.animate({opacity:1}, ms);
                line.toFront();
            }).mouseout(function () {
                p.animate({scale: [1, 1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 0}, ms);
                line.animate({opacity:0}, ms);
            });
            rect.mouseover(function () {
                p.animate({scale: [1.1, 1.1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 1}, ms, "elastic");
                line.animate({opacity:1}, ms);
                line.toFront();
            }).mouseout(function () {
                p.animate({scale: [1, 1, cx, cy]}, ms, "elastic");
                txt.animate({opacity: 0}, ms);
                line.animate({opacity:0}, ms);
            });
            angle += angleplus;
            chart.push(p);
            chart.push(txt);
            start += 1/data.length;
        };
    for (var i = 0, ii = data.length; i < ii; i++) {
        total += data[i];
    }
    for (var i = 0; i < ii; i++) {
        process(i);
    }
}
{/literal}
</script>
{/ksTab}
{/if}
{/ksTabs}
{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/statistics.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_refers#}</dt>
	<dd>{#hint_refers#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}  

