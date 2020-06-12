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
            $pedido = new Pedido($row['id'], $row['comprador_id'], $row['status'], $row['monto_total'], $row['forma_pago'], $row['fecha_pedido']);
            $pedido[] = $pedido->getArray();
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
        $response->addMessage("Error en consulta de publicacion");
        $response->send();
        exit();
    }
}
?>