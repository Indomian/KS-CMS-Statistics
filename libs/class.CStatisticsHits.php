<?php
/**
 * \file class.CStatisticsHits.php
 * Файл с классом для работы с таблицей хитов
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

/**
 * Класс для работы с таблицей хитов модуля статистики 
 */
class CStatisticsHits extends CObject
{
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_hits')
	{
		parent::__construct($sTable);
		$this->arFields=array('date','hits','hosts');
	}
	
	/**
	 * Метод позволяет получить количество записей по дням
	 */
	function GetListByDays($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		global $ks_db;
		$this->arTables=array();
		$this->arJoinTables=array();
		/*Генерируем строку полей (SELECT)*/
		$fields=$this->_GenSelect($arSelect);
		/*Генерируем строку порядка (ORDER BY)*/
		$sOrder=$this->_GenOrder($arOrder);
		/*Генерируем строку фильтра (WHERE)*/
		$sWhere=$this->_GenWhere($arFilter);
		/*Генерируем строку группировки (GROUP BY)*/
		$sGroupBy=$this->_GenGroup($arGroupBy);
		/*Генерируем список таблиц (FROM)*/
		$sFrom=$this->_GenFrom();
		$tableCode=$this->arTables[$this->sTable];
		if($tableCode!='') $tableCode.='.';
		$arResult=array();
		$query="SELECT {$tableCode}day as date, sum({$tableCode}hits) as hits, sum({$tableCode}hosts) as hosts,sum({$tableCode}robot_hits) as robot_hits FROM $sFrom $sWhere GROUP BY day $sOrder $limits ";
		$ks_db->query($query);
		while($arRow=$ks_db->get_row())
		{
			$arResult[]=$arRow;
		}
		return $arResult;
	}
	
	/**
	 * Метод позволяет получить сумму по количеству посещений в час
	 */
	function GetListInHour($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		global $ks_db;
		$this->arTables=array();
		$this->arJoinTables=array();
		/*Генерируем строку полей (SELECT)*/
		$fields=$this->_GenSelect($arSelect);
		/*Генерируем строку порядка (ORDER BY)*/
		$sOrder=$this->_GenOrder($arOrder);
		/*Генерируем строку фильтра (WHERE)*/
		$sWhere=$this->_GenWhere($arFilter);
		/*Генерируем строку группировки (GROUP BY)*/
		$sGroupBy=$this->_GenGroup($arGroupBy);
		/*Генерируем список таблиц (FROM)*/
		$sFrom=$this->_GenFrom();
		$tableCode=$this->arTables[$this->sTable];
		if($tableCode!='') $tableCode.='.';
		$arResult=array();
		$query="SELECT {$tableCode}day as date, {$tableCode}hour as hour, sum({$tableCode}hits) as hits, sum({$tableCode}hosts) as hosts,sum({$tableCode}robot_hits) as robot_hits FROM $sFrom $sWhere GROUP BY hour $sOrder $limits ";
		$ks_db->query($query);
		while($arRow=$ks_db->get_row())
		{
			$arResult[]=$arRow;
		}
		return $arResult;
	}
	
}
?>
