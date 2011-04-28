<?php
/**
 * \file moves.php
 * Файл для отображения маршрутов движения по сайту.
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

$page='_moves';

include_once MODULES_DIR.'/statistics/libs/class.CStatisticsMoves.php';

$obMoves=new CStatisticsMoves();

// Обработка порядка вывода элементов
$arSortFields=Array('date','statistics_sessions.id');
$sOrderField=(in_array($_REQUEST['order'],$arSortFields))?$_REQUEST['order']:'date';
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
	$obFilter->AddField(array('FIELD'=>'statistics_sessions.id','TYPE'=>'STRING'));
	$obFilter->AddField(array('FIELD'=>'statistics_refers.url','TYPE'=>'STRING','METHOD'=>'='));
	$obFilter->AddField(array('FIELD'=>'statistics_acceptors.url','TYPE'=>'STRING','METHOD'=>'='));
	$obFilter->AddField(array('FIELD'=>'date','TYPE'=>'DATE','METHOD'=>'<>'));
	$arFilter=$obFilter->GetFilter();
	$obFilter->SetSmartyFilter('filter');
	$arTitles=array(
			'statistics_sessions.id'=>'ID посетителя',
			'statistics_refers.url'=>'со страницы',
			'statistics_acceptors.url'=>'на страницу',
			'date'=>'дата'
			);
	$smarty->assign('ftitles',$arTitles);
}
else
{
	$arFilter=array();
} 
$arFilter['?move_from']='statistics_refers.id';
$arFilter['?move_to']='statistics_acceptors.id';
$arFilter['?sid']='statistics_sessions.sess_id';
$arSelect=array(
	'id',
	'move_from',
	'move_to',
	'date',
	'sid',
	'statistics_refers.url',
	'statistics_refers.hits',
	'statistics_acceptors.url',
	'statistics_acceptors.hits',
	'statistics_sessions.user_id',
	'statistics_sessions.id',
);
$maxOuts=20;
//Подготавливаем постраничный вывод
$obMoves->Count($arFilter);
$obPages = new CPageNavigation($obMoves);
$pages = $obPages->GetPages();
$arResult['ITEMS']=$obMoves->GetList(Array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits(),$arSelect);
if($arFilter['statistics_refers.url']!='')
{
	$arResult['WAYS']=$obMoves->GetListOuts($arFilter,$maxOuts,$arSelect);
	$arResult['FROMS']=array();
	$arResult['TOS']=array();
	foreach($arResult['WAYS'] as $arItem)
	{
		if(!array_key_exists($arItem['move_from'],$arResult['FROMS']))
		{
			$arResult['FROMS'][$arItem['move_from']]=$arItem['statistics_refers_url'];
		}
		if(!array_key_exists($arItem['move_to'],$arResult['TOS']))
		{
			$arResult['TOS'][$arItem['move_to']]=$arItem['statistics_acceptors_url'];
		}
	}
}
if($arFilter['statistics_sessions.id']!='')
{
	$arResult['USER_WAYS']=$obMoves->GetList(array('date'=>'desc'),$arFilter,$maxOuts,$arSelect);
	for($i=0;$i<count($arResult['USER_WAYS']);$i++)
	{
		$arResult['USER_WAYS'][$i]['LENGTH']=-$arResult['USER_WAYS'][$i+1]['date']+$arResult['USER_WAYS'][$i]['date'];
	}
}
$smarty->assign('data',$arResult);
$smarty->assign('pages',$pages);
$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
?>
