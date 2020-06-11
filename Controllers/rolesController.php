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
if(array_key_exists('id_rol', $_GET)) {
    if($_SERVER['REQUEST_METHOD']!=='POST') {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
    else {
        $tipo=$_GET['id_rol'];
        switch($tipo) {
            case 1:
                $tipo='ADMINISTRADOR';
                break;
            case 2:
                $tipo='COMPRADOR';
                break;
            case 3:
                $tipo='VENDEDOR';
                break;
            case 4:
                $tipo='COMPRADOR-VENDEDOR';
                break;
        }
        try {
            $query=$connection->prepare('INSERT INTO roles(tipo) VALUES(:tipo)');
            $query->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $query->execute();
            $rowCount=$query->rowCount();
            if($rowCount===0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al insertar el rol - inténtelo de nuevo");
                $response->send();
                exit();
            }
            $ultimoID=$connection->lastInsertId();
            $returnData=array();
            $returnData['id']=$ultimoID;
            $returnData['tipo']=$tipo;
            $response=new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Rol creado");
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch(PDOException $e) {
            error_log('Error en BD - ' . $e);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al crear rol");
            $response->send();
            exit();
        }
    }
}
/*if ($_SERVER['CONTENT_TYPE']!=='application/json') {
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
if (!isset($json_data->tipo)) {
    $response=new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    (!isset($json_data->tipo) ? $response->addMessage("El tipo de rol es obligatorio") : false);
    $response->send();
    exit();
}
$tipo=trim($json_data->tipo);
try {
    $query=$connection->prepare('INSERT INTO roles(tipo) VALUES(:tipo)');
    $query->bindParam(':tipo', $tipo, PDO::PARAM_STR);
    $query->execute();
    $rowCount=$query->rowCount();
    if($rowCount===0) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al insertar el rol - inténtelo de nuevo");
        $response->send();
        exit();
    }
    $ultimoID=$connection->lastInsertId();
    $returnData=array();
    $returnData['tipo']=$tipo;
    $response=new Response();
    $response->setHttpStatusCode(201);
    $response->setSuccess(true);
    $response->addMessage("Rol creado");
    $response->setData($returnData);
    $response->send();
    exit();
}
catch(PDOException $e) {
    error_log('Error en BD - ' . $e);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Error al crear rol");
    $response->send();
    exit();
}*/

?>