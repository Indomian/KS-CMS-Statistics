{config_load file=admin.conf section=statistics}
<script type="text/javascript" src="/js/floatmessage.js"></script>
<script type="text/javascript" src="/js/raphael.js"></script>
<script type="text/javascript" src="/js/statistics/raphael_extensions.js"></script>
<ul class="nav">
	<li><a href="/admin.php"><img src="{#images_path#}/icons_menu/home.gif" alt="icon_home" height="13" width="13" />&nbsp;<span>{#home#}</span></a></li>      
    <li><a href="/admin.php?module=statistics"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title#}</span></a></li>
    <li><a href="/admin.php?module=statistics&page=agents"><img src="{#images_path#}/icons_menu/arrow.gif" alt="icon_arrow" height="13" width="13" />&nbsp;<span>{#title_agents#}</span></a></li>
</ul>

<h1>{#title_agents#}</h1>
<div class="manage">
	<table class="layout">
		<tr>
			<td width="100%">
				<span>Просмотр браузеров и клиентских программ</span>
			</td>
		</tr>
	</table>
</div>
{include file='admin/interfaces_filter.tpl' data=$filter titles=$ftitles}
{ksTabs NAME=st_agents head_class=tabs2 title_class=bold}
	{ksTab NAME="Таблица" selected=	1}
	{include file="admin/navigation_pagecounter.tpl" pages=$pages}
	<div class="users">
	    <input type="hidden" name="ACTION" value="common">
	    <table class="layout">
	    <tr>
    		<th width="30%">
    			<a href="{get_url _CLEAR="PAGE" order=browser dir=$order.newdir}">Браузер</a>{if $order.field=='browser'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="30%">
	    		<a href="{get_url _CLEAR="PAGE" order=version dir=$order.newdir}">Версия</a>{if $order.field=='version'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="30%">
	    		<a href="{get_url _CLEAR="PAGE" order=os dir=$order.newdir}">ОС</a>{if $order.field=='os'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    		<th width="10%">
	    		<a href="{get_url _CLEAR="PAGE" order=count dir=$order.newdir}">Входов</a>{if $order.field=='count'}&nbsp;<img src="{#images_path#}/{if $order.curdir=='asc'}up{else}down{/if}.gif">{/if}
    		</th>
    	</tr>
 		{if $data.ITEMS!=0}
			{foreach from=$data.ITEMS item=oItem key=oKey name=fList}
			    <tr {if $smarty.foreach.fList.iteration is even}class="odd"{/if}>
			    	<td>
			    		<a href="{get_url fm=GET filter=1 ffbrowser=$oItem.browser}">{$oItem.browser}</a>
			    	</td>
			    	<td>
			    		<a href="{get_url fm=GET filter=1 ffbrowser=$oItem.browser ffversion=$oItem.version}">{$oItem.version}</a>
			    	</td>
			    	<td>
			    		<a href="{get_url fm=GET filter=1 ffos=$oItem.os}">{$oItem.OS_Name|default:$oItem.os}</a>
			    	</td>
			    	<td>
			    		{$oItem.count}
			    	</td>
				</tr>
			{/foreach}
		{/if}
    </table>
</div>
{include file="admin/navigation_pagecounter.tpl" pages=$pages}
{/ksTab}
{ksTab NAME="График" onActivate="drawChart();"}
<h1>График распределения популярности браузеров</h1>
<div id="chart" style="width:100%;height:500px;">
</div>
<script type="text/javascript"><!--
{literal}
var bChart=false;
function drawChart()
{
	if(bChart) return;
	bChart=true;
	// Grab the data
	{/literal}
	{strip}
	var data=
	[
		{foreach from=$data.BROWSERS key=oKey item=oItem name=br}
		{ldelim}
			"name":"{$oKey}",
			"percent":{$oItem.count/$data.TOTAL*100|replace:",":"."},
			"versions":
				[
					{foreach from=$oItem.versions item=oVersion key=iVersion name=vr}
					{ldelim}
						"version":"{$iVersion}",
						"percent":{$oVersion/$oItem.count*100|replace:",":"."}
					{rdelim}
					{if not $smarty.foreach.vr.last},{/if}
					{/foreach}
				],
			"os":[
					{foreach from=$oItem.os item=oVersion key=iVersion name=os}
					{ldelim}
						"os":"{$iVersion|default:"Другая"}",
						"percent":{$oVersion/$oItem.count*100|replace:",":"."}
					{rdelim}
					{if not $smarty.foreach.os.last},{/if}
					{/foreach}
				]
		{rdelim}
		{if not $smarty.foreach.br.last},{/if}
		{/foreach}
	];
	{/strip}
	{literal}
	var chartDiv=document.getElementById('chart');
	// Draw
	
	var width = chartDiv.offsetWidth-20,
		paddingLeft=100,
		paddingRight=50,
		paddingTop=50,
		paddingBottom=50,
	    r=(width-paddingLeft-paddingRight)/6,
	    vr=r+20,
	    or=vr+20,
	    cx=width/2,
	    cy=paddingTop+or,
	    height = or*2+paddingTop+paddingBottom,
	    stroke="#fff",
	    legendStyle={"stroke":"#f6db9a","stroke-width":1,"fill":"#fff6c4"},
	    paper = Raphael("chart", width+20, height),
	    rad = Math.PI / 180,
	    boxWidth=10,
	    boxHeight=10,
        chart = paper.set();
        document.getElementById('chart').style.height=(height+20)+'px';
    
    var angle = 0,
        total = 0,
        start = 0,
        arSectors=new Array(),
        lastSector=false,
        process = function (j) 
        {
            var value = data[j].percent,
            	angleplus = 360 * value / total,
                popangle = angle + (angleplus / 2),
                color = "hsb(" + start + ", .7, .9)",
                ms = 1000,
                delta = 10,
                popx=cx + (or + delta + 55) * Math.cos(angle * rad),
                popy=cy + (or + delta + 25) * Math.sin(angle* rad),
                p1 = paper.ks.sector(cx, cy, or, angle, angle + angleplus, {fill: color, stroke: stroke, "stroke-width": 1}),
                p=paper.ks.sector(cx, cy, or, angle, angle+angleplus, {"fill-opacity":0,"fill":"#f0f","stroke-opacity":0}),
                txt2= paper.text(cx+or+delta+50+boxWidth,j*20+20,data[j].name+" - "+Math.round(value)+'%'),
                rect= paper.rect(cx+or+delta+50-boxWidth/2,j*20+20-boxHeight/2,boxWidth,boxHeight).attr({"stroke":stroke,fill: color}),
                param={"stroke":stroke,"stroke-width":1,fill: color},
                legend=paper.set().hide(),
				textY;
			textY=20;
            p.subsectors=new Array();
			bbox=txt2.getBBox();
            txt2.attr({x:(bbox.x+bbox.width)});
            p.subsectors.push(p1);
            legend.push(paper.rect(-boxWidth,-boxHeight,maxwidth+boxWidth*4,textY+boxHeight*2,2).attr(legendStyle).hide().toBack());
            if(data[j].versions.length>0)
            {
            	var bangle=angle, delta1, substart=start, color1=color,maxwidth;
            	
            	txt2=paper.text(0,0,"Версии:");
            	bbox=txt2.getBBox();
            	legend.push(txt2.attr({x:(bbox.x+bbox.width)}).hide());
            	maxwidth=bbox.width;
            	for(kk=0;kk<data[j].versions.length;kk++)
            	{
            		bcolor1 = "hsb(" + substart + ", 1, 1)";
            		param.fill=bcolor1;
            		delta1=data[j].versions[kk].percent*angleplus/100;
            		var arc=paper.ks.Arc(cx,cy,vr,or,bangle,bangle+delta1,param);
            		if(arc)
            		{
	            		p.subsectors.push(arc.hide());
	               		bangle+=delta1;
	            		substart+=1/data.length/data[j].versions.length;
	            		//Делаем подписи в рамочку легенды
		           		legend.push(paper.rect(0,textY-boxHeight/2,boxWidth,boxHeight).attr(param).hide())
	            		txt2=paper.text(boxWidth+boxWidth,textY,(data[j].versions[kk].version!=''?data[j].versions[kk].version:'Неизвестно')+" - "+(data[j].versions[kk].percent<1?"<1":Math.round(data[j].versions[kk].percent))+"%");
	            		bbox=txt2.getBBox();
	            		if(bbox.width>maxwidth) maxwidth=bbox.width;
	            		legend.push(txt2.attr({x:(bbox.x+bbox.width)}).hide());
	            		textY+=20;
	            	}
				}
            }
            if(data[j].os.length>0)
            {
            	var bangle=angle;
            	var delta1;
            	var substart=start;
            	var color1=color;
            	txt2=paper.text(0,textY,"Операционные системы:");
            	bbox=txt2.getBBox();
            	legend.push(txt2.attr({x:(bbox.x+bbox.width)}).hide());
            	if(bbox.width>maxwidth) maxwidth=bbox.width;
            	textY+=20;
               	for(kk=0;kk<data[j].os.length;kk++)
            	{
            		color1 = "hsb(" + substart + ", .6, .8)";
            		param.fill=color1;
            		delta1=data[j].os[kk].percent*angleplus/100;
               		p.subsectors.push(paper.ks.sector(cx,cy,r,bangle,delta1+bangle,param).hide());
               		bangle+=delta1;
               		//substart+=0.1/data[j].versions.length;
               		substart+=1/data.length/data[j].os.length;
               		//Делаем подписи в рамочку легенды
	           		legend.push(paper.rect(0,textY-boxHeight/2,boxWidth,boxHeight).attr(param).hide())
            		txt2=paper.text(boxWidth+boxWidth,textY,data[j].os[kk].os+" - "+(data[j].os[kk].percent<1?"<1":Math.round(data[j].os[kk].percent))+"%");
            		bbox=txt2.getBBox();
            		if(bbox.width>maxwidth) maxwidth=bbox.width;
            		legend.push(txt2.attr({x:(bbox.x+bbox.width)}).hide());
            		textY+=20;
            	}
            }
            legend[0].attr({x:-boxWidth,y:-boxHeight,width:maxwidth+boxWidth*4,height:textY+boxHeight*2});
            
            popx=r*1.1+cx+paddingLeft;
            popy=10;
            
            if((angle+angleplus/2)>90)
            {
            	popx=paddingLeft;
            	popy=10;
            }
            if((angle+angleplus/2)>180)
            {
            	popx=paddingLeft;
            	popy=paper.height-legend[0].attrs.height;
            }
            if((angle+angleplus)>270)
            {
            	popx=r*1.1+cx+paddingLeft;
            	popy=paper.height-legend[0].attrs.height;
            }
			if(popy<10) popy=10;
			if(popx<0) popx=0;
			if(popy+legend[0].attrs.height>paper.height) popy=paper.height-legend[0].attrs.height;
			if(popx+legend[0].attrs.width>paper.width) popy=paper.width-legend[0].attrs.width;
			legend.translate(popx,popy);
            p.mouseover(function () {
            	if(lastSector!=false)
            	{
            		lastSector.attr({scale: [1, 1, cx, cy]});
                	lastSector.subsectors[0].attr({scale:[1, 1, cx, cy]});
                	lastSector.legend.hide();
                	for(i=1;i<lastSector.subsectors.length;i++)
                	{
                		lastSector.subsectors[i].hide().attr({scale:[1, 1, cx, cy]});
        	        }
        	    }
        	    lastSector=p;
                /*for(j=0;j<arSectors.length;j++)
                {
                	arSectors[j].attr({scale: [1, 1, cx, cy]});
                	arSectors[j].subsectors[0].attr({scale:[1, 1, cx, cy]});
                	arSectors[j].legend.hide();
                	for(i=1;i<arSectors[j].subsectors.length;i++)
                	{
                		arSectors[j].subsectors[i].hide().attr({scale:[1, 1, cx, cy]});
        	        }
                }*/
                p.attr({scale: [1.1, 1.1, cx, cy]});
                p.subsectors[0].attr({scale:[1.1, 1.1, cx, cy]});
                for(i=1;i<p.subsectors.length;i++)
                {
                	p.subsectors[i].show().attr({scale:[1.2, 1.2, cx, cy]});
                }
                legend.toFront().show();
                p.toFront();
            });
            p.toFront();
            p.legend=legend;
            angle += angleplus;
            //chart.push(p);
            //chart.push(legend);
            arSectors.push(p);
            start += 1/data.length;
        };
    for (var i = 0, ii = data.length; i < ii; i++) {
        total += data[i].percent;
    }
    for (var i = 0; i <ii; i++) {
        process(i);
    }
}
{/literal}
//-->
</script>
{/ksTab}
{/ksTabs}

{strip}
<dl class="def" style="background:#FFF6C4 url('{#images_path#}/big_icons/statistics.gif') left 50% no-repeat;{if $smarty.cookies.showHelpBar==1}display:none;{/if}">
	<dt>{#title_agents#}</dt>
	<dd>{#hint_agents#}</dd>        
</dl> 
<div class="content_arrow_{if $smarty.cookies.showHelpBar==1}down{else}up{/if}" onclick="ToggleHelpBar(this)" style="cursor:pointer;">&nbsp;</div>
{/strip}  

