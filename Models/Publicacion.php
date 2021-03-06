<?php 

class publicateException extends Exception {}

class Publicacion {
    private $_id;
    private $_nombre;
    private $_descripcion;
    private $_stock;
    private $_vendedor;
    private $_comprador;
    private $_fecha;
    private $_precio;
    private $_vistas;
    private $_categoria;
    private $_imagen;
    public $_nomComprador;
    private $_nomVendedor;

    public function __construct($id, $nombre, $descripcion, $stock, $vendedor, $comprador, $fecha, $precio, $vistas, $categoria) {
        $this->setID($id);
        $this->setNombre($nombre);
        $this->setDescripcion($descripcion);
        $this->setStock($stock);
        $this->setVendedor($vendedor);
        $this->setComprador($comprador);
        $this->setFecha($fecha);
        $this->setPrecio($precio);
        $this->setVistas($vistas);
        $this->setCategoria($categoria);
        $this->setNombreComprador(null);
        $this->setNombreVendedor(null);
    }

    public function getID() {
        return $this->_id;
    }

    public function getNombre() {
        return $this->_nombre;
    }

    public function getDescripcion() {
        return $this->_descripcion;
    }

    public function getStock() {
        return $this->_stock;
    }

    public function getVendedor() {
        return $this->_vendedor;
    }

    public function getComprador() {
        return $this->_comprador;
    }

    public function getFecha() {
        return $this->_fecha;
    }

    public function getPrecio() {
        return $this->_precio;
    }

    public function getVistas() {
        return $this->_vistas;
    }

    public function getVentas() {
        return $this->_vistas;
    }

    public function getCategoria() {
        return $this->_categoria;
    }

    public function getImagen() {
        return $this->_imagen;
    }

    public function setID($id) {
        if ($id!==null && (!is_numeric($id) || $id<=0 || $id>=2147483647 || $this->_id!==null)) {
            throw new publicateException("Error en ID de la publicacion");
        }
        $this->_id=$id;
    }

    public function setNombre($nombre) {
        if ($nombre===null || strlen($nombre)>50 || strlen($nombre)<1) {
            throw new publicateException("Error en el nombre de la publicacion");
        }
        $this->_nombre=$nombre;
    }

    public function setDescripcion($descripcion) {
        if ($descripcion!==null && strlen($descripcion)>150) {
            throw new publicateException("Error la descripcion de la publicacion");
        }
        $this->_descripcion=$descripcion;
    }

    public function setStock($stock) {
        if ($stock!==null && (!is_numeric($stock) || $stock<=0 || $stock>=2147483647)) {
            throw new publicateException("Error en cantidad de stock de la publicacion");
        }
        $this->_stock=$stock;
    }
    
    public function setVendedor($vendedorId) {
        if ($vendedorId!==null && (!is_numeric($vendedorId) || $vendedorId<=0 || $vendedorId>=2147483647 || $this->_vendedor!==null)) {
            throw new publicateException("Error en ID del vendedor");
        }
        $this->_vendedor=$vendedorId;
    }

    public function setComprador($compradorId) {
        //print($compradorId);
        if(!$compradorId === NULL) {
            if ((!is_numeric($compradorId) || $compradorId<=0 || $compradorId>=2147483647 || $this->_comprador!==null)) {
                throw new publicateException("Error en ID del comprador");
            }
        }
        $this->_comprador=$compradorId;
    }

    public function setFecha($fechaAlta) {
        //print($fechaAlta);
        if ($fechaAlta!==null && date_format(date_create_from_format('Y-m-d H:i:s', $fechaAlta), 'Y-m-d H:i:s')!==$fechaAlta) {
            throw new publicateException("Error en fecha de alta de la publicacion");
        }
        $this->_fecha=$fechaAlta;
    }

    public function setPrecio($precio) {
        //print($precio);
        if ($precio!==null && (!is_numeric($precio) || $precio<=0 || $precio>=2147483647)) {
            throw new publicateException("Error en el precio");
        }
        $this->_precio=$precio;
    }

    public function setVistas($vistas) {
        if ($vistas!==null && (!is_numeric($vistas) || $vistas<0 || $vistas>=2147483647)) {
            throw new publicateException("Error en las visitas");
        }
        $this->_vistas=$vistas;
    }

    public function setVentas($ventas) {
        if ($ventas!==null && (!is_numeric($ventas) || $ventas<=0 || $ventas>=2147483647)) {
            throw new publicateException("Error en las ventas");
        }
        $this->_ventas=$ventas;
    }

    public function setCategoria($categoria) {
        if ($categoria!==null && strlen($categoria)>20) {
            throw new publicateException("Error en la categoria");
        }
        $this->_categoria=$categoria;
    }

    public function setImagen($foto) {
        $this->_imagen=$foto;
    }

    public function setNombreComprador($name) {
        $this->_nomComprador=$name;
    }

    public function setNombreVendedor($name) {
        $this->_nomVendedor=$name;
    }
    
    public function getNombreComprador() {
        return $this->_nomComprador;
    }

    public function getNombreVendedor() {
        return $this->_nomVendedor;
    }

    public function getArray() {
        $publicacion=array();
        $publicacion['id']=$this->getID();
        $publicacion['nombre']=$this->getNombre();
        $publicacion['descripcion']=$this->getDescripcion();
        $publicacion['stock']=$this->getStock();
        $publicacion['vendedor_id']=$this->getVendedor();
        $publicacion['comprador_id']=$this->getComprador();
        $publicacion['fecha_alta']=$this->getFecha();
        $publicacion['precio']=$this->getPrecio();
        $publicacion['vistas']=$this->getVistas();
        $publicacion['ventas']=$this->getVentas();
        $publicacion['categoria']=$this->getCategoria();
        $publicacion['imagen']=$this->getImagen();
        $publicacion['nombre_comprador']=$this->getNombreComprador();
        $publicacion['nombre_vendedor']=$this->getNombreVendedor();
        return $publicacion;
    }
}

?>