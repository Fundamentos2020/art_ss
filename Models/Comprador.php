<?php 

require_once('Usuarios.php');
require_once('Clientes.php');

class Comprador extends Cliente{
    private $_compras=array();

    public function __construct($id, $compras) {
        $this->setID($id);
        $this->setCompra($compras);
    }

    public function getCompra($index) {
        return $this->_compras[$index];
    }

    public function getCompras() {
        return $this->_compras;
    }

    public function setCompra($compraID) {
        if ($compraID!==null && (!is_numeric($compraID) || $compraID<=0 || $compraID>=2147483647)) {
            throw new userException("Error en el ID de la compra");
        }
        $this->_compras[]=$compraID;
    }

    public function setCompras($comprasArr) {
        $this->_compras=$comprasArr;
    }

    public function getArrayCr() {
        $comprador=array();
        $comprador['usuario_id']=$this->getID();
        $comprador['cuenta_paypal']=$this->getPaypal();
        $comprador['cuenta_tarjeta']=$this->getTarjeta();
        $comprador['Compras_id']=$this->getCompras();
        return $comprador;
    }
}

?>