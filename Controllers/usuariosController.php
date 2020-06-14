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
if ($_SERVER['REQUEST_METHOD']==='PATCH') {
    if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION'])<1) {
        $response=new Response();
        $response->setHttpStatusCode(401);
        $response->setSuccess(false);
        $response->addMessage("No se encontró el token de acceso");
        $response->send();
        exit();
    }
    $accesstoken=$_SERVER['HTTP_AUTHORIZATION']; 
    try {
        $query=$connection->prepare('SELECT id_usuario, caducidad, status FROM sesiones, usuarios WHERE sesiones.id_usuario=usuarios.id AND token=:token_acceso');
        $query->bindParam(':token_acceso', $accesstoken, PDO::PARAM_STR);
        $query->execute();
        $rowCount=$query->rowCount();
        if ($rowCount===0) {
            $response=new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Token de acceso no válido");
            $response->send();
            exit();
        }
        $row=$query->fetch(PDO::FETCH_ASSOC);
        $consulta_idUsuario=$row['id_usuario'];
        $consulta_cadTokenAcceso=$row['caducidad'];
        $consulta_activo=$row['status'];
        if($consulta_activo!=='ACTIVO') {
            $response=new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Cuenta de usuario no activa");
            $response->send();
            exit();
        }
        if(strtotime($consulta_cadTokenAcceso)>time()) {
            $response=new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Token de acceso ha caducado");
            $response->send();
            exit();
        }
    } 
    catch(PDOException $e) {
        error_log('Error en DB - ' . $e);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al autenticar usuario");
        $response->send();
        exit();
    }
    $token=$_SERVER['HTTP_AUTHORIZATION'];
    $postData=file_get_contents('php://input');
    if ($_SERVER['CONTENT_TYPE']!=='application/json'){
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage('Encabezado "Content type" no es JSON');
        $response->send();
        exit();
    }
    if (!$json_data=json_decode($postData)) {
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
        $response->send();
        exit();
    }
    if (!isset($json_data->id_usuario) || !isset($json_data->id_sesion)) {
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage('No se pudo completar el proceso de desactivación de la cuenta');
        $response->send();
        exit();
    }
    $user=$json_data->id_usuario;
    $sesion=$json_data->id_sesion;
    $query=$connection->prepare('DELETE FROM sesiones WHERE id_sesion=:id AND token=:token');
    $query->bindParam(':id', $sesion, PDO::PARAM_INT);
    $query->bindParam(':token', $token, PDO::PARAM_STR);
    $query->execute();
    $rowCount=$query->rowCount();
    if ($rowCount===0) {
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Error al cerrar sesión");
        $response->send();
        exit();
    }
    $query=$connection->prepare('UPDATE usuarios SET status="BAJA" WHERE id=:id');
    $query->bindParam(':id', $user, PDO::PARAM_INT);
    $query->execute();
    $rowCount=$query->rowCount();
    if ($rowCount===0) {
        $response=new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al desactivar la cuenta");
        $response->send();
        exit();
    }
    $response=new Response();
    $response->setHttpStatusCode(200);
    $response->setSuccess(true);
    $response->addMessage("Cuenta desactivada");
    $response->send();
    exit();
}
else {
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
    if (!isset($json_data->nombre) || !isset($json_data->contrasena) || !isset($json_data->rol_id) || !isset($json_data->pais) || !isset($json_data->fecha_alta) || 
        !isset($json_data->calle) || !isset($json_data->colonia) || !isset($json_data->numero_exterior) || !isset($json_data->codigo_postal) || !isset($json_data->estado)) {
        $response=new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        (!isset($json_data->nombre) ? $response->addMessage("El nombre completo es obligatorio") : false);
        (!isset($json_data->rol_id) ? $response->addMessage("La contraseña es obligatoria") : false);
        (!isset($json_data->pais) ? $response->addMessage("El nombre completo es obligatorio") : false);
        (!isset($json_data->fecha_alta) ? $response->addMessage("La contraseña es obligatoria") : false);
        (!isset($json_data->calle) ? $response->addMessage("El nombre completo es obligatorio") : false);
        (!isset($json_data->colonia) ? $response->addMessage("La contraseña es obligatoria") : false);
        (!isset($json_data->numero_exterior) ? $response->addMessage("El nombre completo es obligatorio") : false);
        (!isset($json_data->codigo_postal) ? $response->addMessage("La contraseña es obligatoria") : false);
        (!isset($json_data->estado) ? $response->addMessage("El nombre completo es obligatorio") : false);
        (!isset($json_data->contrasena) ? $response->addMessage("La contraseña es obligatoria") : false);
        $response->send();
        exit();
    }
    $nombre=trim($json_data->nombre);
    $rol_id=$json_data->rol_id;
    $pais=trim($json_data->pais);
    $fecha_alta=$json_data->fecha_alta;
    $calle=trim($json_data->calle);
    $colonia=trim($json_data->colonia);
    $numero_exterior=$json_data->numero_exterior;
    $codigo_postal=$json_data->codigo_postal;
    $estado=trim($json_data->estado);
    $status=trim($json_data->status);
    $email=trim($json_data->email);
    $contrasena=$json_data->contrasena;
    $foto_perfil=$json_data->foto_perfil;
    try {
        $query=$connection->prepare('SELECT status FROM usuarios WHERE nombre=:nombre');
        $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $query->execute();
        $rowCount=$query->rowCount();
        if ($rowCount!==0) {
            $row=$query->fetch(PDO::FETCH_ASSOC);
            if($row['status']==='ACTIVO') {
                $response=new Response();
                $response->setHttpStatusCode(409);
                $response->setSuccess(false);
                $response->addMessage("El nombre de usuario ya existe");
                $response->send();
                exit();
            }
        }
        $contrasena_hash=password_hash($contrasena, PASSWORD_BCRYPT);
        $query=$connection->prepare('INSERT INTO usuarios(nombre, rol_id, pais, fecha_alta, calle, colonia, numero_exterior, codigo_postal, estado, status, email, contraseña, 
            foto_perfil) VALUES(:nombre, :rol_id, :pais, STR_TO_DATE(:fecha_alta, \'%Y-%m-%d %H:%i\'), :calle, :colonia, :numero_exterior, :codigo_postal, :estado, :status, 
            :email, :contrasena, :foto_perfil)');
        $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $query->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
        $query->bindParam(':pais', $pais, PDO::PARAM_STR);
        $query->bindParam(':fecha_alta', $fecha_alta, PDO::PARAM_STR);
        $query->bindParam(':calle', $calle, PDO::PARAM_STR);
        $query->bindParam(':colonia', $colonia, PDO::PARAM_STR);
        $query->bindParam(':numero_exterior', $numero_exterior, PDO::PARAM_INT);
        $query->bindParam(':codigo_postal', $codigo_postal, PDO::PARAM_INT);
        $query->bindParam(':estado', $estado, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':contrasena', $contrasena_hash, PDO::PARAM_STR);
        $query->bindParam(':foto_perfil', $foto_perfil, PDO::PARAM_LOB);
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
        $ultimoID=$connection->lastInsertId();
        $returnData=array();
        $returnData['id']=$ultimoID;
        $returnData['nombre']=$nombre;
        $returnData['rol_id']=$rol_id;
        $returnData['pais']=$pais;
        $returnData['fecha_alta']=$fecha_alta;
        $returnData['calle']=$calle;
        $returnData['colonia']=$colonia;
        $returnData['numero_exterior']=$numero_exterior;
        $returnData['codigo_postal']=$codigo_postal;
        $returnData['estado']=$estado;
        $returnData['status']=$status;
        $returnData['email']=$email;
        $response=new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("Usuario creado");
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
}

?>