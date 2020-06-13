<?php

require_once('../Models/DB.php');
require_once('../Models/Response.php');

try {
    $connection=DB::dbConnect();
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
if (!isset($json_data->usuario_id) || (!isset($json_data->cuenta_paypal) && !isset($json_data->cuenta_tarjeta))) {
    $response=new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    (!isset($json_data->usuario_id) ? $response->addMessage("El ID del usaurio es obligatorio") : false);
    (!isset($json_data->cuenta_paypal) && !isset($json_data->cuenta_tarjeta) ? $response->addMessage("Los datos de pago son obligatorios") : false);
    $response->send();
    exit();
}
$usuario_id=$json_data->usuario_id;
$cuenta_paypal=$json_data->cuenta_paypal;
$cuenta_tarjeta=$json_data->cuenta_tarjeta;
try {
    if($cuenta_paypal!==null) {
        //echo "Entro";
        $cuenta_paypal=trim($json_data->cuenta_paypal);
        $query=$connection->prepare('SELECT usuario_id FROM clientes WHERE cuenta_paypal=:cuenta_paypal');
        $query->bindParam(':cuenta_paypal', $cuenta_paypal, PDO::PARAM_STR);
        $query->execute();
        $rowCount=$query->rowCount();
        if($rowCount!==0) {
            $response=new Response();
            $response->setHttpStatusCode(409);
            $response->setSuccess(false);
            $response->addMessage("La cuenta de PayPal ya existe");
            $response->setData($cuenta_paypal);
            $response->send();
            exit();
        }
    }
    if($cuenta_tarjeta!==null) {
        $query=$connection->prepare('SELECT usuario_id FROM clientes WHERE cuenta_tarjeta=:cuenta_tarjeta');
        $query->bindParam(':cuenta_tarjeta', $cuenta_tarjeta, PDO::PARAM_INT);
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
    }
    $query=$connection->prepare('INSERT INTO clientes(usuario_id, cuenta_paypal, cuenta_tarjeta) VALUES(:usuario_id, :cuenta_paypal, :cuenta_tarjeta)');
    $query->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $query->bindParam(':cuenta_paypal', $cuenta_paypal, PDO::PARAM_STR);
    $query->bindParam(':cuenta_tarjeta', $cuenta_tarjeta, PDO::PARAM_INT);
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
    $returnData['cuenta_tarjeta']=$cuenta_tarjeta;
    $returnData['cuenta_paypal']=$cuenta_paypal;
    $response=new Response();
    $response->setHttpStatusCode(201);
    $response->setSuccess(true);
    $response->addMessage("Cliente creado");
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