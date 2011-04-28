<?php
/**
 * \file class.CStatisticsSessions.php
 * Файл контейнер для класса CStatisticsSessions
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

class CStatisticsSessions extends CObject
{
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_sessions')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','sess_id','first_in','last_active','hits','user_id','user_ip');
	}
}
?>
