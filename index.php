<?php
$arquivoEspelhos = file_get_contents("espelhos.json");
$espelhos = json_decode($arquivoEspelhos, true);
$status = array();
$uf = array();
foreach($espelhos as $esp) {
    $grupo = json_decode(file_get_contents("cache/" . $esp . ".json"));
	isset($status[$grupo->status]) ? $status[$grupo->status]++ : $status[$grupo->status]= 1 ;
	isset($uf[$grupo->uf]) ? $uf[$grupo->uf]++ : $uf[$grupo->uf] = 1;
}
echo "\nResumo de STATUS\n";
foreach($status as $k => $v) printf("%s %s<br>\n", $v, $k);

echo "\nResumo de ESTADO\n";
foreach($uf as $k => $v) printf("%s %s<br>\n", $v, $k);
