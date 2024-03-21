<?php
ini_set('display_errors', 1);

define( 'ROOT', dirname( __DIR__ ) );
require_once ROOT . '/autoload.php';
require_once ROOT . '/vendor/autoload.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$isFetched = false;
if( $data !== null ) {
    $requestParams = $data;
    $isFetched = true;
} else {
    $requestParams = $_POST;
}

// App name witch set in namespaces
$appName = 'ticketing';


$queryString = rtrim( $_SERVER['QUERY_STRING'], '/' );


$nbRequest = 0;
if( !empty( $queryString ) ) {
    $tabRequest = explode( '/', $queryString );
    $nbRequest = count( $tabRequest );
}
// split params in two field : action & vars
$params = [
    'action'    =>'', 
    'vars'      =>'',
    'request'   => $requestParams,
    'isFetched' => $isFetched,
    'redirect'  => ''
];
if( $nbRequest >=1 && !empty( $tabRequest[0] ) ) {
	// Retrieve controller name
    $controllerName = ucfirst( array_shift( $tabRequest ) );
    if( isset( $tabRequest[0] ) ) {
		// Retrieve action
        $params['action'] = array_shift( $tabRequest );
    }
    // Test de redirection 
    if( isset( $_SESSION['redirect'] ) ) {
        $params['redirect'] = $_SESSION['redirect'];
		unset( $_SESSION['redirect'] );
    }
	// Retrieve vars
    $params['vars'] = $tabRequest;
} else {
    $controllerName = 'Security';
}


$fileName = 'controller/' . $controllerName . 'Controller.php';
$className = $appName .'\\controller\\' . $controllerName . 'Controller';

$controller = new $className( $params );



