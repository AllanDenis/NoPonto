<?php
<<<<<<< HEAD
	const DEBUG = true;
	const NOPONTO = true; // Evita acesso direto a arquivos incluídos
	const CITTAMOBI_API = "http://api.plataforma.cittati.com.br/m3p/js";
	require_once('banco.php');	
	require_once('vendor/autoload.php');
	require_once('../geophp.test/geoPHP.inc');
	$app = new \Slim\Slim(array());
	$app->setName('noponto');

	if(DEBUG){
		$app->config('debug', true);
		$app->get('/teste', function () {
			echo "<script>console.log('Versão do GEOS: " . GEOSversion() . "');</script>";
			echo "<script>console.log('Versão do geoPHP: " . geoPHP::version() . "');</script>";
			$sql = 	"SELECT * 
=======
const DEBUG = true;
const NOPONTO = true; // Evita acesso direto a arquivos incluídos
const CITTAMOBI_API = "http://api.plataforma.cittati.com.br/m3p/js";
// require_once ("../banco.php");
// Evita acesso direto a arquivos incluídos
defined ( 'NOPONTO' ) or die ( "Acesso direto proibido. ;)" );
// require_once('config.php');
// Evita acesso direto a arquivos incluídos
// defined ( 'NOPONTO' ) or die ( "Acesso direto proibido. ;)" );
const BD_HOST = 'localhost';
const BD_BANCO = 'noponto';
const BD_USUARIO = 'noponto';
const BD_SENHA = 'er3UpKCsneGFRJHs';
function getConnection() {
	$dbhost = BD_HOST;
	$dbuser = BD_USUARIO;
	$dbpass = BD_SENHA;
	$dbname = BD_BANCO;
	$dbh = new PDO ( "mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass );
	$dbh->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$dbh->setAttribute ( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
	return $dbh;
}

$conexao = getConnection ();
require_once ('vendor/autoload.php');
require_once ('../geophp.test/geoPHP.inc');
$app = new \Slim\Slim ();
$app->setName ( 'noponto' );
$app->contentType ( 'application/json' );
if (DEBUG) {
	$app->config ( 'debug', true );
	$app->get ( '/teste', function () {
		$geojson = array (
				'type' => 'FeatureCollection',
				'features' => array()
		);
		$sql = "SELECT id_original AS id,
						Y(gps) AS lat, X(gps) AS lng,
						nome, sentido, tipo
>>>>>>> origin/master
						FROM pontos";
		$resultado = $GLOBALS ["conexao"]->query ( $sql );
		// Padrão GeoJSON
		foreach ( $resultado->fetchAll () as $i => $linha ) {
			$geojson ['features'] [$i] ['type'] = 'Feature';
			$geojson ['features'] [$i] ['id'] = ( int ) $linha ['id'];
			$geojson ['features'] [$i] ['properties'] = array (
					'nome' => $linha ['nome'],
					'sentido' => $linha ['sentido'],
					'tipo' => $linha ['tipo'] 
			);
			$geojson ['features'] [$i] ['geometry'] = array (
					'type' => 'Point',
					'coordinates' => [ 
							( float ) $linha ['lng'],
							( float ) $linha ['lat'] 
					] 
			);
		}
		$geojson = json_encode ( $geojson, JSON_UNESCAPED_UNICODE );
		echo $geojson;
		// echo "<title>Debug</title>";
		// echo "<h2>Versão do GEOS: " . GEOSversion() . "</h2><br>";
		// echo "<h2>Versão do geoPHP: " . geoPHP::version() . "</h2><br>";
		// // $sql = "SELECT * FROM pontos";
		// $resultado = $GLOBALS["conexao"]->query($sql);
		// echo json_encode($resultado->fetchAll(), JSON_UNESCAPED_UNICODE);
		// phpinfo();
	} );
}

// Pontos [OK]
$app->get ( '/pontos', function () {
	$sql = "SELECT id_original AS id, ASTEXT(GPS) AS gps, sentido, tipo FROM pontos";
	$resultado = $GLOBALS ["conexao"]->query ( $sql );
	echo json_encode ( $resultado->fetchAll (), JSON_UNESCAPED_UNICODE );
} );

// Ponto por ID [OK]
$app->get ( '/ponto/:ponto_id', function ($ponto_id) {
	$sql = sprintf ( "SELECT id, nome, ASTEXT(GPS) as gps, sentido, tipo
					FROM pontos
					WHERE id_original = %s", $ponto_id );
	$resultado = $GLOBALS ["conexao"]->query ( $sql );
	echo json_encode ( $resultado->fetchAll (), JSON_UNESCAPED_UNICODE );
} )->conditions ( array (
		'ponto_id' => '\d+' 
) ); // Validação da entrada
     
// Pontos por linha
$app->get ( '/pontos/por-linha/:linha_id', function ($linha_id) {
	$sql = sprintf ( "SELECT ordem_ponto AS ordem, ponto_id AS id
					FROM  rota_contem_pontos
					WHERE rota_id = %s
					ORDER BY ordem_ponto", $linha_id );
	$resultado = $GLOBALS ["conexao"]->query ( $sql );
	echo json_encode ( $resultado->fetchAll (), JSON_UNESCAPED_UNICODE );
} )->conditions ( array (
		'linha_id' => '\d+' 
) ); // Validação da entrada
     
// Linhas
$app->get ( '/linhas', function () {
	$sql = "SELECT * FROM  rotas";
	$resultado = $GLOBALS ["conexao"]->query ( $sql );
	echo json_encode ( $resultado->fetchAll (), JSON_UNESCAPED_UNICODE );
} );

// Linha por ID
$app->get ( '/linha/:linha_id', function ($linha_id) {
	$sql = sprintf ( "SELECT *
					FROM  rotas
					WHERE id_original = %s", $linha_id );
	$resultado = $GLOBALS ["conexao"]->query ( $sql );
	echo json_encode ( $resultado->fetchAll (), JSON_UNESCAPED_UNICODE );
} )->conditions ( array (
		'linha_id' => '\d+' 
) ); // Validação da entrada
     
// Linhas por ponto
$app->get ( '/linhas/por-ponto/:ponto_id', function ($ponto_id) {
	$sql = sprintf ( "SELECT rota_id AS rota
					FROM  rota_contem_pontos
					WHERE ponto_id = %s", $ponto_id );
	$resultado = $GLOBALS ["conexao"]->query ( $sql );
	echo json_encode ( $resultado->fetchAll (), JSON_UNESCAPED_UNICODE );
} )->conditions ( array (
		'ponto_id' => '\d+' 
) ); // Validação da entrada
     
// Ônibus por linha
$app->get ( '/onibus/por-linha/:linha_id', function ($linha_id) {
	echo file_get_contents ( CITTAMOBI_API . '/vehicles/service/' . $linha_id );
} )->conditions ( array (
		'linha_id' => '\d+' 
) ); // Validação da entrada;
     
// Como chegar - versão inicial:
     // Condições: origem e destino devem estar na mesma linha e no mesmo sentido (ida ou volta)
     // Retorna o ID da rota
$app->get ( '/como-chegar/:pontoOrigem/:pontoDestino', function ($pontoOrigem, $pontoDestino) {
	$sql = sprintf ( "SELECT rota_id FROM  rota_contem_pontos 
						WHERE ponto_id = %s OR ponto_id = %s 
						HAVING COUNT( rota_id ) > 1", $pontoOrigem, $pontoDestino );
	$resultado = $GLOBALS ["conexao"]->query ( $sql );
	echo json_encode ( $resultado->fetchAll (), JSON_UNESCAPED_UNICODE );
} )->conditions ( array (
		'ponto_id' => '\d+' 
) ); // Validação da entrada

$app->run ();
?>
