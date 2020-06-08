<?php 

class rolException extends Exception {}

class Rol {
    private $_id;
    private $_tipo;

    public function __construct($id, $tipo) {
        $this->setID($id);
        $this->setTipo($tipo);
    }

    public function getID() {
        return $this->_id;
    }

    public function getTipo() {
        return $this->_titulo;
    }

    public function setID($id) {
        if ($id!==null && (!is_numeric($id) || $id<=0 || $id>=2147483647 || $this->_id!==null)) {
            throw new rolException("Error en ID del rol");
        }
        $this->_id=$id;
    }

    public function setTipo($tipo) {
        if (strtoupper($tipo)!=='ADMINISTRADOR' && strtoupper($tipo)!=='COMPRADOR' && strtoupper($tipo)!=='VENDEDOR' && strtoupper($tipo)!=='COMPRADOR-VENDEDOR') {
            throw new rolException("Error en el tipo de rol");
        }
        $this->_tipo=$tipo;
    }

    public function getArray() {
        $rol=array();
        $rol['id']=$this->getID();
        $rol['tipo']=$this->getTipo();
        return $rol;
    }
}

?>