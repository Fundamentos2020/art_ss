<?php
class Publicacion {
    public $_id;
    public $_nombre;
    public $_descripcion;
    public $_stock;
    public $_vendedor_id;
    public $_comprador_id;
    public $_fecha_alta;
    public $_precio;
    public $_vistas;
    public $_ventas;
    public $_categoria;
    public $_imagen;

    public function __construct($id, $nombre, $descripcion, $stock, $vendedor_id, $fecha_alta, $precio, $categoria, $imagen) {
        $this->_id = $id;
        $this->_nombre = $nombre;
        $this->_descripcion = $descripcion;
        $this->_stock = $stock;
        $this->_vendedor_id = $vendedor_id;
        $this->_fecha_alta = $fecha_alta;
        $this->_precio = $precio;
        $this->_categoria = $categoria;
        $this->_imagen= $imagen;
    }
}
?>