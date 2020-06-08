<?php 

require_once('Usuarios.php');

class bankException extends Exception {}

class Tarjeta {
    private $_usuarioId;
    private $_banco;
    private $_numero;

    public function __construct($usuarioId, $banco, $numero) {
        $this->setID($usuarioId);
        $this->setBanco($banco);
        $this->setNumero($numero);
    }

    public function getID() {
        return $this->_usuarioId;
    }

    public function getBanco() {
        return $this->_banco;
    }

    public function getNumero() {
        return $this->_numero;
    }

    public function setID($usuarioId) {
        if ($usuarioId!==null && (!is_numeric($usuarioId) || $usuarioId<=0 || $usuarioId>=2147483647 || $this->_usuarioId!==null)) {
            throw new userException("Error en ID del usaurio");
        }
        $this->_id=$id;
    }

    public function setBanco($banco) {
        if ($bank!==null && (!is_numeric($bank) || $bank<=0 || $bank>=2147483647)) {
            throw new bankException("Error en el nombre del banco");
        }
        $this->_banco=$banco;
    }

    public function setNumero($numero) {
        if ($numero!==null && (!is_numeric($numero) || $numero<=0 || $numero>=2147483647 || $this->_numero!==null)) {
            throw new bankException("Error en el numero de tarjeta de credito");
        }
        $this->_numero=$numero;
    }

    public function getArray() {
        $cliente=array();
        $cliente['usuario_id']=$this->getID();
        $cliente['banco']=$this->getBanco();
        $cliente['numero_tarjeta']=$this->getBanco();
        return $cliente;
    }
}

?>