<?php
/**
 * \file class.CStatisticsStrings.php
 * В файле находится класс работы со строками модуля статистики
 * Файл проекта kolos-cms.
 * 
 * Создан 18.03.2010
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 2.5.5
 * @todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

class CStatisticsStrings extends CObject
{
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_strings')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','text','v');
	}
}
?>
