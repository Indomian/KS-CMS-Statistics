<?php
/**
 * @file function.GetUserAgentData.php
 * В данном файле находится функция обработки адресной строки браузера
 * Файл проекта kolos-cms.
 * 
 * Создан 29.08.2009
 *
 * @author blade39 <blade39@kolosstudio.ru>
 * @version 1.0
 * 
 * 1.0 - Базовая версия проверка взята из вики http://ru.wikipedia.org/wiki/User_Agent
 */
/*Обязательно вставляем во все файлы для защиты от взлома*/ 
if( !defined('KS_ENGINE') ) {die("Hacking attempt!");}

/**
 * Функция выполняет определение параметров пользователя по данным переданным в адресной строке
 */
function GetUserAgentData($useragent)
{
	//echo $useragent;
	if(preg_match('#Mozilla\/([0-9\.]+) (\(([^;\)]+(; |)?)+\))(.*)#i',$useragent,$matches))
	{
		//Нормальный браузер который косит под мозилу
		if($pos=strpos($matches[0],'MSIE'))
		{
			//Косим под ИЕ
			if($pos1=strpos($matches[0],'Netscape'))
			{
				$arResult['BROWSER']='Netscape Navigator';
				preg_match('#Netscape\/| ([0-9\.]+)#i',$matches[0],$arVer);
				$arResult['VERSION']=$arVer[1];
			}
			elseif($pos1=strpos($matches[0],'Opera'))
			{
				$arResult['BROWSER']='Opera';
				preg_match('#Opera ([0-9\.]+)#',$matches[0],$arVer);
				$arResult['VERSION']=$arVer[1];
			}
			else
			{
				$arResult['VERSION']=substr($matches[0],$pos+5,strpos($matches[0],';',$pos)-$pos-5);
				$arResult['BROWSER']='MSIE';
			}
			$arResult['OS']='Other';
			//Определяем операционку
			if(preg_match("#Windows ([a-z\.0-9 ]+)(;|\))#i",$matches[2],$arOs))
			{
				$arResult['OS']='Windows '.$arOs[1];
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS';
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Nitro'))
			{
				$arResult['OS']='Nintendo DS';
			}
			elseif($pos1=strpos($matches[2],'Symbian'))
			{
				$arResult['OS']='Symbian OS';
			}
		}
		elseif($pos=strpos($matches[0],'Camino'))
		{
			$arResult['OS']='Mac OS';
			$arResult['BROWSER']='Camino';
			$arResult['VERSION']=substr($matches[0],$pos+7);
		}
		elseif($pos=strpos($matches[0],'Epiphany'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Epiphany';
			$arResult['VERSION']=substr($matches[0],$pos+9);
		}
		elseif($pos=strpos($matches[0],'Flock'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Flock';
			$arResult['VERSION']=substr($matches[0],$pos+6);
		}
		elseif($pos=strpos($matches[0],'Chrome'))
		{
			$pos1=strpos($matches[2],'Windows ');
			$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			$arResult['BROWSER']='Chrome';
			$arResult['VERSION']=substr($matches[0],$pos+7,strpos($matches[0],' ',$pos+7)-$pos-7);
		}
		elseif($pos=strpos($matches[0],'Iceweasel'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Iceweasel';
			$arResult['VERSION']=substr($matches[0],$pos+10,strpos($matches[0],' ',$pos+10)-$pos-10);
		}
		elseif($pos=strpos($matches[0],'Icecat'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Icecat';
			$arResult['VERSION']=substr($matches[0],$pos+7);
		}
		elseif($pos=strpos($matches[0],'K-Meleon'))
		{
			$pos1=strpos($matches[2],'Windows ');
			$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			$arResult['BROWSER']='K-Meleon';
			$arResult['VERSION']=substr($matches[0],$pos+9);
		}
		elseif($pos=strpos($matches[0],'Minimo'))
		{
			$pos1=strpos($matches[2],'Windows ');
			$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			$arResult['BROWSER']='Minimo';
			$arResult['VERSION']=substr($matches[0],$pos+7);
		}
		elseif($pos=strpos($matches[0],'Firefox'))
		{
			if($pos1=strpos($matches[0],'Opera'))
			{
				$arResult['BROWSER']='Opera';
				$arResult['VERSION']=substr($matches[0],$pos1+6,strpos($matches[0],' ',$pos1+6)-$pos1-6);
			}
			else
			{
				$arResult['BROWSER']='Firefox';
				preg_match('#^([0-9\.]+)#i',substr($matches[0],$pos+8),$arVer);
				$arResult['VERSION']=$arVer[1];
			}
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			
		}
		elseif($pos=strpos($matches[0],'Netscape'))
		{
			$arResult['BROWSER']='Netscape Navigator';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'SunOS'))
			{
				$arResult['OS']='SunOS';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+9);
		}
		elseif($pos=strpos($matches[0],'Safari'))
		{
			$arResult['BROWSER']='Safari';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'iPhone'))
			{
				$arResult['OS']='iPhone OS';
			}
			elseif($pos1=strpos($matches[2],'SymbianOS'))
			{
				$arResult['OS']='Symbian OS';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+7);
		}
		elseif($pos=strpos($matches[0],'SeaMonkey'))
		{
			$arResult['BROWSER']='SeaMonkey';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'Win98'))
			{
				$arResult['OS']='Windows 98';
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+10);
		}
		elseif($pos=strpos($matches[0],'Konqueror'))
		{
			$arResult['BROWSER']='Konqueror';
			if($pos1=strpos($matches[2],'Windows '))
			{
				$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
			}
			elseif($pos1=strpos($matches[2],'Win98'))
			{
				$arResult['OS']='Windows 98';
			}
			elseif($pos1=strpos($matches[2],'Linux'))
			{
				$arResult['OS']='Linux';
			}
			elseif($pos1=strpos($matches[2],'Mac'))
			{
				$arResult['OS']='Mac OS X';
			}
			$arResult['VERSION']=substr($matches[0],$pos+10,3);
		}
		elseif($pos=strpos($matches[0],'Gecko'))
		{
			$arResult['OS']='Linux';
			$arResult['BROWSER']='Mozilla';
			$pos2=strpos($matches[0],' ',$pos);
			if(!$pos2)
			{
				$arResult['VERSION']=substr($matches[0],$pos+6);
			}
			else
			{
				$arResult['VERSION']=substr($matches[0],$pos+6,$pos2-$pos-6);
			}
		}
		else
		{
			if(preg_match('#Twiceler-([0-9\.]+)#',$matches[0],$arVer))
			{
				$arResult['BROWSER']='Twiceler';
				$arResult['VERSION']=$arVer[1];
				$arResult['OS']='ROBOT';
			}
			elseif(preg_match('#Yahoo! Slurp\/([0-9\.]+)#',$matches[0],$arVer))
			{
				$arResult['BROWSER']='Yahoo! Slurp';
				$arResult['VERSION']=$arVer[1];
				$arResult['OS']='ROBOT';
			}
			elseif(preg_match('#Googlebot\/([0-9\.]+)#',$matches[0],$arVer))
			{
				$arResult['BROWSER']='Googlebot';
				$arResult['VERSION']=$arVer[1];
				$arResult['OS']='ROBOT';
			}
			else
			{
				$arResult['BROWSER']='Mozilla compatable';
				$arResult['VERSION']=$matches[1];
				if($pos1=strpos($matches[2],'Windows '))
				{
					$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
				}
				elseif($pos1=strpos($matches[2],'Win98'))
				{
					$arResult['OS']='Windows 98';
				}
				elseif($pos1=strpos($matches[2],'Linux'))
				{
					$arResult['OS']='Linux';
				}
				elseif($pos1=strpos($matches[2],'Mac'))
				{
					$arResult['OS']='Mac OS X';
				}
			}
		}
	}
	elseif(preg_match('#Opera\/([0-9\.]+) (\(([^;\)]+(; |)?)+\))(.*)#i',$useragent,$matches))
	{
		//Какаято из опер
		$arResult['BROWSER']='Opera';
		$arResult['VERSION']=$matches[1];
		if($pos1=strpos($matches[2],'Opera Mini'))
		{
			$arResult['BROWSER']='Opera Mini';
			preg_match('#Opera mini\/([a-z0-9\.]+)\/#',$matches[2],$arVer);
			$arResult['VERSION']=$arVer[1];
			$arResult['OS']='J2ME/MIDP';
		}
		elseif($pos1=strpos($matches[2],'Windows '))
		{
			$arResult['OS']='Windows '.substr($matches[2],$pos1+8,strpos($matches[2],';',$pos1+8)-$pos1-8);
		}
		elseif($pos1=strpos($matches[2],'Win98'))
		{
			$arResult['OS']='Windows 98';
		}
		elseif($pos1=strpos($matches[2],'Linux'))
		{
			$arResult['OS']='Linux';
		}
		elseif($pos1=strpos($matches[2],'Mac'))
		{
			$arResult['OS']='Mac OS X';
		}
	}
	elseif(preg_match('#^Yandex(something)?\/([0-9\.]+)#i',$useragent,$matches))
	{
		//яндекс робот
		$arResult['BROWSER']='Yandex';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^Mail\.Ru\/([0-9\.]+)#i',$useragent,$matches))
	{
		//мэйл ру робот
		$arResult['BROWSER']='Mail.ru';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^StackRambler\/([0-9\.]+)#i',$useragent,$matches))
	{
		//рамблер робот
		$arResult['BROWSER']='Rambler';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^msnbot\/([0-9\.]+)#i',$useragent,$matches))
	{
		//мелкософт
		$arResult['BROWSER']='MSN bot';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^FlickySearchBot\/([0-9\.]+)#i',$useragent,$matches))
	{
		//не знаю
		$arResult['BROWSER']='FlickySearchBot';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^Yanga WorldSearch Bot v([0-9\.]+)#i',$useragent,$matches))
	{
		//Какаято из опер
		$arResult['BROWSER']='Yanga WorldSearch Bot';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	elseif(preg_match('#^Yeti\/([0-9\.]+)#i',$useragent,$matches))
	{
		//Какаято из опер
		$arResult['BROWSER']='Yeti';
		$arResult['VERSION']=$matches[2];
		$arResult['OS']='ROBOT';
	}
	else
	{
		$arResult['BROWSER']='Other';
		$arResult['VERSION']='1.0';
		//Определяем операционку
		if($pos1=strpos($matches[0],'Windows'))
		{
			$pos2=strpos($matches[0],';',$pos1);
			if(!$pos2)
			{
				$pos2=strpos($matches[0],')',$pos1);
			}
			$arResult['OS']='Windows '.substr($matches[0],$pos1+8,$pos2-$pos1-8);
		}
		elseif($pos1=strpos($matches[0],'Mac'))
		{
			$arResult['OS']='Mac OS';
		}
		elseif($pos1=strpos($matches[0],'Linux'))
		{
			$arResult['OS']='Linux';
		}
		elseif($pos1=strpos($matches[0],'Nitro'))
		{
			$arResult['OS']='Nintendo DS';
		}
		elseif($pos1=strpos($matches[0],'Symbian'))
		{
			$arResult['OS']='Symbian OS';
		}
	}
	return $arResult;
}

/*function GetUserAgentData($useragent)
{
	$arSpaces=explode(' ',$useragent);
	if(strpos($arSpaces[0],'Mozilla')===0)
	{
		//У нас какой либо из браузеров Mozilla, либо кто-то под него подделывается
		//надо определить кто, сперва смотрим версию
		echo $useragent.'<br/>';
		preg_match('#Mozilla\/([0-9\.]+) (\(([^;\)]+(; |)?)+\))(.*)#i',$useragent,$matches);
		pre_print($matches);
		$arMozilla=explode('/',$arSpaces[0]);
		if($arMozilla[1]=='1.1')
		{
			//Очень старая версия движка, один единственный браузер - MSPIE (IE Mobile)
			$arResult['OS']='Windows CE';
			$arResult['BROWSER']='IE Mobile';
			$arResult['VERSION']='2.0';
		}
		elseif($arMozilla[1]=='1.22')
		{
			//Старые браузеры мелкософт, 2 штуки + разные операционки
			$arResult['BROWSER']='MSIE';
			$arResult['OS']='Windows';
			preg_match_all('#([^;\)\(]+)( |\)|;) ?#i',$matches[2],$params);
			$params=$params[1];	
			if(in_array('MSIE 1.5',$params))
			{
				$arResult['VERSION']='1.5';
			}
			elseif(in_array('MSIE 2.0',$params))
			{
				$arResult['VERSION']='2.0';				
			}
			if(in_array('Windows 95',$params))
			{
				$arResult['OS']='Windows 95';
			}
			elseif(in_array('Windows NT',$params))
			{
				$arResult['OS']='Windows NT';
			}
		}
		elseif($arMozilla[1]=='2.0')
		{
			//Один IE и один робот
			preg_match_all('#([^;\)\(]+)( |\)|;) ?#i',$matches[2],$params);
			$params=$params[1];	
			if(in_array('Ask Jeeves/Teoma',$params))
			{
				$arResult['OS']='ROBOT';
				$arResult['BROWSER']='Ask.com/Teoma';
				$arResult['VERSION']='0';
			}
			else
			{
				$arResult['OS']='Windows 98';
				$arResult['BROWSER']='MSIE';
				$arResult['VERSION']='3.01';
			}
		}
		elseif($arMozilla[1]=='3.0')
		{
			//Браузер под BeOS несколько старых нетскейпов, библиотека для делфи и робот
			preg_match_all('#([^;\)\(]+)( |\)|;) ?#i',$matches[2],$params);
			$params=$params[1];
			if(in_array('NetPositive/2.2',$params))
			{
				//BeOS
				$arResult['OS']='BeOS R5';
				$arResult['BROWSER']='NetPositive';
				$arResult['VERSION']='2.2';
			}
			elseif(in_array('Indy Library',$params))
			{
				$arResult['OS']='Windows';
				$arResult['BROWSER']='Indy Library';
				$arResult['VERSION']='0';
			}
			elseif(in_array('Slurp/si',$params))
			{
				$arResult['OS']='ROBOT';
				$arResult['BROWSER']='Inktomi Slurp';
				$arResult['VERSION']='0';
			}
			else
			{
				//Один из netscape
				$arResult['BROWSER']='Netscape Navigator';
				if(in_array('OS/2',$params))
				{
					$arResult['OS']='OS/2';
					$arResult['VERSION']='2.0';
				}
				else
				{
					for($i=0;$i<count($params);$i++)
					{
						if(strpos($params[$i],'SunOS'))
						{
							$arResult['OS']='SunOS '.substr($params[$i],strpos($params[$i],'SunOS')+6,3);
							$arResult['VERSION']='3.0';
						}
					}
				}
			}
		}
		elseif($arMozilla[1]=='4.0')
		{
			//Очень много браузеров
			preg_match_all('#([^;\)\(]+)( |\)|;) ?#i',$matches[2],$params);
			$params=$params[1];
			if(in_array('MSIE 6.0',$params))
			{
				//Это либо осел, либо AOL браузер
				if(in_array('America Online Browser 1.1',$params))
				{
					$arResult['BROWSER']='America Online Browser';
					for($i=0;$i<count($params);$i++)
					{
						if(substr($params[$i],0,3)=='rev')
						{
							$arResult['VERSION']=substr($params[$i],3);
						}
					}
				}
				elseif(in_array('Avant Browser [avantbrowser.com]',$params))
				{
					$arResult['BROWSER']='Avant Browser';
					$arResult['VERSION']='NEW';
				}
				elseif(in_array('MSN 2.5',$params))
				{
					$arResult['BROWSER']='MSIE';
					$arResult['VERSION']='6.0';
				}
				elseif(strpos($matches[5],'Netscape')>0)
				{
					//Новый нетскейп
					$arResult['BROWSER']='Netscape Navigator';
					$arResult['VERSION']='8.0.1';
				}
				elseif(strpos($matches[5],'Opera')>0)
				{
					$arResult['BROWSER']='Opera';
					if(in_array('Nitro',$params))
					{
						$arResult['OS']='Nintendo DS';
					}
					elseif(in_array('Symbian OS'))
					{
						$arResult['BROWSER']='Opera Mobile';
						$arResult['OS']='Symbian OS';
					}
					$arResult['VERSION']=substr($matches[5],strpos($matches[5],'Opera')+6,4);
				}
				elseif(in_array('Windows NT 5.2',$params))
				{
					$arResult['BROWSER']='Safari';
					$arResult['VERSION']='125';
					$arResult['OS']='Mac OS X';
				}
				elseif(in_array('grub-client-1.4.3',$params))
				{
					$arResult['OS']='ROBOT';
					$arResult['BROWSER']='Grub';
					$arResult['VERSION']='1.4.3';
				}
				else
				{
					//Если сюда попали значит всетаки IE
					$arResult['BROWSER']='MSIE';
					$arResult['VERSION']='6.0';
					if(in_array('Windows CE',$params))
					{
						$arResult['OS']='Windows CE';
					}
					else
					{
						$arResult['OS']='Windows XP';
					}
				}
			}
			elseif(in_array('MSIE 5.0',$params))
			{
				
			}
		}
		
		pre_print($params);
	}
}*/
?>
