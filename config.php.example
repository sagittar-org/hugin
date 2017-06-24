<?php
$host     = 'localhost';
$username = 'root';
$passwd   = '';
$dbname   = 'hugin';
$cmd_list = [
	'cpu' => "vmstat | tail -n1 | awk '{printf 100 - \$15}'",
	'memory' => "free | grep 'Mem' | awk '{printf int((1 - \$7 / \$2) * 100 + 0.5)}'",
	'disk' => "df | grep /dev/sda1 | awk '{printf \$5}' | sed 's/%//'",
];
