<?php
require './config.php';
$mysqli = new mysqli($host, $username, $passwd, $dbname);
if (isset($_POST['key']))
{
	$key   = $mysqli->real_escape_string($_POST['key']);
	$from  = $mysqli->real_escape_string($_POST['from']);
	$to    = $mysqli->real_escape_string($_POST['to']);
	$unit  = $mysqli->real_escape_string($_POST['unit']);
}
else
{
	$key   = array_keys($cmd_list)[0];
	$from  = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - 3600);
	$to    = date('Y-m-d H:i:s');
	$unit  = '60';
}
$from  = date('Y-m-d H:i:s', floor(strtotime($from) / $unit) * $unit);
$to  = date('Y-m-d H:i:s', floor(strtotime($to) / $unit) * $unit);

$mysqli->query('CREATE TEMPORARY TABLE `t_datetime` (`datetime` datetime)');
for ($count = 0, $timestamp = strtotime($from); $count < 100 && $timestamp <= strtotime($to); $count++, $timestamp += $unit)
{
	$mysqli->query('INSERT INTO `t_datetime` (`datetime`) VALUES ("'.date('Y-m-d H:i:s', $timestamp).'")');
}
$rows = $mysqli->query("
SELECT `datetime`, `value` FROM `t_datetime` 
LEFT JOIN (SELECT FROM_UNIXTIME(TRUNCATE(UNIX_TIMESTAMP(`created`) / ($unit), 0) * $unit) AS `created`, ROUND(AVG(`value`), 0) AS `value` FROM `t_{$key}` GROUP BY FROM_UNIXTIME(TRUNCATE(UNIX_TIMESTAMP(`created`) / ($unit), 0) * $unit)) AS `t_{$key}` ON `created` = `datetime` 
ORDER BY `datetime` DESC")->fetch_all(MYSQLI_ASSOC);
$mysqli->close();
?>
<title>Hugin - logging and aggregation</title>
<img src="hugin.svg" style="height:100px;">
<form method="post">
<table>
<tr><th>key</th><td><select name="key" onchange="this.form.submit()">
<?php foreach ($cmd_list as $k => $cmd): ?>
<option value="<?php echo $k; ?>"<?php if ($k === $key): ?> selected<?php endif; ?>><?php echo $k; ?></option>
<?php endforeach; ?>
</select></td></tr>
<tr><th>from</th><td><input name="from" value="<?php echo $from; ?>"></td></tr>
<tr><th>to</th><td><input name="to" value="<?php echo $to; ?>"></td></tr>
<tr><th>unit</th><td><select name="unit" onchange="this.form.submit()">
<?php foreach (['60' => '1m', '300' => '5m', '600' => '10m', '1800' => '30m', '3600' => '1h', '10800' => '3h', '21600' => '6h', '43200' => '12h', '86400' => '1d'] as $u => $l): ?>
<option value="<?php echo $u; ?>"<?php if ($u == $unit): ?> selected<?php endif; ?>><?php echo $l; ?></option>
<?php endforeach; ?>
</select></td></tr>
</table>
<button type="submit">aggregate</button>
</form>

<style>
table {
	border-collapse: collapse;
}
th, td {
	min-width: 100px;
	border:1px solid #c0c0c0;
	text-align: center;
}
</style>

<?php require './chart.php'; ?>

<table>
<tr><th>datetime</th><th><?php echo $key; ?></th></tr>
<?php foreach ($rows as $row): ?>
<tr><td><?php echo $row['datetime']; ?></td><td><?php echo $row['value']; ?></td></tr>
<?php endforeach; ?>
</table>
