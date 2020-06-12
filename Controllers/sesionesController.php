<?php 

require_once('../Models/DB.php');
require_once('../Models/Response.php');

try {
    $connection=DB::dbConnect();
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch (PDOException $e){
    error_log("Error de conexión - " . $e);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Error en conexión a Base de datos");
    $response->send();
    exit();
}
if (array_key_exists('id_sesion', $_GET)) {
    $id_sesion=$_GET['id_sesion'];
    if ($id_sesion==='' || !is_numeric($id_sesion)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        ($id_sesion==='' ? $response->addMessage("Id de la sesión no puede estar vacío") : false);
        (!is_numeric($id_sesion) ? $response->addMessage("Id de la sesión debe ser numérico") : false);
        $response->send();
        exit();
    }
    if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION'])<1) {
        $response = new Response();
        $response->setHttpStatusCode(401);
        $response->setSuccess(false);
        $response->addMessage("No se encontró el token de acceso");
        $response->send();
        exit();
    }
    $token = $_SERVER['HTTP_AUTHORIZATION'];
    if ($_SERVER['REQUEST_METHOD']==='DELETE') {
        try {
            $query=$connection->prepare('DELETE FROM sesiones WHERE id_sesion=:id AND token=:token');
            $query->bindParam(':id', $id_sesion, PDO::PARAM_INT);
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->execute();
            $rowCount=$query->rowCount();
            if ($rowCount===0) {
                $response=new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Error al cerrar la sesión usando el token dado");
                $response->send();
                exit();
            }
            $returnData=array();
            $returnData['id_sesion']=intval($id_sesion);
            $response=new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($returnData);
            $response->addMessage("Sesión cerrada");
            $response->send();
            exit();
        }
        catch (PDOException $ex) {
            $response=new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al cerrar la sesión. Inténtelo de nuevo");
            $response->send();
            exit();
        }
    }
    elseif($_SERVER['REQUEST_METHOD']==='PATCH') {
        if($_SERVER['CONTENT_TYPE']!=='application/json') {
            $response=new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Encabezado Content Type no es JSON");
            $response->send();
            exit();
        }
        $patchData=file_get_contents('php://input');
        if (!$jsonData=json_decode($patchData)){
            $response=new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Cuerpo de la solicitud no es un JSON válido");
            $response->send();
            exit();
        }
        if(!isset($jsonData->token_actualizacion) || strlen($jsonData->token_actualizacion)<1) {
            $response=new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (!isset($jsonData->token_actualizacion) ? $response->addMessage("No se encontró el token de actualización") : false);
            (strlen($jsonData->token_actualizacion)<1 ? $response->addMessage("El token de actualización no debe ser vacío") : false);
            $response->send();
            exit();
        }
        try{
            $token_actualizacion = $jsonData->token_actualizacion;
            $query=$connection->prepare('SELECT sesiones.id AS id_sesion, sesiones.id_usuario, status, token, token_actualizacion, caducidad_token_acceso, 
            caducidad_token_actualizacion FROM sesiones, usuarios WHERE sesiones.id_usuario=usuarios.id AND sesiones.id=:id_sesion AND sesiones.token=:token AND 
            token_actualizacion=:token_actualizacion');
            $query->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->bindParam(':token_actualizacion', $token_actualizacion, PDO::PARAM_STR);
            $query->execute();
            $rowCount=$query->rowCount();
            if ($rowCount===0) {
                $response=new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Token de acceso o token de actualización es incorrecto para el id de la sesión");
                $response->send();
                exit();
            }
            $row=$query->fetch(PDO::FETCH_ASSOC);
            $consulta_id=$row['id_sesion'];
            $consulta_id_usuario=$row['id_usuario'];
            $consulta_activo=$row['status'];
            $consulta_token=$row['token'];
            $consulta_tokenActualizacion=$row['token_actualizacion'];
            $consulta_cad=$row['caducidad'];
            $consulta_cadTokenActualizacion=$row['caducidad_token_actualizacion'];
            if($consulta_activo!=='ACTIVO') {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Usuario no activo");
                $response->send();
                exit();
            }
            if(strtotime($consulta_cadTokenActualizacion)<time()) {
                $response=new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Token de actualización ha caducado - inicie sesión de nuevo");
                $response->send();
                exit();
            }
            $token=base64_encode(bin2hex(openssl_random_pseudo_bytes(24).time()));
            $token_actualizacion=base64_encode(bin2hex(openssl_random_pseudo_bytes(24).time()));
            $caducidad_tacceso_s=1200;
            $caducidad_tactualizacion_s=1296000;
            $query = $connection->prepare('UPDATE sesiones SET token=:token, caducidad_token_acceso=DATE_ADD(NOW(), INTERVAL :caducidad_tacceso_s SECOND), 
            token_actualizacion=:token_actualizacion, caducidad_token_actualizacion=DATE_ADD(NOW(), INTERVAL :caducidad_tactualizacion_s SECOND) WHERE id=:id_sesion AND 
            id_usuario=:id_usuario AND token=:consulta_tokenAcceso AND token_actualizacion=:consulta_tokenActualizacion');
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->bindParam(':caducidad_tacceso_s', $caducidad_tacceso_s, PDO::PARAM_INT);
            $query->bindParam(':token_actualizacion', $token_actualizacion, PDO::PARAM_STR);
            $query->bindParam(':caducidad_tactualizacion_s', $caducidad_tactualizacion_s, PDO::PARAM_INT);
            $query->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
            $query->bindParam(':id_usuario', $consulta_id_usuario, PDO::PARAM_INT);
            $query->bindParam(':consulta_tokenAcceso', $consulta_tokenAcceso, PDO::PARAM_STR);
            $query->bindParam(':consulta_tokenActualizacion', $consulta_tokenActualizacion, PDO::PARAM_STR);
            $query->execute();
            $rowCount=$query->rowCount();
            if($rowCount===0) {
                $response=new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("El token de acceso no pudo ser actualizado. Inicie sesión de nuevo");
                $response->send();
                exit();
            }
            $returnData=array();
            $returnData['id_sesion'] = $id_sesion;
            $returnData['token']=$token;
            $returnData['caducidad_token_acceso']=$caducidad_tacceso_s;
            $returnData['token_actualizacion']=$token_actualizacion;
            $returnData['caducidad_token_actualizacion'] = $caducidad_tactualizacion_s;
            $response=newResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage('Token actualizado');
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch(PDOException $e) {
            error_log('Error en BD - ' . $e);
            $response=new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al actualizar el token - inicie sesión de nuevo");
            $response->send();
            exit();
        }
    }
    else {
        $response=new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
}
elseif (empty($_GET)) {
    if($_SERVER['REQUEST_METHOD']!=='POST') {
        $response=new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
    if ($_SERVER['CONTENT_TYPE']!=='application/json') {
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Encabezado Content Type no es JSON");
        $response->send();
        exit();
    }
    $postData=file_get_contents('php://input');
    if(!$jsonData=json_decode($postData)) {
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Cuerpo de la solicitud no es un JSON válido");
        $response->send();
        exit();
    }
    if(!isset($jsonData->nombre_usuario) || !isset($jsonData->contrasena)) {
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        (!isset($jsonData->nombre_usuario) ? $response->addMessage("El nombre de usuario es obligatorio") : false);
        (!isset($jsonData->contrasena) ? $response->addMessage("La contraseña es obligatoria") : false);
        $response->send();
        exit();
    }
    try {
        $nombre_usuario=trim($jsonData->nombre_usuario);
        $contrasena=trim($jsonData->contrasena);
        $query=$connection->prepare('SELECT id, nombre, contraseña, status FROM usuarios WHERE nombre=:nombre');
        $query->bindParam(':nombre', $nombre_usuario, PDO::PARAM_STR);
        $query->execute();
        $rowCount=$query->rowCount();
        //echo $rowCount;
        if ($rowCount===0) {
            $response=new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Nombre de usuario incorrecto");
            //$response->send();
            exit();
        }
        $row=$query->fetch(PDO::FETCH_ASSOC);
        $consulta_id=$row['id'];
        $consulta_nombreCompleto=$row['nombre'];
        $consulta_contasena=$row['contraseña'];
        $consulta_activo=$row['status'];
        if ($consulta_activo!=='ACTIVO') {
            $response = new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Nombre de usuario no activo");
            $response->send();
            exit();
        }
        if(!password_verify($contrasena, $consulta_contasena)) {
            $response=new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Contraseña incorrecta");
            $response->send();
            exit();
        }
        $token=base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
        $token_actualizacion=base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
        $caducidad_tacceso_s=1200;
        $caducidad_tactualizacion_s=1296000;
    }
    catch(PDOException $e){
        error_log('Error en DB - '.$e);
        $response=new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al iniciar sesión");
        $response->send();
        exit();
    }
    try{
        $connection->beginTransaction();
        $query = $connection->prepare('INSERT INTO sesiones(id_usuario, token, caducidad, token_actualizacion, caducidad_token_actualizacion) VALUES (:id_usuario, 
        :token, DATE_ADD(NOW(), INTERVAL :caducidad_tacceso_s SECOND), :token_actualizacion, DATE_ADD(NOW(), INTERVAL :caducidad_tactualizacion_s SECOND))');
        $query->bindParam(':id_usuario', $consulta_id, PDO::PARAM_INT);
        $query->bindParam(':token', $token, PDO::PARAM_STR);
        $query->bindParam(':caducidad_tacceso_s', $caducidad_tacceso_s, PDO::PARAM_INT);
        $query->bindParam(':token_actualizacion', $token_actualizacion, PDO::PARAM_STR);
        $query->bindParam(':caducidad_tactualizacion_s', $caducidad_tactualizacion_s, PDO::PARAM_INT);
        $query->execute();
        $ultimoID=$connection->lastInsertId();
        $connection->commit();
        $returnData=array();
        $returnData['id_sesion']=intval($ultimoID);
        $returnData['token']=$token;
        $returnData['caducidad_token_acceso']=$caducidad_tacceso_s;
        $returnData['token_actualizacion']=$token_actualizacion;
        $returnData['caducidad_token_actualizacion']=$caducidad_tactualizacion_s;
        $response=new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        $connection->rollBack();
        error_log('Error en DB - '.$e);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al iniciar sesión");
        $response->send();
        exit();
    }
    echo 'listo';
}
else{
    $response=new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Ruta no encontrada");
    $response->send();
    exit();
}

?>