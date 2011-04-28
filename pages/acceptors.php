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

$page='_acceptors';

include_once MODULES_DIR.'/statistics/libs/class.CStatisticsAcceptors.php';

$obAcceptors=new CStatisticsAcceptors();

// Обработка порядка вывода элементов
$arSortFields=Array('url','hits');
$sOrderField=(in_array($_REQUEST['order'],$arSortFields))?$_REQUEST['order']:'hits';
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
	$obFilter->AddField(array('FIELD'=>'url','TYPE'=>'STRING','METHOD'=>'~'));
	$obFilter->AddField(array('FIELD'=>'top_count','DEFAULT'=>'10'));
	$obFilter->AddField(array('FIELD'=>'hits','DEFAULT'=>'100','METHOD'=>'>'));
	$arFilter=$obFilter->GetFilter();
	$obFilter->SetSmartyFilter('filter');
	$arTitles=array(
			'url'=>'Путь',
			'top_count'=>'Популярных путей',
			'hits'=>'Переходов больше чем',
			);
	$smarty->assign('ftitles',$arTitles);
}
else
{
	$arFilter=array();
} 
//Подготавливаем постраничный вывод
$obAcceptors->Count($arFilter);
$obPages = new CPageNavigation($obAcceptors);
$pages = $obPages->GetPages();
$arResult['ITEMS']=$obAcceptors->GetList(Array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits());
if(is_array($arResult['ITEMS'])&&count($arResult['ITEMS'])>0)
{
	foreach($arResult['ITEMS'] as $key=>$arValue)
	{
		$arPath=explode('/',$arValue['url']);
		$sPath='';
		if(count($arPath)>2)
		{
			foreach($arPath as $arDir)
			{
				if($arDir=='') continue;
				$sPath.='/'.$arDir;
				$arResult['ITEMS'][$key]['PATH'][]=array('title'=>$arDir,'url'=>$sPath);
			}
		}
	}
}
$arResult['GRAPH']=$obAcceptors->GetList(array('hits'=>'desc','url'=>'asc'),$arFilter,$arFilter['top_count']);
$hits=$obAcceptors->GetLesserCount(intval($arResult['GRAPH'][count($arResult['GRAPH'])-1]['hits']),$arFilter);
if($hits>0)
	$arResult['GRAPH'][]=array('hits'=>$hits,'url'=>'Другие');
$arResult['HITS']=$obAcceptors->TotalHits($arFilter);
if($arResult['HITS']>0)
{
	foreach($arResult['GRAPH'] as $key=>$arValue)
	{
		$arResult['GRAPH'][$key]['PERCENT']=round($arValue['hits']/$arResult['HITS']*100);
	}
}

$smarty->assign('data',$arResult);
$smarty->assign('pages',$pages);
$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
?>
