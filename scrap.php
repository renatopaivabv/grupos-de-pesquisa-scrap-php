<?php
setlocale (LC_ALL, 'pt_BR');
date_default_timezone_set('America/Fortaleza');

require __DIR__ . "/vendor/autoload.php";
if(!file_exists("cache")) mkdir("cache");

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;
use DiDom\Document;

$arquivoEspelhos = file_get_contents("espelhos.json");
$espelhos = json_decode($arquivoEspelhos, true);
echo count($espelhos) . " registros de espelhos." . PHP_EOL;
$client = new Client(['headers' => ['user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36']]);

$url = "http://dgp.cnpq.br/dgp/espelhogrupo/";

$mapa = Array(
    0 => "status",
    1 => "anoformacao",
    2 => "datasituacao",
    3 => "ultimoenvio",
    5 => "area",
    14 => "uf",
    20 => "telefone",
    22 => "contato",
    25 => "titulo"
);

//Função criada para filtrar o array de lideres
function filtrar($string) 
{ 
    return (strlen(trim($string)) > 0 ? true : false);
}
//Função criada para retornar um percutual
function percent($passo, $total)
{
    return number_format(($passo / $total)*100, 1) . " %";
}

//Função criada para gravar um arquivo com o nome do espelho e os dados do grupo em formato JSON
function gravar($arq, $grupo)
{
    $arquivo = "cache/" . $arq . ".json";
    $fp = fopen($arquivo, "w");
    fwrite($fp, json_encode($grupo, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE));
    fclose($fp);
    return "Arquivo '" . $arquivo . "' gravado em " . date("H:i:s").PHP_EOL;
}

//Funcção criada para transformar um número de segundos em uma string no formato H:i:s
function tempo($seconds)
{
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}

$total = count($espelhos);
$atual = 0;
//
foreach($espelhos as $k => $esp){
	
    $atual = $k+1;
    if(file_exists("cache/" . $esp . ".json")) {
        echo percent($atual, $total) . " - Arquivo " . $esp . " existe!" . PHP_EOL;
        continue;
    }
    //$html = $client->request("GET", $url . $esp)->getBody()->getContents();
    //$dom = HtmlDomParser::str_get_html($html);
	
	$dom = new Document($url . $esp, true); //Didom
    $grupo = Array();
    $grupo['espelho'] = $esp; 
	
	//Varre o array $mapa e cria uma variavel pra cada item
    foreach($mapa as $k => $v){
        $grupo[$v] = trim($dom->find('div.control-group .controls')[$k]->text());
    }
	//Trecho específico para os lideres
    $lideres = trim($dom->find('div.control-group .controls')[4]->text());
	
    $lideres = str_replace('Permite enviar email', ' ', $lideres);
    $lideres = str_replace('ui-button', ' ', $lideres);
	$lideres = preg_replace('/\$\(function\S+\;/', ' ', $lideres);
	$lideres = preg_replace('/PrimeFaces\S+\;/', ' ', $lideres);
    while(stripos($lideres, '   '))
        $lideres = str_replace('   ', '  ', $lideres);
    $lideres = explode('  ', $lideres);
    
    $lideres = array_filter($lideres, "filtrar");

    foreach($lideres as $l) $grupo['lideres'][] = trim($l);
    echo percent($atual, $total) . " - " . gravar($esp, $grupo);
}

$tempo = microtime(true) - $_SERVER["REQUEST_TIME"];
echo PHP_EOL . ">>> " . tempo($tempo) . " foi o tempo que o arquivo levou pra ser executado" . PHP_EOL;


