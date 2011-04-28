<?php
/**
 * \file hits.php
 * Файл для работы с хитами в статистике
 * Файл проекта kolos-cms.
 * 
 * Создан 24.08.2009
 *
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

require_once MODULES_DIR.'/main/libs/class.CModuleAdmin.php';
require_once MODULES_DIR.'/statistics/libs/class.CStatisticsHits.php';

class CStatisticsHitsAI extends CAdminTable
{
	private $obHits;
	
	function __construct($module='guestbook2',&$smarty,&$parent)
	{
		parent::__construct($module,$smarty,$parent);
		$this->obHits=new CStatisticsHits();
		$this->arColumns=array(
			'date'=>array(
				'title'=>'Дата',
				'default'=>true,
				'sort'=>'date',
				'width'=>'',
			),
			'hits'=>array(
				'title'=>'Хитов',
				'default'=>true,
				'sort'=>'hits',
				'width'=>'',
			),
			'hosts'=>array(
				'title'=>'Хостов',
				'default'=>true,
				'sort'=>'hosts',
				'width'=>'',
			),
			'rhits'=>array(
				'title'=>'Хитов роботов',
				'default'=>true,
				'sort'=>'robot_hits',
				'width'=>'',
			),
			'hitsOnHost'=>array(
				'title'=>'Просмотров на одного поситителя',
				'default'=>false,
				'sort'=>'',
				'width'=>'',
			)
		);
	}
	
	/**
	 * Метод выполняет построение таблицы хитов
	 */
	function Table()
	{
		parent::Table();
		// Обработка порядка вывода элементов
		$arSortFields=Array('date','hits','hosts','robot_hits');
		$arSort=$this->InitSort($arSortFields,$_REQUEST['order'],$_REQUEST['dir']);
		if(class_exists('CFilterFrame'))
		{
			$obFilter=new CFilterFrame();
			$obFilter->AddField(array('FIELD'=>'date','TYPE'=>'DATE','METHOD'=>'<>','DEFAULT'=>array(date('r',time()-86400*30),date('r',time()))));
			$arFilter=$obFilter->GetFilter();
			$obFilter->SetSmartyFilter('filter');
			$arTitles=array(
				'date'=>'Дата');
			$this->smarty->assign('ftitles',$arTitles);
		}
		if($arFilter['>date']=='')
		{
			$arFilter=array(
				'>date'=>time()-86400*30,
				'<date'=>time()+86400,
			);
		} 
		//Подготавливаем постраничный вывод
		$this->obHits->Count($arFilter);
		$obPages = new CPageNavigation($this->obHits);
		$pages = $obPages->GetPages();
		$arResult['ITEMS']=$this->obHits->GetListByDays(Array($arSort[0]=>$arSort[1]),$arFilter,$obPages->GetLimits());
		$arRecords=$this->obHits->GetList(Array($arSort[0]=>$arSort[1]),$arFilter);
		if(is_array($arRecords)&&(count($arRecords)>0))
		{
			$arResult['dateFrom']=floor(($arFilter['>date'])/3600);
			$arResult['dateTo']=floor(($arFilter['<date'])/3600);
			foreach($arRecords as $arItem)
			{
				$arResult['HOURS'][floor($arItem['date']/3600)]=$arItem;
			}
		}
		$this->smarty->assign('data',$arResult);
		$this->smarty->assign('pages',$pages);
		$this->smarty->assign('STRUCTURE',$this->arColumns);
		$this->smarty->assign('order',Array('newdir'=>($arSort[1]=='asc'?'desc':'asc'),'curdir'=>$arSort[1],'field'=>$arSort[0]));
		return '_hits';
	}
	/**
	 * Метод выполняет основные операции по работе с хитами в административной части
	 * выполняет организацию интерфейса и операций с интерфейсом
	 * 
	 */
	function Run($action='')
	{
		$this->ParseAction($action);
		switch($this->sAction)
		{
			case '':
				$page=$this->Table();
			break;
			default:
				$page=parent::Run();
		}
		return $page;
	}
}
$obHits=new CStatisticsHitsAI('statistics',$smarty,$this);
$page=$obHits->Run();
?>
