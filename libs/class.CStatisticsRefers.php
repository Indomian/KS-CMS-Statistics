<?php
/**
 * \file class.CStatisticsRefers.php
 * Контейнер для класса CStatisticsRefers
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

class CStatisticsRefers extends CStatisticsPathes
{
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_refers')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','url','hits');
	}
	
	/**
	 * Метод получает список хостов по адресам урл в базе
	 */
	function GetHosts($arFilter=false,$arLimit=false,$bOrder=false)
	{
		global $ks_db;
		if($arLimit>0) $sLimit=" LIMIT $arLimit ";
		$query="SELECT substring(url,1,locate('/',url,8)) as host, url, sum(hits) as hits FROM ".PREFIX.$this->sTable.' GROUP BY host ORDER BY hits DESC'.$sLimit;
		$ks_db->query($query);
		$arResult=array();
		while($arRow=$ks_db->get_row())
		{
			$arResult[]=$arRow;
		}
		return $arResult;
	}
	
	/**
	 * Класс перекрывает родительский для обработки полей из таблицы ks_statistics_strings
	 */
	function GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		$arFilter['?statistics_strings.id']=$this->sTable.'.url';
		$arFilter['statistics_strings.v']='refer';
		$arList=parent::GetList($arOrder,$arFilter,$limit,$arSelect,$arGroupBy);
		foreach($arList as $key=>$arItem)
		{
			$arList[$key]['url']=$arItem['text'];
		}
		return $arList;
	}
}
?>
