<?php 

class userException extends Exception {}

class Usuario {
    private $_id;
    private $_nombre;
    private $_rolId;
    private $_pais;
    private $_fechaAlta;
    private $_calle;
    private $_colonia;
    private $_numeroEx;
    private $_CP;
    private $_estado;
    private $_status;
    private $_email;
    private $_password;
    private $_foto;

    public function __construct($id, $nombre, $rolId, $pais, $fechaAlta, $calle, $colonia, $numeroEx, $CP, $estado, $status, $email, $password, $foto) {
        $this->setID($id);
        $this->setNombre($nombre);
        $this->setRol($rolId);
        $this->setPais($pais);
        $this->setFecha($fechaAlta);
        $this->setCalle($calle);
        $this->setColonia($colonia);
        $this->setNumero($numeroEx);
        $this->setCP($CP);
        $this->setEstado($estado);
        $this->setStatus($status);
        $this->setemail($email);
        $this->setCont($password);
        $this->setFoto($foto);
    }

    public function getID() {
        return $this->_id;
    }

    public function getNombre() {
        return $this->_nombre;
    }

    public function getRol() {
        return $this->_rolId;
    }

    public function getPais() {
        return $this->_pais;
    }

    public function getFecha() {
        return $this->_fechaAlta;
    }

    public function getCalle() {
        return $this->_calle;
    }

    public function getColonia() {
        return $this->_colonia;
    }

    public function getNumero() {
        return $this->_numero;
    }

    public function getCP() {
        return $this->_CP;
    }

    public function getEstado() {
        return $this->_estado;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getCont() {
        return $this->_password;
    }

    public function getFoto() {
        return $this->_foto;
    }

    public function setID($id) {
        if ($id!==null && (!is_numeric($id) || $id<=0 || $id>=2147483647 || $this->_id!==null)) {
            throw new userException("Error en ID del usaurio");
        }
        $this->_id=$id;
    }

    public function setNombre($nombre) {
        if ($nombre===null || strlen($nombre)>30 || strlen($nombre)<1) {
            throw new userException("Error en el nombre del usuario");
        }
        $this->_nombre=$nombre;
    }

    public function setRol($rolId) {
        if ($rolId!==null && (!is_numeric($rolId) || $rolId<=0 || $rolId>=2147483647 || $this->_rolId!==null)) {
            throw new userException("Error en ID del rol");
        }
        $this->_rolId=$rolId;
    }

    public function setPais($pais) {
        if ($pais!==null && strlen($pais)>20) {
            throw new userException("Error en nombre del pais");
        }
        $this->_pais=$pais;
    }

    public function setFecha($fechaAlta) {
        if ($fechaAlta!==null && date_format(date_create_from_format('Y-m-d H:i', $fechaAlta), 'Y-m-d H:i')!==$fechaAlta) {
            throw new userException("Error en fecha de alta");
        }
        $this->_fechaAlta=$fechaAlta;
    }

    public function setCalle($calle) {
        if ($calle!==null && strlen($calle)>50) {
            throw new userException("Error en la calle");
        }
        $this->_calle=$calle;
    }

    public function setColonia($colonia) {
        if ($colonia!==null && strlen($colonia)>50) {
            throw new userException("Error en la colonia");
        }
        $this->_colonia=$colonia;
    }

    public function setNumero($numeroEx) {
        if ($numeroEx!==null && (!is_numeric($numeroEx) || $numeroEx<=0 || $numeroEx>=2147483647 || $this->_numeroEx!==null)) {
            throw new userException("Error en el numero exterior");
        }
        $this->_numeroEx=$numeroEx;
    }

    public function setCP($CP) {
        if ($CP!==null && (!is_numeric($CP) || $CP<=0 || $CP>=2147483647)) {
            throw new userException("Error en el código postal");
        }
        $this->_CP=$CP;
    }

    public function setEstado($estado) {
        if ($estado!==null && strlen($estado)>20) {
            throw new userException("Error en el estado");
        }
        $this->_estado=$estado;
    }

    public function setStatus($status) {
        if (strtoupper($status)!=='ACTIVO' && strtoupper($status)!=='BAJA') {
            throw new rolException("Error en el status");
        }
        $this->_status=$status;
    }

    public function setEmail($email) {
        if ($email===null || strlen($email)>45 || strlen($email)<1 || !strpos($email, '@')) {
            throw new userException("Error en el correo electronico");
        }
        $this->_email=$email;
    }

    public function setCont($password) {
        if ($password===null || strlen($password)>45 || strlen($nombre)<1) {
            throw new userException("Error en la contraseña");
        }
        $this->_password=$password;
    }

    public function setFoto($foto) {
        $this->_foto=$foto;
    }

    public function getArray() {
        $usuario=array();
        $usuario['id']=$this->getID();
        $usuario['nombre']=$this->getNombre();
        $usuario['rol_id']=$this->getRol();
        $usuario['pais']=$this->getPais();
        $usuario['fecha_alta']=$this->getFecha();
        $usuario['calle']=$this->getCalle();
        $usuario['colonia']=$this->getColonia();
        $usuario['numero_exterior']=$this->getNumero();
        $usuario['codigo_postal']=$this->getCP();
        $usuario['estado']=$this->getEstado();
        $usuario['status']=$this->getStatus();
        $usuario['email']=$this->getEmail();
        $usuario['contraseña']=$this->getCont();
        $usuario['foto_perfil']=$this->getFoto();
        return $usuario;
    }
}

?>