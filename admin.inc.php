<?php
/**
 * \file admin.inc.php
 * Файл администрирования модуля Статистика
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

$module_name='statistics';

$access_level=$USER->GetLevel($module_name);

if($access_level>3) throw new CAccessError('STATISTICS_ACCESS_DENIED');

if(!preg_match('#[a-z0-9]*#',$_GET['page'])) throw new CError('SYSTEM_WRONG_ADMIN_PATH',1001);

if(file_exists(MODULES_DIR.'/'.$module_name.'/pages/'.$_GET['page'].'.php'))
{
	include  MODULES_DIR.'/'.$module_name.'/pages/'.$_GET['page'].'.php';
	$this->page=$_GET['page'];
}
else
{
	$KS_URL->redirect('/admin.php?module='.$module_name.'&page=hits');
}
?>
