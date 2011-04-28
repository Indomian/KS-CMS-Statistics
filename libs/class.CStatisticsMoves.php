<?php
/**
 * \file class.CStatisticsMoves.php
 * Файл контейнер для класса CStatisticsMoves
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

class CStatisticsMoves extends CObject
{
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_moves')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','date','move_from','move_to','sid');
	}
	
	/**
	 * Метод получает список выходов с определенного адреса
	 */
	function GetListOuts($arFilter=false,$count=false,$arSelect=false)
	{
		global $ks_db;
		$sWhere=$this->_GenWhere($arFilter);
		$sSelect=$this->_GenSelect($arSelect);
		$sTables=$this->_GenFrom();
		if($count>0) $sLimit=" LIMIT $count ";
		$query="SELECT $sSelect, count(move_to) as num_out FROM $sTables $sWhere GROUP BY move_to ORDER BY num_out DESC $sLimit";
		$ks_db->query($query);
		while($arRow=$ks_db->get_row())
		{
			$arResult[]=$arRow;
		}
		return $arResult;
	}
}
?>
