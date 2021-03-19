<?php

require 'vendor/autoload.php';
 
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$host = getenv("HOST");
$port = getenv("PORT");
$user = getenv("USER");
$pass = getenv("PASS");
$strLocalPath = __DIR__;
$strServerPath = getenv("SERVER_PATH");

if($conn = ftp_connect($host, $port)) {echo "Conexao realizada com sucesso! \n";}
else {die("Nao foi possivel conectar ao $host");}

function getEspelhos()
{
    return json_decode(file_get_contents('espelhos.json'));
}

try {
    $login = ftp_login($conn, $user, $pass);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}


if (isset($login) && $login)
{ 
    // Change the dir 
    ftp_chdir($conn, $strServerPath . '/cache');         
    $pasv = ftp_pasv($conn, true);

    $esp = getEspelhos();

    foreach($esp as $v)
    {   
        $remoteFile = $v . ".json";
        $localFile = "cache/" . $remoteFile;
        subir_arquivo($conn, $localFile, $remoteFile);
    }
	
	ftp_chdir($conn, $strServerPath);
	subir_arquivo($conn, 'espelhos.json', 'espelhos.json');

}

function subir_arquivo($conn, $localFile, $remoteFile)
{
	if(file_exists($localFile))
        {
            if(ftp_put($conn, $remoteFile, $localFile, FTP_ASCII))
                echo $msgSuccess[] = "Arquivo " . $remoteFile ." foi upado com sucesso\n";
            else 
                echo $msgErro[] = "Ocorreu um erro ao tentar fazer upload do arquivo " . $localFile ."\n";
        }
        else
        {
            $msgErro[] = "Arquivo <strong>" . $localFile ."</strong> não existe";
        }
}

desconectar($conn);
function desconectar($conn)
{
	if(ftp_close($conn)) echo "Desconectado!\n";
}
