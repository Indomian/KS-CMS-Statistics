<?php

/**
 * Конфигурационный файл модуля "statistics"
 * Последнее изменение: 18.03.2010, 18:15:38
 */

$MODULE_statistics_config = array
(
	'active' => "1",
	'save_admin_path' => "0",
	'save_admin' => "0",
	'path_save_length' => "3",
	'countRobots' => "1",
	'sidLifeTime' => "172800",
	'sidCount' => "10000",
	'movesLifeTime' => "172800",
	'movesCount' => "10000",
	'osNames' => array
	(
		'Windows NT 5.0' => "Windows 2000",
		'Windows NT 5.1' => "Windows XP",
		'Windows NT 5.2' => "Windows Server 2003",
		'Windows NT 6.0' => "Windows Vista",
		'Windows NT 6.1' => "Windows 7"
	),
	'table' => array
	(
		'date' => "1",
		'hits' => "1",
		'hosts' => "1",
		'rhits' => "0",
		'hitsOnHost' => "0"
	)
);

?>