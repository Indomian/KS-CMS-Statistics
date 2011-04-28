<?php
/**
 * \file class.CStatistics.php
 * Файл содержит класс для работы со статистикой. Не наследует от класса CObject
 * для повышения производительности вся работа с базой данных выполняется напрямую
 * Файл проекта kolos-cms.
 * 
 * Создан 13.07.2009
 * 
 * \author blade39 <blade39@kolosstudio.ru>
 * \version 1.0
 * \todo
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

include_once MODULES_DIR.'/main/libs/class.CMain.php';
include_once MODULES_DIR.'/statistics/libs/function.GetUserAgentData.php';

class CStatistics extends CBaseObject
{
	static $arConfig;
	
	/**
	 * Метод получает значение настройки модуля статистики
	 */
	function GetConfigVar($var)
	{
		if(!is_array(self::$arConfig))
		{
			include MODULES_DIR.'/statistics/config.php';
			self::$arConfig=$MODULE_statistics_config;
		}
		return self::$arConfig[$var];
	}
	
	function GetRecord($sess_id)
	{
		global $ks_db;
		if(preg_match('#^[a-f0-9]+$#',$sess_id))
		{
			$query="SELECT * FROM ".PREFIX."statistics_sessions WHERE sess_id='$sess_id'";
			$rRes=$ks_db->query($query);
			if($arRow=$ks_db->get_row($rRes))
			{
				return $arRow;
			}
			return false;
		}
		throw new CError('STATISTICS_SESSION_CODE_ERROR',0,__LINE__.__FILE__);
	}
	
	function GetRecordByUserID($user_id)
	{
		global $ks_db;
		$query="SELECT * FROM ".PREFIX."statistics_sessions WHERE user_id='$user_id'";
		$rRes=$ks_db->query($query);
		if($arRow=$ks_db->get_row($rRes))
		{
			return $arRow;
		}
		return false;
	}
	
	function AddGuest($sess_id,$data)
	{
		global $ks_db;
		if(preg_match('#^[a-f0-9]+$#',$sess_id))
		{
			if(self::GetConfigVar('sidLifeTime')>0 && self::GetConfigVar('sidCount')>0)
			{
				$query="INSERT INTO ".PREFIX."statistics_sessions(sess_id,first_in,last_active,hits,user_id,user_ip) VALUES" .
						'('.
						"'$sess_id'," .
						"'".time()."',".
						"'".time()."',1,".
						"'".$data['user_id']."',".
						"'".$data['user_ip']."')";
				$rRes=$ks_db->query($query);
				$arResult=array(
					'id'=>$ks_db->insert_id(),
					'sess_id'=>$sess_id,
					'first_in'=>time(),
					'last_active'=>time(),
					'hits'=>1,
					'user_id'=>$data['user_id'],
					'user_ip'=>$data['user_ip'],
				);
				return $arResult;
			}
			return false;
		}
		throw new CError('STATISTICS_SESSION_CODE_ERROR',0,__LINE__.__FILE__);
	}
	
	/**
	 * Метод выполняет очистку таблицы сессий по настройкам заданным в 
	 * конфигурационном файле.
	 */
	function ClearSessions()
	{
		global $ks_db;
		//Удаляем по датам
		$query="DELETE FROM ".PREFIX."statistics_sessions WHERE last_active<=".(time()-self::GetConfigVar('sidLifeTime'));
		$ks_db->query($query);
		//Смотрим сколько осталось
		$query="SELECT count(*) as cnt FROM ".PREFIX."statistics_sessions";
		$ks_db->query($query);
		$arRow=$ks_db->get_row();
		$count=$arRow['cnt'];
		//Больше чем ограничение
		if($count>self::GetConfigVar('sidCount'))
		{
			$query="DELETE FROM ".PREFIX."statistics_sessions ORDER BY last_active DESC LIMIT ".($count-self::GetConfigVar('sidCount')+1);
			$ks_db->query($query);
		}
	}
	
	/**
	 * Метод выполняет очистку таблицы переходов по настройкам заданным в 
	 * конфигурационном файле.
	 */
	function ClearMoves()
	{
		global $ks_db;
		//Удаляем по датам
		$query="DELETE FROM ".PREFIX."statistics_moves WHERE date<=".(time()-self::GetConfigVar('movesLifeTime'));
		$ks_db->query($query);
		//Смотрим сколько осталось
		$query="SELECT count(*) as cnt FROM ".PREFIX."statistics_moves";
		$ks_db->query($query);
		$arRow=$ks_db->get_row();
		$count=$arRow['cnt'];
		//Больше чем ограничение
		if($count>self::GetConfigVar('movesCount'))
		{
			$query="DELETE FROM ".PREFIX."statistics_moves ORDER BY date DESC LIMIT ".($count-self::GetConfigVar('movesCount')+1);
			$ks_db->query($query);
		}
	}
	
	function Update($sess_id,$data)
	{
		global $ks_db;
		if(preg_match('#^[a-f0-9]+$#',$sess_id))
		{
			$arUpdate=array();
			foreach($data as $key=>$value)
			{
				$arUpdate[]=$key."='$value'";
			}
			if(count($arUpdate)>0)
			{
				$query="UPDATE ".PREFIX."statistics_sessions SET ".join(',',$arUpdate)." WHERE sess_id='$sess_id'";
				$rRes=$ks_db->query($query);
				return true;
			}
			return false;
		}
		throw new CError('STATISTICS_SESSION_CODE_ERROR',0,__LINE__.__FILE__);
	}
	
	/**
	 * Метод выполняет удаление записи в статистике по коду строки
	 */
	function DeleteById($id)
	{
		global $ks_db;
		$query="DELETE FROM ".PREFIX."statistics_sessions WHERE id='$id'";
		$ks_db->query($query);
	}
	/**
	 * Метод выполняет обновление записи в статистике по коду строки
	 */
	function UpdateById($id,$data)
	{
		global $ks_db;
		$arUpdate=array();
		foreach($data as $key=>$value)
		{
			$arUpdate[]=$key."='$value'";
		}
		if(count($arUpdate)>0)
		{
			$query="UPDATE ".PREFIX."statistics_sessions SET ".join(',',$arUpdate)." WHERE id='$id'";
			$rRes=$ks_db->query($query);
			return true;
		}
		return false;
	}
	
	/**
	 * Метод конвертирует ip адрес в целое число
	 */
	function IPtoInt($ip)
	{
		if(is_string($ip))
		{
			$iResult=0;
			$arIp=explode('.',$ip);
			if(count($arIp)==4)
			{
				$arIp[0]=$arIp[0]*16777216;
				$arIp[1]=$arIp[1]*65536;
				$arIp[2]=$arIp[2]*256;
				return array_sum($arIp);
			}
			return 0;
		}
		return 0;
	}
		
	function IntToIP($int)
	{
		$hex=dechex($int);
		if(strlen($hex)<8)
		{
			$hex=str_repeat('0',8-strlen($hex)).$hex;
		}
		preg_match('#([a-f0-9]{2,2})([a-f0-9]{2,2})([a-f0-9]{2,2})([a-f0-9]{2,2})#i',$hex,$matches);
		$ip=hexdec($matches[1]).'.'.hexdec($matches[2]).'.'.hexdec($matches[3]).'.'.hexdec($matches[4]);
		return $ip;
	}
	
	//Обработчик хита пользователя (не гостя) на сайте
	function onUserSessionUpdate(&$data,$params=false)
	{
		if(self::GetConfigVar('active')==0) return;
	}
	
	//Обработчик хита на сайте (кого угодно)
	/**
	 * Данный метод выполняет запись статистической информации, 
	 * при переходе пользователя на страницу сайта.
	 * \todo подумать как снизить количество запросов.
	 */
	function onUserObjectInit(&$data,$params=false)
	{
		global $ks_db,$KS_MODULES;
		if(self::GetConfigVar('active')==0) return;
		if(IS_ADMIN)
		{
			if(self::GetConfigVar('save_admin')==0) return;
		}
		//Подготовка времени хита
		$hour=idate('H');
		$corr=idate('Z');
		$day=round((time()-$corr)/86400)*86400;
		$time=floor((time()-$corr)/3600)*3600;
		//Обработка браузера пользователя
		if($_SESSION['statistics_agent']=='')
		{
			//Определяем клиента
			$userAgent=$_SERVER['HTTP_USER_AGENT'];
			$arUserAgent=GetUserAgentData($userAgent);
			$browser=$arUserAgent['BROWSER'];
			$version=$arUserAgent['VERSION'];
			$os=$arUserAgent['OS'];
			//Отбрасываем неизвестные браузеры из обработки статистики
			if($browser=='Other') return;
			//Ищем номер браузера и оси в таблице текстовых значений
			$query="SELECT id, text, v FROM ks_statistics_strings " .
					"WHERE " .
						"(" .
							"text='".$ks_db->safesql($browser)."' AND " .
							"v='client'" .
						") OR (" .
							"text='".$ks_db->safesql($os)."' AND " .
							"v='os'" .
						")";
			$res=$ks_db->query($query);
			$arData=array();
			while($arRow=$ks_db->get_row($res))
			{
				$arData[$arRow['v']]=$arRow['id'];
			}
			if($arData['os']==0)
			{
				//Не нашли запись о такой операционке
				$query="INSERT INTO ks_statistics_strings(text,v) VALUES ('".$ks_db->safesql($os)."','os')";
				$res=$ks_db->query($query);
				$arData['os']=$ks_db->insert_id();
			}
			if($arData['client']==0)
			{
				//Не нашли запись о таком браузере
				$query="INSERT INTO ks_statistics_strings(text,v) VALUES ('".$ks_db->safesql($browser)."','client')";
				$res=$ks_db->query($query);
				$arData['client']=$ks_db->insert_id();
			}
			$query="SELECT * FROM ks_statistics_agents WHERE " .
					"browser='".$arData['client']."' AND " .
					"version='".$ks_db->safesql($version)."' AND " .
					"os='".$arData['os']."' AND " .
					"date='".$day."' LIMIT 1";
			$ks_db->query($query);
			if($arRow=$ks_db->get_row())
			{
				$query="UPDATE ks_statistics_agents SET count=count+1 WHERE id='".$arRow['id']."'";
				$ks_db->query($query);
			}
			else
			{
				$query="INSERT INTO ks_statistics_agents(browser,version,os,date,count) VALUES ('".$arData['client']."','$version','".$arData['os']."',$day,1)";
				$ks_db->query($query);	
			}
			$_SESSION['statistics_agent']=$userAgent;
		}
		else
		{
			$arUserAgent=GetUserAgentData($_SESSION['statistics_agent']);
		}
		//Записываем пользователя в таблицу сессий если он не робот
		if($arUserAgent['OS']!='ROBOT')
		{
			$sid=session_id();
			$arUser=self::GetRecord($sid);
			if(!is_array($arUser))
			{
				$arUser=array(
					'user_id'=>$data['id'],
					'user_ip'=>self::IPtoInt($_SERVER['REMOTE_ADDR']),
				);
				$arUser=self::AddGuest($sid,$arUser);
			}
			else
			{
				$data=array(
					'hits'=>$arUser['hits']+1,
					'last_active'=>time(),
					'user_id'=>$data['id'],
				);
				self::Update($sid,$data);
			}
			//Очистка таблицы сессий
			self::ClearSessions();
			//Записываем хит в счетчик
			$query="INSERT INTO ks_statistics_hits(date,day,hour,hits) VALUES ($time,$day,$hour,1) ON DUPLICATE KEY UPDATE hits=hits+1";
			if($_SESSION['statistics_guest']!=$day)
			{
				$_SESSION['statistics_guest']=$day;
				$query="INSERT INTO ks_statistics_hits(date,day,hour,hits,hosts) VALUES ($time,$day,$hour,1,1) ON DUPLICATE KEY UPDATE hits=hits+1, hosts=hosts+1";
			}
			$ks_db->query($query);
		}
		else
		{
			if(self::GetConfigVar('countRobots')==1)
			{
				$query="INSERT INTO ks_statistics_hits(date,day,hour,hits,robot_hits) VALUES ($time,$day,$hour,0,1) ON DUPLICATE KEY UPDATE robot_hits=robot_hits+1";
				if($_SESSION['statistics_guest']!=$day)
				{
					$_SESSION['statistics_guest']=$day;
					$query="INSERT INTO ks_statistics_hits(date,day,hour,hits,robot_hits,hosts) VALUES ($time,$day,$hour,0,1,0) ON DUPLICATE KEY UPDATE robot_hits=robot_hits+1";
				}
				$ks_db->query($query);
			}
			return;
		}
		
		if(IS_ADMIN)
		{
			if(self::GetConfigVar('save_admin_path')==0) return;
		}
		//Обработка добавления рефера в базу
		$refer=$_SERVER['HTTP_REFERER'];
		$to=$_SERVER['REQUEST_URI'];
		if(($refer!='')&&($to!='')&&($refer!=$to))
		{
			//Блокировка на случай обновления страницы
			if($_SESSION['statistics_lastway']!=array($refer,$to))
			{
				$_SESSION['statistics_lastway']=array($refer,$to);
				//Ищем числовые значения путей
				$query="SELECT id, text, v FROM ks_statistics_strings " .
						"WHERE " .
							"(" .
								"text='".$ks_db->safesql($refer)."' AND " .
								"v='refer'" .
							") OR (" .
								"text='".$ks_db->safesql($to)."' AND " .
								"v='path'" .
							")";
				$res=$ks_db->query($query);
				$arData=array();
				while($arRow=$ks_db->get_row($res))
				{
					$arData[$arRow['v']]=$arRow['id'];
				}
				if($arData['path']==0)
				{
					//Не нашли запись о таком пути
					$query="INSERT INTO ks_statistics_strings(text,v) VALUES ('".$ks_db->safesql($to)."','path')";
					$res=$ks_db->query($query);
					$arData['path']=$ks_db->insert_id();
				}
				if($arData['refer']==0)
				{
					//Не нашли запись о таком рефере
					$query="INSERT INTO ks_statistics_strings(text,v) VALUES ('".$ks_db->safesql($refer)."','refer')";
					$res=$ks_db->query($query);
					$arData['refer']=$ks_db->insert_id();
				}				
				$query="SELECT * FROM ks_statistics_refers WHERE url='".$arData['refer']."' AND date='".$day."'";
				$rRes=$ks_db->query($query);
				$arRow=$ks_db->get_row($rRes);
				if($arRow['id']==0)
				{
					$query="INSERT INTO ks_statistics_refers(url,hits,date) VALUES ('".$arData['refer']."',1,'$day')";
					$ks_db->query($query);
					$iMove_from=$ks_db->insert_id();
				}
				else
				{
					$query="UPDATE ks_statistics_refers SET hits=hits+1 WHERE id=".$arRow['id'];
					$ks_db->query($query);
					$iMove_from=$arRow['id'];
				}
				$query="SELECT * FROM ks_statistics_acceptors WHERE url='".$arData['path']."' AND date='".$day."'";
				$rRes=$ks_db->query($query);
				$arRow=$ks_db->get_row($rRes);
				if($arRow['id']==0)
				{
					$query="INSERT INTO ks_statistics_acceptors(url,hits,date) VALUES ('".$arData['path']."',1,'$day')";
					$ks_db->query($query);
					$iMove_to=$ks_db->insert_id();
				}
				else
				{
					$query="UPDATE ks_statistics_acceptors SET hits=hits+1 WHERE id=".$arRow['id'];
					$ks_db->query($query);
					$iMove_to=$arRow['id'];
				}
				$query="INSERT INTO ks_statistics_moves(date,move_from,move_to,sid) VALUES (".time().",$iMove_from,$iMove_to,'$sid')";
				$ks_db->query($query);
				self::ClearMoves();
			}
		}
	}
	
	/**
	 * Данный метод вызывается когда происходит логин пользователя на сайт
	 */
	function onLogin(&$data,$params=false)
	{
		if(self::GetConfigVar('active')==0) return;
		$arUser=self::GetRecordByUserID($data['id']);
		$sid=session_id();
		$arGuest=self::GetRecord($sid);
		if(is_array($arGuest)&&is_array($arUser))
		{
			$data=array(
				'hits'=>$arUser['hits']+$arGuest['hits'],
				'last_active'=>time(),
				'user_id'=>$data['id'],
				'sess_id'=>$sid,
				'user_ip'=>self::IPtoInt($_SERVER['REMOTE_ADDR'])
			);
			self::DeleteById($arGuest['id']);
			self::UpdateById($arUser['id'],$data);
		}
		elseif(is_array($arGuest)&&!is_array($arUser))
		{
			$data=array(
				'hits'=>$arGuest['hits']+1,
				'last_active'=>time(),
				'user_id'=>$data['id'],
				'user_ip'=>self::IPtoInt($_SERVER['REMOTE_ADDR']),
			);
			self::Update($sid,$data);
		}
		elseif(!is_array($arGuest)&&is_array($arUser))
		{
			$data=array(
				'hits'=>$arUser['hits']+1,
				'last_active'=>time(),
				'user_id'=>$data['id'],
				'sess_id'=>$sid,
				'user_ip'=>self::IPtoInt($_SERVER['REMOTE_ADDR'])
			);
			self::UpdateById($arUser['id'],$data);
		}
		else
		{
			$arUser=array(
				'user_id'=>$data['id'],
				'user_ip'=>self::IPtoInt($_SERVER['REMOTE_ADDR']),
			);
			self::AddGuest($sid,$arUser);
		}
		self::ClearSessions();
	}
	
	function onBeforeLogout(&$data,$params=false)
	{
		if(self::GetConfigVar('active')==0) return;
	}
}
?>
