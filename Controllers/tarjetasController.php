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
if (!isset($json_data->usuario_id) || !isset($json_data->banco) || !isset($json_data->numero_cuenta)) {
    $response=new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    (!isset($json_data->usuario_id) ? $response->addMessage("El ID del usaurio es obligatorio") : false);
    (!isset($json_data->banco) ? $response->addMessage("El nombre del banco es obligatorio") : false);
    (!isset($json_data->numero_cuenta) ? $response->addMessage("El numero de cuenta es obligatorio") : false);
    $response->send();
    exit();
}
$usuario_id=$json_data->usuario_id;
$banco=trim($json_data->banco);
$numero_cuenta=trim($json_data->numero_cuenta);
try {
    $query=$connection->prepare('SELECT usuario_id FROM tarjetas WHERE numero_cuenta=:numero_cuenta');
    $query->bindParam(':numero_cuenta', $numero_cuenta, PDO::PARAM_STR);
    $query->execute();
    $rowCount=$query->rowCount();
    if($rowCount!==0) {
        $response=new Response();
        $response->setHttpStatusCode(409);
        $response->setSuccess(false);
        $response->addMessage("La cuenta bancaria ya existe");
        $response->send();
        exit();
    }
    $query=$connection->prepare('INSERT INTO tarjetas(usuario_id, banco, numero_cuenta) VALUES(:usuario_id, :banco, :numero_cuenta)');
    $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $query->bindParam(':banco', $banco, PDO::PARAM_STR);
    $query->bindParam(':numero_cuenta', $numero_cuenta, PDO::PARAM_INT);
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
    $returnData['banco']=$banco;
    $returnData['numero_cuenta']=$numero_cuenta;
    $response=new Response();
    $response->setHttpStatusCode(201);
    $response->setSuccess(true);
    $response->addMessage("Tarjeta creada");
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