<?php
/**
 * \file class.CStatisticsAcceptors.php
 * Контейнер для класса CStatisticsAcceptors
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

include_once MODULES_DIR.'/statistics/libs/class.CStatisticsPathes.php';

class CStatisticsAcceptors extends CStatisticsPathes
{
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_acceptors')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','url','hits','date');
	}
	
	/**
	 * Класс перекрывает родительский для обработки полей из таблицы ks_statistics_strings
	 */
	function GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		$arFilter['?statistics_strings.id']=$this->sTable.'.url';
		$arFilter['statistics_strings.v']='path';
		$arList=parent::GetList($arOrder,$arFilter,$limit,$arSelect,$arGroupBy);
		foreach($arList as $key=>$arItem)
		{
			$arList[$key]['url']=$arItem['text'];
		}
		return $arList;
	}
}
?>
