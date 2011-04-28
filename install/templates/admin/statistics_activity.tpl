{config_load file=admin.conf section=statistics}
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
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="/admin.php?module=statistics"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="/admin.php?module=statistics&page=activity"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_activity#}</span></a></li>
</ul>

<h1>{#title_activity#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>Просмотр статистики посещения сайта по часам</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{ksTabs NAME=st_activity head_class=tabs2 title_class=bold}
	{ksTab NAME="Таблица" selected=	1}
	<div class="users">
	    <input type="hidden" name="ACTION" value="common">
	    <table class="layout">
	    <tr>
    		<th width="25%">
    			<a href="{get_url _CLEAR="PAGE" order=hour dir=$order.newdir}">Час</a>{if $order.field=='hour'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="25%">
	    		<a href="{get_url _CLEAR="PAGE" order=hits dir=$order.newdir}">Хитов</a>{if $order.field=='hits'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="25%">
    			<a href="{get_url _CLEAR="PAGE" order=hosts dir=$order.newdir}">Хостов</a>{if $order.field=='hosts'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="25%">
    			<a href="{get_url _CLEAR="PAGE" order=robot_hits dir=$order.newdir}">Хитов роботов</a>{if $order.field=='robot_hits'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    	</tr>
 		{if $data.ITEMS!=0}
			{foreach from=$data.ITEMS item=oItem key=oKey name=fList}
			    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			    	<td>
			    		{$oItem.hour}:00
			    	</td>
			    	<td>
			    		{$oItem.hits}
			    	</td>
					<td>{$oItem.hosts}</td>
					<td>{$oItem.robot_hits}</td>
				</tr>
			{/foreach}
		{/if}
    </table>
</div>
{/ksTab}
{ksTab NAME="График" onActivate="drawChart();"}
<div id="chart" class="users" style="width:100%;height:400px;background:white;">
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
		data1 =[],
		data2 =[];
	var data3={ldelim}{foreach from=$data.HOURS item=oItem key=oKey name=hits}'{$oKey}':{$oItem.hits}{if not $smarty.foreach.hits.last},{/if}{/foreach}{rdelim};
	var data4={ldelim}{foreach from=$data.HOURS item=oItem key=oKey name=hosts}'{$oKey}':{$oItem.hosts}{if not $smarty.foreach.hosts.last},{/if}{/foreach}{rdelim};
	var data5={ldelim}{foreach from=$data.HOURS item=oItem key=oKey name=robot_hits}'{$oKey}':{$oItem.robot_hits}{if not $smarty.foreach.robot_hits.last},{/if}{/foreach}{rdelim};
	//Пересчет данных чтобы получить верные даты
	var clock=new Date;
	var dateFrom=0;
	var dateTo=24;
	labels=[];
	{literal}
	var step=1;
	//alert(step);
	var j=0;
	var sum=0,sum2=0,sum3=0;
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
				sum3+=data5[i];
			}
			j++;
		}
		if(j>=step)
		{
			labels.push(i+':00');
			data.push(sum);
			data1.push(sum3);
			data2.push(sum2);
			sum=0;
			sum2=0;
			sum3=0;
			j=0;
		}
	}
	data.push(data[0]);
	data1.push(data1[0]);
	data2.push(data2[0]);
	labels.push('24:00');
	var chartDiv=document.getElementById('chart');
	// Draw
	
	var width = chartDiv.offsetWidth-20,
	    height = 400,
	    leftgutter = 30,
	    bottomgutter = 60,
	    topgutter = 20,
	    rightgutter=0,
	    gridX=24;
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
	    X = (width-leftgutter-rightgutter) / (labels.length-1),
	    max = Math.max(Math.max.apply(Math, data),Math.max.apply(Math,data1))+10,
	    Y = (height - bottomgutter - topgutter) / max;
	var path_a = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter+2-Y*data[0]), 
		path = r.path({stroke: color, "stroke-width": lineWidth,"opacity":1}).moveTo(leftgutter,height-bottomgutter-Y*data[0]),
	    bgp = r.path({stroke: "none", opacity: .3, fill: color}).moveTo(leftgutter, height - bottomgutter),
	    path_a1 = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter+2-Y*data1[0]),
	    path1 = r.path({stroke: color1, "stroke-width": 4}).moveTo(leftgutter,height-bottomgutter-Y*data1[0]),
	    bgp1 = r.path({stroke: "none", opacity: .3, fill: color1}).moveTo(leftgutter, height - bottomgutter),
	    path_a2 = r.path({stroke: "#fff", "stroke-width": lineWidth*2,"opacity":1,"stroke-linejoin":"round"}).moveTo(leftgutter,height-bottomgutter+2-Y*data2[0]),
	    path2 = r.path({stroke: color2, "stroke-width": 4}).moveTo(leftgutter,height-bottomgutter-Y*data2[0]),
	    bgp2 = r.path({stroke: "none", opacity: .3, fill: color2}).moveTo(leftgutter, height - bottomgutter),
	 
	    frame = r.rect(10, 10, 100, 60, 2).attr({fill: "#fff6c4", stroke: "#f6db9a", "stroke-width": 1}).hide(),
	    label = [],
	    is_label_visible = false,
	    leave_timer,
	    blanket = r.set();
	label[0] = r.text(60, 10, "24 hits").attr(txt1).hide();
	label[1] = r.text(60, 40, "22 September 2008").attr(txt1).attr({fill: "#aaaaaa"}).hide();
	dot = r.circle(60, 10, 7).attr({fill: color, stroke: "#000", opacity:0});
	//рисуем сетку по X
	var axesY=r.path({"stroke-width":1,"stroke":"#e5e5e5"}).moveTo(leftgutter,topgutter).lineTo(leftgutter,height-bottomgutter+20);
	r.text(leftgutter+30, height-bottomgutter+15, labels[0]).attr(axesStyle).toBack();
	var rgridX=r.path(gridStyle),sx=(width-rightgutter-leftgutter)/gridX,sy=(height-bottomgutter-topgutter)/gridY;
    for(var i=1;i<=gridX;i++)	
    {
    	rgridX.moveTo(leftgutter+sx*i,topgutter).lineTo(leftgutter+sx*i,height-bottomgutter);
    	if(i%2==0)
    	{
    	  	axesY.moveTo(leftgutter+sx*i,height-bottomgutter).lineTo(leftgutter+sx*i,height-bottomgutter+20);
    		if(i!=gridX) r.text(leftgutter+sx*i+30, height-bottomgutter+15, labels[Math.min(Math.ceil(labels.length/gridX*i),labels.length-1)]).attr(axesStyle).toBack();
    	}
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
	
	bgp2.lineTo(x, height - bottomgutter).andClose().toBack();
	bgp1.lineTo(x, height - bottomgutter).andClose().toBack();
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
	<dt>{#title_activity#}</dt>
	<dd>{#hint_activity#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}  

