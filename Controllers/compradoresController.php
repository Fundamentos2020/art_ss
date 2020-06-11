<?php

require_once('../Models/DB.php');
require_once('../Models/Response.php');

try {
    $connection=connect::dbConnect();
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch (PDOException $e){
    error_log("Error de conexión - ".$e);
    $response=new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Error en conexión a Base de datos");
    $response->send();
    exit();
}
if ($_SERVER['REQUEST_METHOD']!=='POST') {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Método no permitido");
    $response->send();
    exit();
}
if ($_SERVER['CONTENT_TYPE']!=='application/json') {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("Encabezado Content Type no es JSON");
    $response->send();
    exit();
}
$postData = file_get_contents('php://input');
if (!$json_data = json_decode($postData)) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("El cuerpo de la solicitud no es un JSON válido");
    $response->send();
    exit();
}
if (!isset($json_data->usuario_id)) {
    $response=new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("El ID del usaurio es obligatorio");
    $response->send();
    exit();
}
$usuario_id=$json_data->usuario_id;
$compras_id=$json_data->compras_id;
try {
    $query=$connection->prepare('INSERT INTO compradores(usuario_id, compras_id) VALUES(:usuario_id, :compras_id)');
    $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $query->bindParam(':compras_id', $compras_id, PDO::PARAM_STR);
    $query->execute();
    $rowCount=$query->rowCount();
    if($rowCount===0) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al crear usuario - inténtelo de nuevo");
        $response->send();
        exit();
    }
    $returnData=array();
    $returnData['usuario_id']=$usuario_id;
    $returnData['compras_id']=$compras_id;
    $response=new Response();
    $response->setHttpStatusCode(201);
    $response->setSuccess(true);
    $response->addMessage("Comprador creado");
    $response->setData($returnData);
    $response->send();
    exit();
}
catch(PDOException $e) {
    error_log('Error en BD - ' . $e);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Error al crear usuario");
    $response->send();
    exit();
}

?>