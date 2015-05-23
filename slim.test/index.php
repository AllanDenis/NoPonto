<?php
	const DEBUG = false;
	// Evita acesso direto a arquivos incluídos
	const NOPONTO = true;
	require_once('banco.php');	
	require_once('vendor/autoload.php');
	require_once('../geophp.test/geoPHP/geoPHP.inc');

	$app = new \Slim\Slim(array());
	$app->setName('noponto');
	
	if(DEBUG){
		echo "Versão do GEOS: " . GEOSversion() . "<br>";
		echo "Versão do geoPHP: " . geoPHP::version() . "<br>";
		$app->config('debug', true);
	}
	
	//Ponto por ID
	$app->get('/ponto/:ponto_id', function ($ponto_id) {
		$sql = "SELECT id, nome, ASTEXT(GPS) as gps, sentido, tipo FROM pontos WHERE id = " . $ponto_id;
		$resultado = $GLOBALS["conexao"]->query($sql);
		foreach($resultado as $linha){
			if(DEBUG) var_dump($linha);
			echo json_encode($linha, JSON_UNESCAPED_UNICODE);
		}
	}) ->conditions(array('ponto_id' => '\d+')); // Validação da entrada

	//Lista de pontos
	$app->get('/pontos', function () {
		$sql = "SELECT id, ASTEXT(GPS) as gps, sentido, tipo FROM pontos";
		$resultado = $GLOBALS["conexao"]->query($sql);
		foreach($resultado as $linha){
			if(DEBUG) var_dump($linha);
			echo json_encode($linha, JSON_UNESCAPED_UNICODE);
		}
	});

	//Ponto por ID
	$app->get('/pontos/linha/:linha_id', function ($linha_id) {
		$sql = sprintf(
					"SELECT ponto_id, ordem_ponto
					FROM  rota_contem_pontos
					WHERE rota_id = %s
					ORDER BY ordem_ponto", 
					$linha_id
				);
		$resultado = $GLOBALS["conexao"]->query($sql);
		foreach($resultado as $linha){
			if(DEBUG) var_dump($linha);
			echo json_encode($linha, JSON_UNESCAPED_UNICODE);
		}
	}) ->conditions(array('linha_id' => '\d+')); // Validação da entrada

	
	$app->run();
?>
