<html>
<head>
<!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
</head>
<body>

<div id="chart_div"></div>

<?php
$arquivoEspelhos = file_get_contents("espelhos.json");
$espelhos = json_decode($arquivoEspelhos, true);
$status = array();
$grupos = array();
$filtro = (isset($_GET['status']) ? $_GET['status'] : false);

$anoFormacao = array();
echo "<table>";?>
<tr>
	<th>Nome do grupo</th>
	<th>Líder</th>
	<th>Status</th>
	<th>Ano formação</th>
</tr>
<?php
foreach($espelhos as $esp) {
    $grupo = json_decode(file_get_contents("cache/" . $esp . ".json"));
	isset($status[$grupo->status]) ? $status[$grupo->status]++ : $status[$grupo->status]= 1 ;
	isset($anoFormacao[$grupo->anoformacao]) ? $anoFormacao[$grupo->anoformacao]++ : $anoFormacao[$grupo->anoformacao]= 1 ;
	$grupos[$grupo->status][] = $grupo->titulo;
		echo "<tr>
			<td>" . $grupo->titulo . "</td>
			<td>" . $grupo->lideres[0] . "</td>
			<td>" . $grupo->status .  "</td>
			<td>" . $grupo->anoformacao .  "</td>
		</tr>";
}
echo "</table>";
ksort($anoFormacao);
print_r($status);
print_r($anoFormacao);
$grafico = "['Ano Formação', 'Total'],";
foreach($anoFormacao as $k => $v) 
	$grafico .= "['" . $k . "', " . $v . "],";

echo "Aqui: " . $grafico;
?>

<script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([<?=$grafico;?>]);

        var options = {
			width: 1200,
      height: 400,
      animation:{
        duration: 1000,
        easing: 'out',
      },
          legend: { position: 'none' },
          chart: {
            title: 'Grupos de Pesquisa na Unilab'
          }
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
</body>
</html>