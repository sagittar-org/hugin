<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<div id="chart_div"></div>
<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawLineColors);

function drawLineColors() {
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'X');
      data.addColumn('number', '<?php echo $key; ?>');

      data.addRows([
<?php foreach (array_reverse($rows) as $row): ?>
<?php echo "['{$row['datetime']}',{$row['value']}], \n"; ?>
<?php endforeach; ?>
      ]);

      var options = {
        vAxis: {
          title: '<?php echo $key; ?>'
        },
      };

      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      chart.draw(data, options);
}
</script>
