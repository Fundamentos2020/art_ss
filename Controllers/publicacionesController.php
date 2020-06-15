<?php
require_once('../Models/DB.php');
require_once('../Models/Publicacion.php');
require_once('../Models/Response.php');

try {
    $connection = DB::dbConnect();
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

if($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (array_key_exists("id", $_GET))
        getById($_GET['id']);
    else if(array_key_exists("vendedor_id", $_GET))
        getByVendedorId($_GET['vendedor_id']);
    else if(array_key_exists("comprador_id", $_GET))
        getByCompradorId($_GET['comprador_id']);
    else if(array_key_exists("categoria", $_GET))
        getByCategoria($_GET['categoria']);
    else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
}
else if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION'])<1) {
        $response = new Response();
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
    savePublicacion();
}
else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    editPublicacion();
}
else {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Metodo no permitido");
    $response->send();
    exit();
}

/* ------------------------------------------- GETS -------------------------------------------------- */
//Obtiene la publicacion en base a su id
function getById($id) {
    if ($id == '' || !is_numeric($id)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El id de publicacion no puede estar vacío y debe ser numérico");
        $response->send();
        exit();
    }

    try {
        $connection = DB::dbConnect();
        $query = $connection->prepare('SELECT * FROM publicaciones WHERE id = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor_id'], $row['comprador_id'], $row['fecha_alta'], 
            $row['precio'], $row['vistas'], $row['categoria']);
            $publicacion->setImagen("data:imagen/png;base64, ".base64_encode($row['imagen']));
            $publicaciones[] = $publicacion->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $publicaciones;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setToCache(true);
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch(PublicacionException $e){
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        error_log("Error en BD - " . $e);

        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error en consulta de publicacion");
        $response->send();
        exit();
    }
}

//Publicaciones por vendedor
function getByVendedorId($vendedor_id) {
    if ($vendedor_id == '' || !is_numeric($vendedor_id)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El id de vendedor no puede estar vacío y debe ser numérico");
        $response->send();
        exit();
    }

    try {
        $connection = DB::dbConnect();
        $query = $connection->prepare('SELECT * FROM publicaciones WHERE vendedor_id = :vendedor_id');
        $query->bindParam(':vendedor_id', $vendedor_id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor_id'], $row['comprador_id'], $row['fecha_alta'], 
            $row['precio'], $row['vistas'], $row['categoria']);
            $publicacion->setImagen("data:imagen/png;base64, ".base64_encode($row['imagen']));
            $query=$connection->prepare('SELECT nombre FROM usuarios WHERE id=:vendedor_id');
            $query->bindParam(':vendedor_id', $row['vendedor_id'], PDO::PARAM_INT);
            $query->execute();
            $r=$query->fetch(PDO::FETCH_ASSOC);
            $publicacion->setNombreVendedor($r['nombre']);            
            if ($publicacion->getComprador()!==null) {
                $query=$connection->prepare('SELECT nombre FROM usuarios WHERE id=:comprador_id');
                $query->bindParam(':comprador_id', $row['comprador_id'], PDO::PARAM_INT);
                $query->execute();
                $r=$query->fetch(PDO::FETCH_ASSOC);
                $publicacion->setNombreComprador($r['nombre']);
            }
            $publicaciones[] = $publicacion->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $publicaciones;


        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setToCache(true);
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch(PublicacionException $e){
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        error_log("Error en BD - " . $e);

        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error en consulta de publicacion");
        $response->send();
        exit();
    }
}

//Obtiene las publicaciones por comprador
function getByCompradorId($comprador_id) {
    if ($comprador_id == '' || !is_numeric($comprador_id)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El id de comprador no puede estar vacío y debe ser numérico");
        $response->send();
        exit();
    }

    try {
        $connection = DB::dbConnect();
        $query = $connection->prepare('SELECT * FROM publicaciones WHERE comprador_id = :comprador_id');
        $query->bindParam(':comprador_id', $comprador_id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor_id'], $row['comprador_id'], $row['fecha_alta'], 
            $row['precio'], $row['vistas'], $row['categoria']);
            $publicacion->setImagen("data:imagen/png;base64, ".base64_encode($row['imagen']));
            $publicaciones[] = $publicacion->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $publicaciones;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setToCache(true);
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch(PublicacionException $e){
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        error_log("Error en BD - " . $e);

        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error en consulta de publicacion");
        $response->send();
        exit();
    }
}

//Obtiene las publicaciones en base a una categoría
function getByCategoria($categoria) {
    if ($categoria == '') {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("La categoria no puede estar vacía");
        $response->send();
        exit();
    }

    try {
        $connection = DB::dbConnect();
        $query = $connection->prepare('SELECT * FROM publicaciones WHERE categoria = :categoria AND comprador_id is NULL');
        $query->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor_id'], $row['comprador_id'], $row['fecha_alta'], 
            $row['precio'], $row['vistas'], $row['categoria']);
            $publicacion->setImagen("data:imagen/png;base64, ".base64_encode($row['imagen']));
            $publicaciones[] = $publicacion->getArray();
        }
        //print_r($publicaciones);

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $publicaciones;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->setToCache(true);
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch(PublicacionException $e){
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        error_log("Error en BD - " . $e);

        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error en consulta de publicacion");
        $response->send();
        exit();
    }
}

/* ------------------------------------------- POST -------------------------------------------------- */
function savePublicacion() {
    try {
        $connection = DB::dbConnect();
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('Encabezado "Content type" no es JSON');
            $response->send();
            exit();
        }

        $postData = file_get_contents('php://input');
        //print("Intentas POSTEAR:"+$postData);
        //print_r($postData);
        
        if(!$json_data=json_decode($postData)) {
            $response=new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Cuerpo de la solicitud no es un JSON válido");
            $response->send();
            exit();
        }
        //print_r($json_data);

        if (!isset($json_data->nombre) || !isset($json_data->descripcion) || !isset($json_data->stock) || !isset($json_data->vendedor_id) || 
        !isset($json_data->fecha_alta) || !isset($json_data->precio) || !isset($json_data->vistas) || !isset($json_data->ventas) || !isset($json_data->categoria)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (!isset($json_data->nombre) ? $response->addMessage('El nombre es obligatorio') : false);
            (!isset($json_data->descripcion) ? $response->addMessage('El campo de descripcion es obligatorio') : false);
            (!isset($json_data->stock) ? $response->addMessage('El campo de stock es obligatorio') : false);
            (!isset($json_data->vendedor_id) ? $response->addMessage('El campo de vendedor_id es obligatorio') : false);
            (!isset($json_data->precio) ? $response->addMessage('El campo de precio es obligatorio') : false);
            (!isset($json_data->vistas) ? $response->addMessage('El campo de vistas es obligatorio') : false);
            (!isset($json_data->ventas) ? $response->addMessage('El campo de ventas es obligatorio') : false);
            (!isset($json_data->categoria) ? $response->addMessage('La categoría es obligatoria') : false);
            (!isset($json_data->imagen) ? $response->addMessage('El campo de imagen es obligatorio') : false);
            $response->send();
            exit();
        }
        
        $nombre = $json_data->nombre;
        $descripcion = $json_data->descripcion;
        $stock = $json_data->stock;
        $vendedor_id = $json_data->vendedor_id;
        $comprador_id = null;
        $fecha_alta = $json_data->fecha_alta;
        $precio = $json_data->precio;
        $vistas = $json_data->vistas;
        $ventas = $json_data->ventas;
        $categoria = $json_data->categoria;
        $imagen=file_get_contents('../images/'.$json_data->imagen);
        //$imagen=null;
        //$imagen = $json_data->imagen;

        $query = $connection->prepare('INSERT INTO publicaciones(
            nombre, descripcion, stock, vendedor_id, comprador_id, fecha_alta, precio, vistas, ventas,
            categoria, imagen) 
            VALUES (:nombre, :descripcion, :stock, :vendedor_id, :comprador_id, 
            :fecha_alta, :precio, :vistas, :ventas, :categoria, :imagen)');
            
        $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $query->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $query->bindParam(':stock', $stock, PDO::PARAM_INT);
        $query->bindParam(':vendedor_id', $vendedor_id, PDO::PARAM_INT);
        $query->bindParam(':fecha_alta', $fecha_alta, PDO::PARAM_STR);
        $query->bindParam(':comprador_id', $comprador_id, PDO::PARAM_INT);
        $query->bindParam(':precio', $precio, PDO::PARAM_STR);
        $query->bindParam(':vistas', $vistas, PDO::PARAM_INT);
        $query->bindParam(':ventas', $ventas, PDO::PARAM_INT);
        $query->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        //$imagen=null;
        $query->bindParam(':imagen', $imagen, PDO::PARAM_LOB);
        $query->execute();
        //print_r($query->errorInfo());
        
        
        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al CREAR la publicacion");
            $response->send();
            exit();
        }

        $ultimo_ID = $connection->lastInsertId();

        $query = $connection->prepare('SELECT id, nombre, descripcion, stock, vendedor_id, comprador_id, fecha_alta, precio, vistas, ventas, categoria, imagen 
        FROM publicaciones WHERE id = :id');
        $query->bindParam(':id', $ultimo_ID, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();
        //print_r($connection->errorInfo());
        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al obtener la publicación después de crearla");
            $response->send();
            exit();
        }

        //$publicaciones = array();
        $returnData=array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor_id'], $row['comprador_id'], $row['fecha_alta'], $row['precio'], $row['vistas'], $row['categoria'], null);
            $returnData[] = $publicacion->getArray();
        }

        /*$returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $publicaciones;*/

        $response = new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("Publicacion Creada");
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch (PublicacionException $e) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch (PDOException $e){
        error_log("Error en BD - " . $e);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error en crear publicación");
        $response->send();
        exit();
    }
}
/* ------------------------------------------- PATCH -------------------------------------------------- */
function editPublicacion() {
    try {
        $connection = DB::dbConnect();
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('Encabezado "Content type" no es JSON');
            $response->send();
            exit();
        }

        $patchData = file_get_contents('php://input');

        if (!$json_data = json_decode($patchData)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
            $response->send();
            exit();
        }

        $actualiza_comprador = false;
        $campos_query = "";

        if (isset($json_data->comprador_id)) {
            $actualiza_comprador = true;
            $campos_query .= "comprador_id = :comprador_id";
        }

        if ($actualiza_comprador === false) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("No hay campos para actualizar!!");
            $response->send();
            exit();
        }

        $id_publicacion = $json_data->id;

        $query = $connection->prepare('SELECT * FROM publicaciones WHERE id = :id');
        $query->bindParam(':id', $id_publicacion, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage("No se encontró la publicacion");
            $response->send();
            exit();
        }

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor_id'], $row['comprador_id'], $row['fecha_alta'], $row['precio'], $row['vistas'], $row['categoria'], null);
        }

        $cadena_query = 'UPDATE publicaciones SET ' . $campos_query . ' WHERE id = :id';
        $query = $connection->prepare($cadena_query);

        if($actualiza_comprador === true) {
            $publicacion->setComprador($json_data->comprador_id);
            $up_comprador = $publicacion->getComprador();
            $query->bindParam(':comprador_id', $up_comprador, PDO::PARAM_STR);
        }

        $query->bindParam(':id', $id_publicacion, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al actualizar la publicacion");
            $response->send();
            exit();
        }

        $query = $connection->prepare('SELECT * FROM publicaciones WHERE id = :id');
        $query->bindParam(':id', $id_publicacion, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage("No se encontró la publicacion después de actulizar");
            $response->send();
            exit();
        }

        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor_id'], $row['comprador_id'], $row['fecha_alta'], $row['precio'], $row['vistas'], $row['categoria'], null);
            $publicaciones[] = $publicacion->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $publicaciones;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Publicacion actualizada");
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch(TareaException $e) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        error_log("Error en BD - " . $e);

        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error en BD al actualizar la publicacion");
        $response->send();
        exit();
    }
}
?>