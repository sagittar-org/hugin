#!/usr/bin/php
<?php
require './config.php';
$mysqli = new mysqli($host, $username, $passwd, $dbname);
foreach ($cmd_list as $key => $cmd)
{
	$value = shell_exec("{$cmd} 2>&1");
	$mysqli->query("CREATE TABLE IF NOT EXISTS `t_{$key}` (`created` datetime DEFAULT CURRENT_TIMESTAMP, `value` varchar(255))");
	$mysqli->query("INSERT INTO `t_{$key}` (`value`) VALUES ({$value})");
}
$mysqli->close();
