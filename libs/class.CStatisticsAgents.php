<?php
/**
 * \file class.CStatisticsAgents.php
 * Файл контейнер для класса CStatisticsAgents
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

require_once MODULES_DIR.'/statistics/libs/class.CStatisticsStrings.php';

class CStatisticsAgents extends CObject
{
	private $obStrings;
	/**
	 * Конструктор класса
	 */
	function __construct($sTable='statistics_agents')
	{
		parent::__construct($sTable);
		$this->arFields=array('id','browser','version','os','count','date');
		$this->obStrings=new CStatisticsStrings();
	}
	
	/**
	 * Класс перекрывает родительский для обработки полей из таблицы ks_statistics_strings
	 */
	function GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false)
	{
		$arList=parent::GetList($arOrder=false,$arFilter=false,$limit=false,$arSelect=false,$arGroupBy=false);
		if(is_array($arList)&& count($arList)>0)
		{
			$arBrowsers=array();
			$arOs=array();
			foreach($arList as $key=>$arItem)
			{
				$arBrowsers[$arItem['browser']]++;
				$arOs[$arItem['os']]++;	
			}
			$arBList=$this->obStrings->GetList(array('id'=>'asc'),array('->id'=>array_keys($arBrowsers),'v'=>'client'));
			foreach($arBList as $arRow)
				$arBrowsers[$arRow['id']]=$arRow['text'];
			$arBList=$this->obStrings->GetList(array('id'=>'asc'),array('->id'=>array_keys($arOs),'v'=>'os'));
			foreach($arBList as $arRow)
				$arOs[$arRow['id']]=$arRow['text'];
			foreach($arList as $key=>$arItem)
			{
				$arList[$key]['browser']=$arBrowsers[$arItem['browser']];
				$arList[$key]['os']=$arOs[$arItem['os']];
			}
		}
		return $arList;
	}
}
?>
