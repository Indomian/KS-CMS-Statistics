<?php
/**
 * \file guests.php
 * Файл для работы со списком посетителей сайта
 * Файл проекта kolos-cms.
 * 
 * Создан 25.08.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$page='_guests';

include_once MODULES_DIR.'/statistics/libs/class.CStatistics.php';
include_once MODULES_DIR.'/statistics/libs/class.CStatisticsSessions.php';

$obGuests=new CStatisticsSessions();

// Обработка порядка вывода элементов
$arSortFields=Array('id','user_ip','user_id','first_in','last_active','hits');
$sOrderField=(in_array($_REQUEST['order'],$arSortFields))?$_REQUEST['order']:'last_active';
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
	$obFilter->AddField(array('FIELD'=>'id'));
	$obFilter->AddField(array('FIELD'=>'last_active','TYPE'=>'DATE','METHOD'=>'<>','DEFAULT'=>array(date('r',time()-86400*30),date('r',time()+1000))));
	$obFilter->AddField(array('FIELD'=>'first_in','TYPE'=>'DATE','METHOD'=>'<>'));
	$arFilter=$obFilter->GetFilter();
	$obFilter->SetSmartyFilter('filter');
	$arTitles=array(
			'last_active'=>'Последняя активность',
			'first_in'=>'Первый вход',
			'id'=>'Код посетителя'
			);
	$smarty->assign('ftitles',$arTitles);
}
else
{
	$arFilter=array(
		'>last_active'=>time()-86400*30,
		'<=last_active'=>time(),
	);
} 
//Подготавливаем постраничный вывод
$obGuests->Count($arFilter);
$obPages = new CPageNavigation($obGuests);
$pages = $obPages->GetPages();
$arSelect=array(
	'id',
	'user_id',
	'user_ip',
	'last_active',
	'first_in',
	'sess_id',
	'hits',
	'users.id',
	'users.title',
);
$arFilter['<?'.$obGuests->sTable.'.user_id']='users.id';
$arResult['ITEMS']=$obGuests->GetList(Array($sOrderField=>$sOrderDir),$arFilter,$obPages->GetLimits(),$arSelect);
foreach($arResult['ITEMS'] as $key=>$arItem)
{
	$arResult['ITEMS'][$key]['user_ip']=CStatistics::IntToIP($arResult['ITEMS'][$key]['user_ip']);
}

$smarty->assign('data',$arResult);
$smarty->assign('pages',$pages);
$smarty->assign('order',Array('newdir'=>$sNewDir,'curdir'=>$sOrderDir,'field'=>$sOrderField));
?>
