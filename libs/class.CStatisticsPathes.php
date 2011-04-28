<?php
/**
 * \file class.CStatisticsPathes.php
 * Контейнер для класса CStatisticsPathes
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

class CStatisticsPathes extends CObject
{
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_acceptors')
	{
		parent::__construct($sTable);
	}
	
	/**
	 * Метод получает сумму хитов по страницам не вошедшим в указанный топ
	 */
	function GetLesserCount($from_hits,$arFilter=false)
	{
		global $ks_db;
		$sWhere=$this->_GenWhere($arFilter);
		if($sWhere=='') $sWhere='WHERE '; else $sWhere.=' AND ';
		$sTables=$this->_GenFrom();
		$query="SELECT sum(hits) as sum FROM $sTables $sWhere hits<$from_hits";
		$ks_db->query($query);
		$arResult=$ks_db->get_row();
		return $arResult['sum'];
	}
	
	/**
	 * Метод возвращает общее количество хитов
	 */
	function TotalHits($arFilter=false)
	{
		global $ks_db;
		$sWhere=$this->_GenWhere($arFilter);
		$sTables=$this->_GenFrom();
		$query="SELECT sum(hits) as sum FROM $sTables $sWhere ";
		$ks_db->query($query);
		$arResult=$ks_db->get_row();
		return $arResult['sum'];
	}
}
?>
