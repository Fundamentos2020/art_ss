<?php
require_once('../Models/DB.php');
require_once('../Models/Response.php');
require_once('../Models/Pedido.php');

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
    if(array_key_exists("comprador_id", $_GET))
        getByCompradorID($_GET['comprador_id']);
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
    savePedido();
}
else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    editPedido();
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
//Obtiene el pedido por su ID
function getById($id) {
    if ($id == '' || !is_numeric($id)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El id de pedido no puede estar vacío y debe ser numérico");
        $response->send();
        exit();
    }

    try {
        $connection = DB::dbConnect();
        $query = $connection->prepare('SELECT * FROM pedidos WHERE id = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $pedidos = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $pedido = new Pedido($row['id'], $row['comprador_id'], $row['estatus'], $row['monto_total'], $row['forma_pago'], $row['fecha_pedido']);
            $pedidos[] = $pedido->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $pedidos;

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
        $response->addMessage("Error en consulta del pedido");
        $response->send();
        exit();
    }
}

/* Obtiene los pedidos por Comprador ID */
function getByCompradorID($comprador_id) {
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
        $query = $connection->prepare('SELECT * FROM pedidos WHERE comprador_id = :comprador_id');
        $query->bindParam(':comprador_id', $comprador_id, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $pedidos = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $pedido = new Pedido($row['id'], $row['comprador_id'], $row['estatus'], $row['monto_total'], $row['forma_pago'], $row['fecha_pedido']);
            $pedidos[] = $pedido->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $pedidos;

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
        $response->addMessage("Error en consulta del pedido por comprador id");
        $response->send();
        exit();
    }
}

/* ------------------------------------------- POST -------------------------------------------------- */
function savePedido() {
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
        

        if (!$json_data = json_decode($postData)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
            $response->send();
            exit();
        }
        //print_r($json_data);
        if (!isset($json_data->comprador_id) || !isset($json_data->monto_total) || !isset($json_data->forma_pago)){
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (!isset($json_data->comprador_id) ? $response->addMessage('El nombre es obligatorio') : false);
            (!isset($json_data->monto_total) ? $response->addMessage('El campo de monto total es obligatorio') : false);
            (!isset($json_data->forma_pago) ? $response->addMessage('El campo de forma de pago es obligatorio') : false);
            $response->send();
            exit();
        }

        $comprador_id = $json_data->comprador_id;
        $estatus = $json_data->estatus;
        $monto_total = $json_data->monto_total;
        $forma_pago = $json_data->forma_pago;
        $fecha_pedido = $json_data->fecha_pedido;

        // $query = $connection->prepare('INSERT INTO publicaciones(nombre, descripcion, stock, vendedor_id, comprador_id, fecha_alta, precio, vistas, 
        // ventas, categoria, imagen) VALUES(:nombre, :descripcion, :stock, :vendedor_id, :comprador_id, STR_TO_DATE(:fecha_alta, \'%Y-%m-%d %H:%i\'), 
        // :precio, :vistas, :ventas, :categoria, :imagen)');

        $query = $connection->prepare('INSERT INTO pedidos (comprador_id, estatus, monto_total, forma_pago, fecha_pedido) 
        VALUES (:comprador_id, :estatus, :monto_total, :forma_pago, :fecha_pedido)');
        $query->bindParam(':comprador_id', $comprador_id, PDO::PARAM_INT);
        $query->bindParam(':estatus', $estatus, PDO::PARAM_STR);
        $query->bindParam(':monto_total', $monto_total, PDO::PARAM_STR);
        $query->bindParam(':forma_pago', $forma_pago, PDO::PARAM_STR);
        $query->bindParam(':fecha_pedido', $fecha_pedido, PDO::PARAM_STR);
        $query->execute();
        //print_r($query->errorInfo());
        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al CREAR el pedido");
            $response->send();
            exit();
        }

        $ultimo_ID = $connection->lastInsertId();

        $query = $connection->prepare('SELECT id, comprador_id, estatus, monto_total, forma_pago, fecha_pedido FROM pedidos WHERE id = :id');
        $query->bindParam(':id', $ultimo_ID, PDO::PARAM_INT);
        //$query->bindParam(':usuario_id', $consulta_idUsuario, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al obtener el pedido después de crearlo");
            $response->send();
            exit();
        }

        $pedidos = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $pedido = new Pedido($row['id'], $row['comprador_id'], $row['estatus'], $row['monto_total'], $row['forma_pago'], $row['fecha_pedido']);
            $pedidos[] = $pedido->getArray();
        }

        $returnData = array();
        $returnData['total_registros'] = $rowCount;
        $returnData['res'] = $pedidos;

        $response = new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("Pedido Creado");
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
        $response->addMessage("Error en crear pedido");
        $response->send();
        exit();
    }
}

?>