<?php 

require_once('Usuarios.php');
require_once('Clientes.php');

class Vendedor extends Cliente{
    private $_publicaciones=array();

    public function __construct($id, $publicaciones) {
        $this->setID($id);
        $this->setPublicaciones($publicaciones);
    }

    public function getPublicacion($index) {
        return $this->_publicaciones[$index];
    }

    public function getPublicaciones() {
        return $this->_compras;
    }

    public function setPublicacion($publicacionID) {
        if ($publicacionID!==null && (!is_numeric($publicacionID) || $publicacionID<=0 || $publicacionID>=2147483647)) {
            throw new userException("Error en el ID de la publicacion");
        }
        $this->_publicaciones[]=$publicacionID;
    }

    public function setPublicaciones($publicacionesArr) {
        $this->_publicaciones=$publicacionesArr;
    }

    public function getArrayVe() {
        $vendedor=array();
        $vendedor['usuario_id']=$this->getID();
        $vendedor['cuenta_paypal']=$this->getPaypal();
        $vendedor['cuenta_tarjeta']=$this->getTarjeta();
        $vendedor['Publicaciones_id']=$this->getPublicaciones();
        return $vendedor;
    }
}

?>