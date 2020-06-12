<?php 

class pedidoException extends Exception {}

class Pedido {
    private $_id;
    private $_comprador_id;
    private $_status;
    private $_monto_total;
    private $_forma_pago;
    private $_fecha_pedido;

    public function __construct($id, $comprador_id, $status, $monto_total, $forma_pago, $fecha_pedido) {
        $this->setID($id);
        $this->setComprador($comprador_id);
        $this->setStatus($status);
        $this->setMontoTotal($monto_total);
        $this->setFormaPago($forma_pago);
        $this->setFecha($fecha_pedido);
    }

    public function getID() {
        return $this->_id;
    }

    public function getCompradorID() {
        return $this->_comprador_id;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function getMontoTotal() {
        return $this->_monto_total;
    }

    public function getFormaPago() {
        return $this->_forma_pago;
    }

    public function getFecha() {
        return $this->_fecha_pedido;
    }

    public function setID($id) {
        if ($id!==null && (!is_numeric($id) || $id<=0 || $id>=2147483647 || $this->_id!==null)) {
            throw new pedidoException("Error en ID del pedido");
        }
        $this->_id=$id;
    }

    public function setComprador($comprador_id) {
        if(!$comprador_id === NULL) {
            if ((!is_numeric($comprador_id) || $comprador_id<=0 || $comprador_id>=2147483647 || $this->_comprador_id!==null)) {
                throw new pedidoException("Error en ID del comprador");
            }
        }
        $this->_comprador_id=$comprador_id;
    }

    public function setStatus($status) {
        if ($status===null || strlen($status)>50 || strlen($status)<1) {
            throw new pedidoException("Error en el estatus de la publicacion");
        }
        $this->_status=$status;
    }

    public function setMontoTotal($monto_total) {
        if ($monto_total!==null && (!is_numeric($monto_total) || $monto_total<=0 || $monto_total>=2147483647)) {
            throw new pedidoException("Error en el monto total");
        }
        $this->_monto_total=$monto_total;
    }

    public function setFecha($fecha_pedido) {
        //print($fechaAlta);
        if ($fecha_pedido!==null && date_format(date_create_from_format('Y-m-d H:i:s', $fecha_pedido), 'Y-m-d H:i:s')!==$fecha_pedido) {
            throw new pedidoException("Error en fecha de alta de pedido");
        }
        $this->_fecha_pedido=$fecha_pedido;
    }

    public function setFormaPago($forma_pago) {
        if ($forma_pago===null || strlen($forma_pago)>50 || strlen($forma_pago)<1) {
            throw new pedidoException("Error en el nombre de la publicacion");
        }
        $this->_forma_pago=$forma_pago;
    }

    public function getArray() {
        $pedido=array();
        $pedido['id']=$this->getID();
        $pedido['comprador_id']=$this->getComprador();
        $pedido['fecha_pedido']=$this->getFecha();
        $pedido['monto_total']=$this->getMontoTotal();
        $pedido['forma_pago']=$this->getFormaPago();
        $pedido['status']=$this->getStatus();
        return $pedido;
    }
}
?>