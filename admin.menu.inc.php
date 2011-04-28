<?php
/**
 * \file admin.menu.inc.php
 * Файл меню модуля статистики
 * Файл проекта kolos-cms.
 * 
 * Создан 24.08.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 * 
 * В данном файл переменная $this является объектом класса CMain.
 * В переменной $row передается информация о модуле из таблицы ks_modules
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

$module_name=$row['directory'];
if($USER->GetLevel($module_name)<=3)
{
	$this->Menu($module_name,'',$row['name'],array('class'=>'manage_statistics'))->
		Menu($module_name,'hits','Посещения',array('class'=>'item.gif'))->
		Menu($module_name,'activity','Активность по часам',array('class'=>'item.gif'))->
		Menu($module_name,'guests','Посетители',array('class'=>'item.gif'))->
		Menu($module_name,'acceptors','Страницы',array('class'=>'item.gif'))->
		Menu($module_name,'refers','Реферы',array('class'=>'item.gif'))->
		Menu($module_name,'moves','Маршруты',array('class'=>'item.gif'))->
		Menu($module_name,'agents','Браузеры',array('class'=>'item.gif'));
	if($USER->GetLevel($module_name)==0)
		$this->Menu($module_name,'options','Настройки',array('class'=>'options.gif'));	
}
?>
