<?php
/**
 * \file acceptors.php
 * Файл для вывода информации о страницах входа на сайт.
 * Файл проекта kolos-cms.
 * 
 * Создан 26.08.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$page='_agents';

include_once MODULES_DIR.'/statistics/libs/class.CStatisticsAgents.php';

$obAgents=new CStatisticsAgents();

// Обработка порядка вывода элементов
$arSortFields=Array('browser','os','version','count');
$sOrderField=(in_array($_REQUEST['order'],$arSortFields))?$_REQUEST['order']:'count';
if($_REQUEST['dir']=='asc')
{
	$sOrderDir='asc';
	$sNewDir='desc';
}
else
{
	$sOrderDir='desc';
	$sNewDir='asc';
}

if(class_exists('CFilterFrame'))
{
	$obFilter=new CFilterFrame();
	$obFilter->AddField(array('FIELD'=>'browser','TYPE'=>'STRING','METHOD'=>'~'));
	$obFilter->AddField(array('FIELD'=>'version','TYPE'=>'STRING','METHOD'=>'~'));
	$obFilter->AddField(array('FIELD'=>'os','TYPE'=>'STRING','METHOD'=>'~'));
	$obFilter->AddField(array('FIELD'=>'not_os','TYPE'=>'SELECT','VALUES'=>array('garbega'=>'Отображать роботов','ROBOT'=>'Скрывать роботов')));
	$obFilter->AddField(array('FIELD'=>'top_count','DEFAULT'=>'10'));
	$arFilter=$obFilter->GetFilter();
	$obFilter->SetSmartyFilter('filter');
	$arTitles=array(
			'browser'=>'Браузер',
			'version'=>'Версия',
			'os'=>'Операционная система',
			'not_os'=>'Показывать роботов',
			'top_count'=>'Браузеров на графике',
			);
	$smarty->assign('ftitles',$arTitles);
}
else
{
	$arFilter=array();
} 
if($arFilter['not_os']!='') $arFilter['!os']=$arFilter['not_os'];
unset($arFilter['not_os']);
//Подготавливаем постраничный вывод
$obAgents->Count($arFilter);
$obPages = new CPageNavigation($obAgents);
$pages = $obPages->GetPages();
$arResult['ITEMS']=$obAgents->GetList(Array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits());
$arConfigNames=$this->GetConfigVar('statistics','osNames');
foreach($arResult['ITEMS'] as $key=>$arItem)
{
	$arResult['ITEMS'][$key]['OS_Name']=$arConfigNames[$arItem['os']];
}
$arResult['GRAPH']=$obAgents->GetList(array('count'=>'desc','url'=>'asc'),$arFilter,$arFilter['top_count'],false);
$total=0;
foreach($arResult['GRAPH'] as $arItem)
{
	$total+=$arItem['count'];
}
$arResult['BROWSERS']=array();
foreach($arResult['GRAPH'] as $arItem)
{
	if($arConfigNames[$arItem['os']]!='') $arItem['os']=$arConfigNames[$arItem['os']];
	$arResult['BROWSERS'][$arItem['browser']]['count']+=$arItem['count'];
	$arResult['BROWSERS'][$arItem['browser']]['versions'][$arItem['version']]+=$arItem['count'];
	$arResult['BROWSERS'][$arItem['browser']]['os'][$arItem['os']]+=$arItem['count'];
	$arResult['TOTAL']+=$arItem['count'];
}
foreach($arResult['BROWSERS'] as $browser=>$arItem)
{
	$tmp=0;
	$arTemp=$arItem['versions'];
	foreach($arTemp as $version=>$Count)
	{
		if($Count/$arItem['count']<0.01)
		{
			$tmp+=$Count;
			unset($arItem['versions'][$version]);
			//$arResult['TOTAL']-=$Count;
		}
	}
	if($tmp>0) $arItem['versions']['Другие']=$tmp;
	$arTemp=$arItem['os'];
	$tmp=0;
	foreach($arTemp as $version=>$Count)
	{
		if($Count/$arItem['count']<0.01)
		{
			$tmp+=$Count;
			unset($arItem['os'][$version]);
			//$arResult['TOTAL']-=$Count;
		}
	}
	if($tmp>0) $arItem['os']['Другие']=$tmp;
	$arResult['BROWSERS'][$browser]=$arItem;
}
$smarty->assign('data',$arResult);
$smarty->assign('pages',$pages);
$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
?>
