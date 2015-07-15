<?php
	// Evita acesso direto a arquivos incluídos
	defined('NOPONTO') or die("Acesso direto proibido. ;)");
	require_once('config.php');
	
	function getConnection() {
		$dbhost=BD_HOST;
		$dbuser=BD_USUARIO;
		$dbpass=BD_SENHA;
		$dbname=BD_BANCO;
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);  
		$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $dbh;
    }
	
	$conexao = getConnection();
?>
