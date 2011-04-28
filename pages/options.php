<?php
/**
 * \file options.php
 * Файл настроек модуля "Статистика"
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

global $KS_URL;
$module_name='statistics';

//Проверка прав доступа
if($USER->GetLevel($module_name)>0) throw new CAccessError('SYSTEM_NOT_ACCESS_SETTINGS');
 
//Подключаем необходимые библиотеки
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';
require_once MODULES_DIR.'/main/libs/class.CConfigParser.php';

//Инициализируем файл конфигурации
$obConfig=new CConfigParser($module_name);
$obConfig->LoadConfig();

//Получаем права на доступ к модулю
$USERGROUP=new CUserGroup;
$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
//Получаем список доступов для модуля
$arAccess['module']=$KS_MODULES->GetAccessArray($module_name);
$obAccess=new CModulesAccess();
$arAccess['levels']=$obAccess->GetList(array('id'=>'asc'),array('module'=>$module_name));
unset($arAccess['levels'][$module_name]);
$arRes=array();
foreach($arAccess['levels'] as $key=>$item)
{
	$arRes[$item['group_id']]=$item;
}
$arAccess['levels']=$arRes;

if ($_POST['action']=='save')
{
	try
   	{
   		$obConfig->Set('active',intval($_POST['active']));
   		$obConfig->Set('save_admin_path',intval($_POST['save_admin_path']));
   		$obConfig->Set('save_admin',intval($_POST['save_admin']));
   		$obConfig->Set('countRobots',intval($_POST['countRobots']));
   		$slt=intval($_POST['sc_sidLifeTime']);
   		$slt=abs($slt);
   		$obConfig->Set('sidLifeTime',($slt>240?240*3600:$slt*3600));
   		$obConfig->Set('sidCount',abs(intval($_POST['sc_sidCount'])));
   		$slt=abs(intval($_POST['sc_movesLifeTime']));
   		$obConfig->Set('movesLifeTime',($slt>240?240*3600:$slt*3600));
   		$obConfig->Set('movesCount',abs(intval($_POST['sc_movesCount'])));
				
		$obConfig->WriteConfig();   		
   		//Выполняем сохранение прав доступа
   		if(is_array($_POST['sc_groupLevel']))
   		{
	   		foreach($_POST['sc_groupLevel'] as $key=>$value)
			{
				//echo min($value);
				$obAccess->Set($key,$module_name,min($value));
			}	
   		}
   		$KS_URL->redirect('/admin.php?module='.$module_name.'&page=options');
   	}
   	catch (EXCEPTION $e)
   	{
   		$smarty->assign('last_error',$e);
   	}
}

$data=array();
$obusers=new CUserGroup();
$data['usergroups']=$obusers->GetList();
$data['config']=$obConfig->GetConfig();
$data['access']=$arAccess;
$smarty->assign('data',$data);
$page='_options';
?>