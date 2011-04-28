<?php
/**
 * \file hits.php
 * Файл для работы с хитами в час в статистике
 * Файл проекта kolos-cms.
 * 
 * Создан 24.08.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$page='_activity';

include_once MODULES_DIR.'/statistics/libs/class.CStatisticsHits.php';

$obHits=new CStatisticsHits();

// Обработка порядка вывода элементов
$arSortFields=Array('hour','hits','hosts','robot_hits');
$sOrderField=(in_array($_REQUEST['order'],$arSortFields))?$_REQUEST['order']:'hour';
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
	$obFilter->AddField(array('FIELD'=>'date','TYPE'=>'DATE','METHOD'=>'<>','DEFAULT'=>array(date('r',time()-86400*30),date('r',time()))));
	$arFilter=$obFilter->GetFilter();
	$obFilter->SetSmartyFilter('filter');
	$arTitles=array(
			'date'=>'Дата');
	$smarty->assign('ftitles',$arTitles);
}
else
{
	$arFilter=array(
		'>date'=>time()-86400*30,
		'<date'=>time(),
	);
} 
//Подготавливаем постраничный вывод
$arRecords=$obHits->GetListInHour(Array($sOrderField=>$sOrderDir),$arFilter);
$arResult['ITEMS']=$arRecords;
$arRecords=$obHits->GetListInHour(Array('hour'=>'asc'),$arFilter);
foreach($arRecords as $arItem)
{
	$arResult['HOURS'][$arItem['hour']]=$arItem;
}

$smarty->assign('data',$arResult);
$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
$page='_activity';
?>
