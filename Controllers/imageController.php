<?php

require_once('../Models/DB.php');
require_once('../Models/Response.php');

$response=new Response();

try {
    if($_SERVER['REQUEST_METHOD']==='POST') {
        if(!isset($_FILES['imagen']['error'] ) || is_array( $_FILES['imagen']['error'] ) )    {
            throw new Exception('Error en los parametros.', 400 );
        }
        if($_FILES['imagen']['error']!==UPLOAD_ERR_OK )    {
            $mensaje = 'Error al subir imagen.' . Imagen::ImagenUploadErrors[$_FILES['imagen']['error']] ;
            throw new Exception( $mensaje, 500 );
        }
        $nombre_imagen=$_FILES['imagen']['name'];
        $tmp=$_FILES['imagen']['tmp_name'];
        move_uploaded_file($tmp, "../images/".$nombre_imagen);
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage('Imagen subida.');
        $response->setData( $nombre_imagen );
        $response->send();
    }
    else    {
        throw new Exception('Método no permitido', 405 );
    }
}
catch( Exception $e )   {
    if( get_class( $e ) === 'PDOException' ) {
        error_log('Error en base de datos - ' . $e );
    }
    $response->setHttpStatusCode( $e->getCode() );
    $response->addMessage( $e->getMessage() );
}