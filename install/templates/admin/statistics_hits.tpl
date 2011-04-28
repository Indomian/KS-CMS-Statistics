{config_load file=admin.conf section=statistics}
{include file="admin/blocks/navChain.tpl"}
<script type="text/javascript" src="/js/floatmessage.js"></script>
<script type="text/javascript" src="/js/raphael.js"></script>
<script type="text/javascript" src="/js/statistics/raphael_extensions.js"></script>
<script type="text/javascript">
function checkAll(oForm, checked)
{ldelim}
for (var i=0; i < oForm.length; i++)
{ldelim}
     oForm[i].checked = checked;
{rdelim}
{rdelim}
</script>
<h1>{#title_hits#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td>
				<div>
					<form action="{get_url action="confcols"}" method="post">
						<input type="submit" value="{#config_columns#}"/>
					</form>
				</div>
			</td>
			<td width="100%">
				<span>Просмотр статистики посещения сайта</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{ksTabs NAME=st_hits head_class=tabs2 title_class=bold}
	{ksTab NAME="Таблица" selected=	1}
	{include file="admin/navigation_pagecounter.tpl" pages=$pages}
	<div class="users">
	    <input type="hidden" name="ACTION" value="common">
	    <table class="layout">
		    <tr>
		    	{foreach from=$STRUCTURE item=oItem key=oKey name=header} 
		    		{if $oItem.show==1}
	    			<th width="{$oItem.width}">
	    				{if $oItem.sort!=''}
	    					<a href="{get_url _CLEAR="PAGE" order=$oItem.sort dir=$order.newdir}">{$oItem.title}</a>
	    					{if $order.field==$oItem.sort}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
	    				{else}
	    					{$oItem.title}
	    				{/if}
	    			</th>
	    			{/if}
	    		{/foreach}
	    	</tr>
	 		{if $data.ITEMS!=0}
				{foreach from=$data.ITEMS item=oItem key=oKey name=fList}
				    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
				    	{if $STRUCTURE.date.show==1}
				    		<td>
			    				{$oItem.date|date_format:"%d.%m.%Y"}
			    			</td>
			    		{/if}
			    		{if $STRUCTURE.hits.show==1}
			    			<td>
			    				{$oItem.hits}
			    			</td>
			    		{/if}
			    		{if $STRUCTURE.hosts.show==1}
							<td>{$oItem.hosts}</td>
						{/if}
						{if $STRUCTURE.rhits.show==1}
							<td>{$oItem.robot_hits}</td>
						{/if}
						{if $STRUCTURE.hitsOnHost.show==1}
							<td>{$oItem.hits/$oItem.hosts}</td>
						{/if}
					</tr>
				{/foreach}
			{/if}
    	</table>
	</div>
{include file="admin/navigation_pagecounter.tpl" pages=$pages}
{/ksTab}
{ksTab NAME="График" onActivate="drawChart();"}
<div class="users" style="background:white;">
<div id="chart" style="width:100%;height:400px;">
</div>
</div>
<script type="text/javascript">
{literal}
var bChart=false;
function drawChart()
{
	if(bChart) return;
	bChart=true;
	// Grab the data
	var labels = [{/literal}{foreach from=$data.HOURS item=oItem name=labels}'{$oItem.date|date_format:"%d.%m.%Y %H:00"}'{if not $smarty.foreach.labels.last},{/if}{/foreach}],
		data = [],
		data1=[],
		data2 =[];
	var data3={ldelim}{foreach from=$data.HOURS item=oItem key=oKey name=hits}'{$oKey}':{$oItem.hits}{if not $smarty.foreach.hits.last},{/if}{/foreach}{rdelim};
	var data4={ldelim}{foreach from=$data.HOURS item=oItem key=oKey name=hosts}'{$oKey}':{$oItem.hosts}{if not $smarty.foreach.hosts.last},{/if}{/foreach}{rdelim};
	var data5={ldelim}{foreach from=$data.HOURS item=oItem key=oKey name=robot_hits}'{$oKey}':{$oItem.robot_hits}{if not $smarty.foreach.robot_hits.last},{/if}{/foreach}{rdelim};
	//Пересчет данных чтобы получить верные даты
	var clock=new Date;
	var dateFrom={$data.dateFrom};
	var dateTo={$data.dateTo};
	var steps=22;
	//var steps=100;
	labels=[];
	{literal}
	var step=(dateTo-dateFrom)/steps;
	//alert(step);
	var j=0;
	var sum=0,sum1=0,sum2=0;
	for(i=dateFrom;i<dateTo;i++)
	{
		if(j<step)
		{
			if(data3[i])
			{
				sum+=data3[i];
			}
			if(data4[i])
			{
				sum2+=data4[i];
			}
			if(data5[i])
			{
				sum1+=data5[i];
			}
			j++;
		}
		if(j>=step)
		{
			clock.setTime(i*3600000);
			labels.push(clock.getDate()+'.'+(clock.getMonth()+1)+'.'+clock.getFullYear());
			data.push(sum);
			data2.push(sum2);
			data1.push(sum1);
			sum=0;
			sum2=0;
			sum1=0;
			j=0;
		}
	}
	var chartDiv=document.getElementById('chart');
	// Draw
	
	var width = chartDiv.offsetWidth-20,
	    height = 400,
	    leftgutter = 60,
	    bottomgutter = 60,
	    topgutter = 20,
	    rightgutter=20,
	    gridX=5;
	    gridY=10;
	    color2= "#54a8df",
	    color1="#5aba7f",
	    color= "#f95311",
	    lineWidth=4,
	    r = Raphael("chart", width+20, height),
	    txt = {font: '12px Fontin-Sans, Arial', fill: "#000"},
	    txt1 = {font: '12px Fontin-Sans, Arial', fill: "#000"},
	    txt2 = {font: '12px Fontin-Sans, Arial', fill: "#000"},
	    gridStyle={"stroke-width":1,"stroke":"#e5e5e5","stroke-dasharray":"- "},
	    axesStyle={font: '11px Tahoma, Arial', fill: "#aaaaaa"},
	    max = Math.max(Math.max.apply(Math, data),Math.max.apply(Math, data1))+10,
	    X = (width - leftgutter-rightgutter) / (labels.length-1),
	    Y = (height - bottomgutter - topgutter) / max;
	var path_a = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter-Y*data[0]), 
		path = r.path({stroke: color, "stroke-width": lineWidth,"opacity":1}).moveTo(leftgutter,height-bottomgutter-Y*data[0]),
	    bgp = r.path({stroke: "none", opacity: .3, fill: color}).moveTo(leftgutter, height - bottomgutter),
	    path_a1 = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter-Y*data1[0]),
	    path1 = r.path({stroke: color1, "stroke-width": 4}).moveTo(leftgutter,height-bottomgutter-Y*data1[0]),
	    bgp1 = r.path({stroke: "none", opacity: .3, fill: color1}).moveTo(leftgutter, height - bottomgutter),
	    path_a2 = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter-Y*data2[0]),
	    path2 = r.path({stroke: color2, "stroke-width": 4}).moveTo(leftgutter,height-bottomgutter-Y*data2[0]),
	    bgp2 = r.path({stroke: "none", opacity: .3, fill: color2}).moveTo(leftgutter, height - bottomgutter),
	    
	    frame = r.rect(10, 10, 100, 60, 2).attr({fill: "#fff6c4", stroke: "#f6db9a", "stroke-width": 1}).hide(),
	    label = [],
	    is_label_visible = false,
	    leave_timer,
	    blanket = r.set();
	label[0] = r.text(60, 10, "users\n24 hits").attr(txt1).hide();
	label[1] = r.text(60, 50, "22 September 2008").attr(txt1).attr({fill: "#aaaaaa"}).hide();
	dot = r.circle(60, 10, 7).attr({fill: color, stroke: "#000", opacity:0});
	//рисуем сетку по X
	var axesY=r.path({"stroke-width":1,"stroke":"#e5e5e5"}).moveTo(leftgutter,topgutter).lineTo(leftgutter,height-bottomgutter+20);
	r.text(leftgutter+30, height-bottomgutter+15, labels[0]).attr(axesStyle).toBack();
	var rgridX=r.path(gridStyle),sx=(width-rightgutter-leftgutter)/gridX,sy=(height-bottomgutter-topgutter)/gridY;
    for(var i=1;i<=gridX;i++)	
    {
    	rgridX.moveTo(leftgutter+sx*i,topgutter).lineTo(leftgutter+sx*i,height-bottomgutter);
    	axesY.moveTo(leftgutter+sx*i,height-bottomgutter).lineTo(leftgutter+sx*i,height-bottomgutter+20);
    	if(i!=gridX) r.text(leftgutter+sx*i+30, height-bottomgutter+15, labels[Math.min(Math.ceil(labels.length/gridX*i),labels.length-1)]).attr(axesStyle).toBack();
    }
    var rgridY=r.path(gridStyle);
    for(var j=0;j<=gridY;j++)
    {
    	rgridY.moveTo(leftgutter,topgutter+sy*j).lineTo(width-rightgutter,topgutter+sy*j);
    	if(j!=gridY) r.text(leftgutter/2,topgutter+sy*j,Math.round(max/gridY*(gridY-j))).attr(axesStyle);
    }
    for (var i = 0, ii = labels.length; i < ii; i++) 
	{
		var y = Math.round(height - bottomgutter - Y * data[i]),
	    	x = leftgutter + X*i;
	    bgp.lineTo(x, y);
	    path_a.lineTo(x,y);
		path.lineTo(x,y);
		r.circle(x,y,4).attr({"stroke-width":2,"stroke":"#fff","fill":color});
		
		blanket.push(r.rect(x-X/2, y-10, X, 20).attr({stroke: "none", fill: "#f00", opacity: 0}));
		var rect = blanket[blanket.length - 1];
		(function (x, y, data, lbl, dot) {
	        var timer, i = 0;
	        $(rect.node).hover(function () {
	            clearTimeout(leave_timer);
	            var newcoord = {x: +x + 7.5, y: y - 19};
	            if (newcoord.x + 100 > width) {
	                newcoord.x -= 114;
	            }
	            frame.show().animate({x: newcoord.x, y: newcoord.y}, 200 * is_label_visible);
	            label[0].attr({text: "Посетители:\n"+data + " хит" + ((data % 10 == 1) ? "" : "ов")}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 20}, 200 * is_label_visible);
	            label[1].attr({text: lbl}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 45}, 200 * is_label_visible);
	            dot.show().attr({opacity:1,cx: x, cy: y, fill:color});
	            is_label_visible = true;
	            r.safari();
	        }, function () {
	            r.safari();
	            leave_timer = setTimeout(function () {
	                frame.hide();
	                label[0].hide();
	                label[1].hide();
	                dot.hide();
	                is_label_visible = false;
	                r.safari();
	            }, 1);
	        });
	    })(x, y, data[i], labels[i], dot);
		
		var y = Math.round(height - bottomgutter - Y * data2[i]),
	    	x = leftgutter + X*i;
	   	bgp2.lineTo(x, y);
		path2.lineTo(x, y);
		path_a2.lineTo(x,y);
		r.circle(x,y,4).attr({"stroke-width":2,"stroke":"#fff","fill":color2});
		blanket.push(r.rect(x-X/2, y-10, X, 20).attr({stroke: "none", fill: "#f00", opacity: 0}));
		var rect = blanket[blanket.length - 1];
		(function (x, y, data, lbl, dot) {
            var timer, i = 0;
            $(rect.node).hover(function () {
                clearTimeout(leave_timer);
                var newcoord = {x: +x + 7.5, y: y - 19};
                if (newcoord.x + 100 > width) {
                    newcoord.x -= 114;
                }
                frame.show().animate({x: newcoord.x, y: newcoord.y}, 200 * is_label_visible);
                label[0].attr({text: "Посетители:\n"+data + " хост" + ((data % 10 == 1) ? "" : "ов")}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 20}, 200 * is_label_visible);
                label[1].attr({text: lbl}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 45}, 200 * is_label_visible);
                dot.show().attr({opacity:1,cx: x, cy: y,fill:color2});
                is_label_visible = true;
                r.safari();
            }, function () {
                r.safari();
                leave_timer = setTimeout(function () {
                    frame.hide();
                    label[0].hide();
                    label[1].hide();
                    dot.hide();
                    is_label_visible = false;
                    r.safari();
                }, 1);
            });
        })(x, y, data2[i], labels[i], dot);
        
        //Третий график
       	var y = Math.round(height - bottomgutter - Y * data1[i]),
	    	x = leftgutter + X*i;
	   	bgp1.lineTo(x, y);
		path1.lineTo(x, y);
		path_a1.lineTo(x,y);
		r.circle(x,y,4).attr({"stroke-width":2,"stroke":"#fff","fill":color1});
		blanket.push(r.rect(x-X/2, y-10, X, 20).attr({stroke: "none", fill: "#f00", opacity: 0}));
		var rect = blanket[blanket.length - 1];
		(function (x, y, data, lbl, dot) {
            var timer, i = 0;
            $(rect.node).hover(function () {
                clearTimeout(leave_timer);
                var newcoord = {x: +x + 7.5, y: y - 19};
                if (newcoord.x + 100 > width) {
                    newcoord.x -= 114;
                }
                frame.show().animate({x: newcoord.x, y: newcoord.y}, 200 * is_label_visible);
                label[0].attr({text: "Роботы:\n"+data + " хит" + ((data % 10 == 1) ? "" : "ов")}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 20}, 200 * is_label_visible);
                label[1].attr({text: lbl}).show().animate({x: +newcoord.x + 50, y: +newcoord.y + 45}, 200 * is_label_visible);
                dot.show().attr({opacity:1,cx: x, cy: y,fill:color1});
                is_label_visible = true;
                r.safari();
            }, function () {
                r.safari();
                leave_timer = setTimeout(function () {
                    frame.hide();
                    label[0].hide();
                    label[1].hide();
                    dot.hide();
                    is_label_visible = false;
                    r.safari();
                }, 1);
            });
        })(x, y, data1[i], labels[i], dot);
	}
	
	bgp1.lineTo(x, height - bottomgutter).andClose().toBack();
	bgp2.lineTo(x, height - bottomgutter).andClose().toBack();
	bgp.lineTo(x, height - bottomgutter).andClose().toBack();
	rgridY.toBack();
    rgridX.toBack();
	frame.toFront();
	label[0].toFront();
	label[1].toFront();
	blanket.toFront();
	
}
{/literal}
</script>
{/ksTab}
{/ksTabs}

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/statistics.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_hits#}</dt>
	<dd>{#hint_hits#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}  

