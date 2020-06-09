<?php

require_once('../Models/DB.php');
require_once('../Models/Publicacion.php');
require_once('../Models/Response.php');

try {
    $connection = DB::getConnection();
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
    savePublicacion();
}
else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    editPublicacion();
}
else {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Método no permitido");
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
        $query = $connection->prepare('SELECT id FROM publicaciones WHERE id = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor'], $row['fecha'], $row['precio'], $row['precio'], $row['vistas'], $row['categoria'], $row['imagen']);
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
        $query = $connection->prepare('SELECT * FROM publicaciones WHERE vendedor_id = :vendedor_id');
        $query->bindParam(':vendedor_id', $vendedor_id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor'], $row['fecha'], $row['precio'], $row['precio'], $row['vistas'], $row['categoria'], $row['imagen']);
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
        $query = $connection->prepare('SELECT * FROM publicaciones WHERE comprador_id = :comprador_id');
        $query->bindParam(':comprador_id', $comprador_id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor'], $row['fecha'], $row['precio'], $row['precio'], $row['vistas'], $row['categoria'], $row['imagen']);
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
        $query = $connection->prepare('SELECT * FROM publicaciones WHERE categoria = :categoria');
        $query->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $publicaciones = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor'], $row['fecha'], $row['precio'], $row['precio'], $row['vistas'], $row['categoria'], $row['imagen']);
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

/* ------------------------------------------- POST -------------------------------------------------- */
function savePublicacion() {
    try {
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('Encabezado "Content type" no es JSON');
            $response->send();
            exit();
        }

        $postData = file_get_contents('php://input');

        if (!$json_data = json_decode($postData)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
            $response->send();
            exit();
        }

        if (!isset($json_data->nombre) || || !isset($json_data->descripcion) || !isset($json_data->stock) || 
        !isset($json_data->vendedor_id) || !isset($json_data->comprador_id) || 
        !isset($json_data->precio) || !isset($json_data->vistas) || !isset($json_data->ventas) ||
        !isset($json_data->categoria) || !isset($json_data->imagen)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (!isset($json_data->nombre) ? $response->addMessage('El nombre es obligatorio') : false);
            (!isset($json_data->descripcion) ? $response->addMessage('El campo de descripcion es obligatorio') : false);
            (!isset($json_data->stock) ? $response->addMessage('El campo de stock es obligatorio') : false);
            (!isset($json_data->vendedor_id) ? $response->addMessage('El campo de vendedor_id es obligatorio') : false);
            (!isset($json_data->comprador_id) ? $response->addMessage('El campo de comprador_id es obligatorio') : false);
            //Fecha
            (!isset($json_data->precio) ? $response->addMessage('El campo de precio es obligatorio') : false);
            (!isset($json_data->vistas) ? $response->addMessage('El campo de vistas es obligatorio') : false);
            (!isset($json_data->ventas) ? $response->addMessage('El campo de ventas es obligatorio') : false);
            (!isset($json_data->categoria) ? $response->addMessage('La categoría es obligatoria') : false);
            (!isset($json_data->imagen) ? $response->addMessage('El campo de imagen es obligatorio') : false);
            $response->send();
            exit();
        }

        $publicacion = new Publicacion(
            null, 
            $json_data->nombre,
            $json_data->descipcion,
            $json_data->stock,
            $json_data->vendedor_id,
            $json_data->comprador_id,
            //Fecha
            $json_data->precio,
            $json_data->vistas,
            $json_data->ventas,
            $json_data->categoria,
            $json_data->imagen
        );

        $nombre = $publicacion->getNombre();
        $descripcion = $publicacion->getDescipcion();
        $stock = $publicacion->getStock();
        $vendedor_id = $publicacion->getVendedor()
        $comprador_id = $publicacion->getComprador();
        //Fecha
        $precio = $publicacion->getPrecio();
        $vistas = $publicacion->getVistas();
        $ventas = $publicacion->getVentas();
        $categoria = $publicacion->getCategoria();
        $imagen = $publicacion->getImagen();

        $query = $connection->prepare('INSERT INTO publicaciones (nombre, descripcion, stock, vendedor_id, comprador_id, precio, vistas, ventas, categoria, imagen) VALUES (:nombre, :descripcion, :stock, :vendedor_id, :comprador_id, :precio, :vistas, :ventas, :categoria, :imagen)');
        $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $query->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $query->bindParam(':stock', $stock, PDO::PARAM_INT);
        $query->bindParam(':vendedor_id', $vendedor_id, PDO::PARAM_INT);
        $query->bindParam(':comprador_id', $comprador_id, PDO::PARAM_INT);
        $query->bindParam(':precio', $precio, PDO::PARAM_INT);
        $query->bindParam(':vistas', $vistas, PDO::PARAM_INT);
        $query->bindParam(':ventas', $ventas, PDO::PARAM_INT);
        $query->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        $query->bindParam(':imagen', $imagen, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al crear la publicacion");
            $response->send();
            exit();
        }

        $ultimo_ID = $connection->lastInsertId();

        $query = $connection->prepare('SELECT id, titulo, descripcion, DATE_FORMAT(fecha_limite, "%Y-%m-%d %H:%i") fecha_limite, completada, categoria_id FROM tareas WHERE id = :id AND usuario_id = :usuario_id');
        $query->bindParam(':id', $ultimo_ID, PDO::PARAM_INT);
        $query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al obtener tarea después de crearla");
            $response->send();
            exit();
        }

        $tareas = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publicacion = new Publicacion($row['id'], $row['nombre'], $row['descripcion'], $row['stock'], $row['vendedor'], $row['fecha'], $row['precio'], $row['precio'], $row['vistas'], $row['categoria'], $row['imagen']);
            $publicaciones[] = $publicacion->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $publicaciones;

        $response = new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("Publicacion creada");
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
        $response->addMessage("Error en creación de publicaciones");
        $response->send();
        exit();
    }
}
/* ------------------------------------------- PATCH -------------------------------------------------- */
function editPublicacion() {
    console.log("Editar Publicacion");
}
?>