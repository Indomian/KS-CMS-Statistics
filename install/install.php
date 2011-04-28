<?php
/**
 * \file install.php
 * Файл выполняющий операции по установке модуля статистики
 * Файл проекта kolos-cms.
 * 
 * Создан 24.08.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

global $ks_db,$KS_EVENTS_HANDLER;

$module_name='statistics';
$sContent='';

include 'description.php';
require_once MODULES_DIR.'/main/libs/class.CUserGroup.php';
require_once MODULES_DIR.'/main/libs/class.CAccess.php';

//Получаем список таблиц системы
$ks_db->query("SHOW TABLES");
while($arRow=$ks_db->get_row())
{
	$arTables[]=current($arRow);
}

//Проверяем наличие уже созданных таблиц
if(in_array('ks_statistics_hits',$arTables))
{
	$bTableHitsExists=true;
}
$bTableRefersExists=in_array('ks_statistics_refers',$arTables);
$bTableAcceptorsExists=in_array('ks_statistics_acceptors',$arTables);
$bTableMovesExists=in_array('ks_statistics_moves',$arTables);
$bTableUsersExists=in_array('ks_statistics_sessions',$arTables);
$bTableAgentsExists=in_array('ks_statistics_agents',$arTables);

//Определяем режим работы
if(array_key_exists('go',$_POST))
{
	//Операции по созданию таблиц модуля статистики
	$sContent="go";
	//Создаем таблицу хитов
	if($bTableHitsExists)
	{
		$action=$_POST['ks_install_hits'];
		if($action=="clear")
		{
			$ks_db->query("TRUNCATE TABLE ks_statistics_hits");
			$sContent.='<br/>Таблица "Посещения" очищена';
		}
		elseif($action=="new")
		{
			$ks_db->query("DROP TABLE ks_statistics_hits");
			$sContent.='<br/>Таблица "Посещения" была заменена';
		}
		else
		{
			$sContent.='<br/>Таблица "Посещение" осталась без изменений'; 
		}
	}
	$visitTable="CREATE TABLE IF NOT EXISTS ks_statistics_hits (" .
			"date int not null default 0 primary key," .
			"hour int not null default 0,".
			"day int not null default 0,".
			"hits int not null default 0,". 
			'robot_hits int not null default 0,'.
			"hosts int not null default 0) type=MyISAM;";
	$ks_db->query($visitTable);
	if($bTableRefersExists)
	{
		$action=$_POST['ks_install_refers'];
		if($action=="clear")
		{
			$ks_db->query("TRUNCATE TABLE ks_statistics_refers");
			$sContent.='<br/>Таблица "Реферы" очищена';
		}
		elseif($action=="new")
		{
			$ks_db->query("DROP TABLE ks_statistics_refers");
			$sContent.='<br/>Таблица "Реферы" была заменена';
		}
		else
		{
			$sContent.='<br/>Таблица "Реферы" осталась без изменений'; 
		}
	}
	$referTable="CREATE TABLE IF NOT EXISTS ks_statistics_refers (".
			"id int not null auto_increment primary key," .
			"url varchar(255) default '' unique," .
			"hits int not null default 0) type=MyISAM;";
	$ks_db->query($referTable);
	//Создаем таблицу со списком страниц на которые совершены переходы
	if($bTableAcceptorsExists)
	{
		$action=$_POST['ks_install_acceptors'];
		if($action=="clear")
		{
			$ks_db->query("TRUNCATE TABLE ks_statistics_acceptors");
			$sContent.='<br/>Таблица "Адреса переходов" очищена';
		}
		elseif($action=="new")
		{
			$ks_db->query("DROP TABLE ks_statistics_acceptors");
			$sContent.='<br/>Таблица "Адреса переходов" была заменена';
		}
		else
		{
			$sContent.='<br/>Таблица "Адреса переходов" осталась без изменений'; 
		}
	}
	$referTable="CREATE TABLE IF NOT EXISTS ks_statistics_acceptors (".
			"id int not null auto_increment primary key," .
			"url varchar(255) default '' unique," .
			"hits int not null default 0) type=MyISAM;";
	$ks_db->query($referTable);
	//Создаем таблицу со списком всех переходов
	if($bTableMovesExists)
	{
		$action=$_POST['ks_install_moves'];
		if($action=="clear")
		{
			$ks_db->query("TRUNCATE TABLE ks_statistics_moves");
			$sContent.='<br/>Таблица "Переходы" очищена';
		}
		elseif($action=="new")
		{
			$ks_db->query("DROP TABLE ks_statistics_moves");
			$sContent.='<br/>Таблица "Переходы" была заменена';
		}
		else
		{
			$sContent.='<br/>Таблица "Переходы" осталась без изменений'; 
		}
	}
	$referTable="CREATE TABLE IF NOT EXISTS ks_statistics_moves (".
			"id int not null auto_increment primary key," .
			"date int not null default 0,".
			"move_from int not null default 0," .
			"move_to int not null default 0," .
			"sid char(32) default '') type=MyISAM;";
	$ks_db->query($referTable);
	//Создаем таблицу со списком всех посетителей
	if($bTableMovesExists)
	{
		$action=$_POST['ks_install_users'];
		if($action=="clear")
		{
			$ks_db->query("TRUNCATE TABLE ks_statistics_sessions");
			$sContent.='<br/>Таблица "Посетители" очищена';
		}
		elseif($action=="new")
		{
			$ks_db->query("DROP TABLE ks_statistics_sessions");
			$sContent.='<br/>Таблица "Посетители" была заменена';
		}
		else
		{
			$sContent.='<br/>Таблица "Посетители" осталась без изменений'; 
		}
	}
	$referTable="CREATE TABLE IF NOT EXISTS ks_statistics_sessions (".
			"id int not null auto_increment primary key," .
			"sess_id char(32) not null unique,".
			"first_in int not null default 0," .
			"last_active int not null default 0," .
			"hits char(32) default ''," .
			"user_id int not null default 0," .
			"user_ip int not null default 0) type=MyISAM;";
	$ks_db->query($referTable);
	//Создаем таблицу со списком всех браузеров и роботов которыми подключились
	if($bTableMovesExists)
	{
		$action=$_POST['ks_install_agents'];
		if($action=="clear")
		{
			$ks_db->query("TRUNCATE TABLE ks_statistics_agents");
			$sContent.='<br/>Таблица "Клиенты" очищена';
		}
		elseif($action=="new")
		{
			$ks_db->query("DROP TABLE ks_statistics_agents");
			$sContent.='<br/>Таблица "Клиенты" была заменена';
		}
		else
		{
			$sContent.='<br/>Таблица "Клиенты" осталась без изменений'; 
		}
	}
	$referTable="CREATE TABLE IF NOT EXISTS ks_statistics_agents (".
			"id int not null auto_increment primary key," .
			"browser char(255) not null default '',".
			"os char(32) not null default '',".
			"version char(32) not null default '',".
			"count int not null default 0) type=MyISAM;";
	$ks_db->query($referTable);
	//Добавляем событие в дерево событий
	$KS_EVENTS_HANDLER->AddStaticEvent('main','onUserObjectInit',array('hFile' => 'statistics.php', 'hFunc'=>array('CStatistics','onUserObjectInit')));
	$KS_EVENTS_HANDLER->AddStaticEvent('main','onUserSessionUpdate',array('hFile' => 'statistics.php', 'hFunc'=>array('CStatistics','onUserSessionUpdate')));
	$KS_EVENTS_HANDLER->AddStaticEvent('main','onLogin',array('hFile' => 'statistics.php', 'hFunc'=>array('CStatistics','onLogin')));
	$KS_EVENTS_HANDLER->AddStaticEvent('main','onBeforeLogout',array('hFile' => 'statistics.php', 'hFunc'=>array('CStatistics','onBeforeLogout')));
	if($KS_EVENTS_HANDLER->SaveToFile()==0)
		throw new CModuleError('MAIN_MODULE_INSTALL_EVENTS_SYS_ERROR');
	$sContent.='<br/>Система событий успешно обновлена';
	//Прописываем уровни доступа для всех групп
	$USERGROUP=new CUserGroup;
	$arAccess['groups']=$USERGROUP->GetList(array('title'=>'asc'));
	$obAccess=new CModulesAccess();
	//Выполняем сохранение прав доступа
   	if(is_array($arAccess['groups']))
   	{
	   	foreach($arAccess['groups'] as $key=>$value)
		{
			if($value['id']=='1')
			{
				$obAccess->Set($value['id'],$module_name,0);
			}
			else
			{
				$obAccess->Set($value['id'],$module_name,10);
			}
		}	
   	}
	
	$query="INSERT INTO ks_main_modules(name,URL_ident,directory,include_global_template,active,orderation,hook_up,allow_url_edit) VALUES ('".$arDescription['title']."','','$module_name',0,1,10,1,0)";
	$ks_db->query($query);
	$sContent.="<br/>Модуль добавлен в список модулей системы";
	$sContent.="<br/>Модуль успешно установлен";
}
else
{
	//Если мы не выполняем работу то надо сообщить о настройках модуля перед установкой
	$sContent=$arDescription['description'].'<br/>';
	if($bTableHitsExists)
	{
		$sContent.='<div class="atention">Внимание! Таблица "Посещяемость" уже существует.' .
				' Для установки модуля необходимо выбрать соответсвующее поведение. Мы рекомендуем' .
				' "заменить таблицу".</div><br/>' .
				'Таблица "Посещаемость": <select name="ks_install_hits">' .
				'<option value="new">Заменить таблицу</option>' .
				'<option value="clear">Очистить значения в таблице</option>' .
				'<option value="keep">Не изменять таблицу</option>' .
				'</select><br/>';
	}
	if($bTableRefersExists)
	{
		$sContent.='<div class="atention">Внимание! Таблица "Реферы" уже существует.' .
				' Для установки модуля необходимо выбрать соответсвующее поведение. Мы рекомендуем' .
				' "заменить таблицу".</div><br/>' .
				'Таблица "Реферы": <select name="ks_install_refers">' .
				'<option value="new">Заменить таблицу</option>' .
				'<option value="clear">Очистить значения в таблице</option>' .
				'<option value="keep">Не изменять таблицу</option>' .
				'</select><br/>';
	}
	if($bTableAcceptorsExists)
	{
		$sContent.='<div class="atention">Внимание! Таблица "Адреса переходов" уже существует.' .
				' Для установки модуля необходимо выбрать соответсвующее поведение. Мы рекомендуем' .
				' "заменить таблицу".</div><br/>' .
				'Таблица "Адреса переходов": <select name="ks_install_acceptors">' .
				'<option value="new">Заменить таблицу</option>' .
				'<option value="clear">Очистить значения в таблице</option>' .
				'<option value="keep">Не изменять таблицу</option>' .
				'</select><br/>';
	}
	if($bTableMovesExists)
	{
		$sContent.='<div class="atention">Внимание! Таблица "Переходы" уже существует.' .
				' Для установки модуля необходимо выбрать соответсвующее поведение. Мы рекомендуем' .
				' "заменить таблицу".</div><br/>' .
				'Таблица "Переходы": <select name="ks_install_moves">' .
				'<option value="new">Заменить таблицу</option>' .
				'<option value="clear">Очистить значения в таблице</option>' .
				'<option value="keep">Не изменять таблицу</option>' .
				'</select><br/>';
	}
	if($bTableUsersExists)
	{
		$sContent.='<div class="atention">Внимание! Таблица "Посетители" уже существует.' .
				' Для установки модуля необходимо выбрать соответсвующее поведение. Мы рекомендуем' .
				' "заменить таблицу".</div><br/>' .
				'Таблица "Посетители": <select name="ks_install_users">' .
				'<option value="new">Заменить таблицу</option>' .
				'<option value="clear">Очистить значения в таблице</option>' .
				'<option value="keep">Не изменять таблицу</option>' .
				'</select><br/>';
	}
	if($bTableAgentsExists)
	{
		$sContent.='<div class="atention">Внимание! Таблица "Клиенты" уже существует.' .
				' Для установки модуля необходимо выбрать соответсвующее поведение. Мы рекомендуем' .
				' "заменить таблицу".</div><br/>' .
				'Таблица "Клиенты": <select name="ks_install_agents">' .
				'<option value="new">Заменить таблицу</option>' .
				'<option value="clear">Очистить значения в таблице</option>' .
				'<option value="keep">Не изменять таблицу</option>' .
				'</select><br/>';
	}
}
?>
