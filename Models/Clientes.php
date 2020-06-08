<?php 

require_once('Usuarios.php');

class Cliente extends Usuario {
    private $_paypal;
    private $_tarjeta;

    public function __construct($id, $paypal, $tarjeta) {
        $this->setID($id);
        $this->setPaypal($paypal);
        $this->setTarjeta($tarjeta);
    }

    public function getPaypal() {
        return $this->_paypal;
    }

    public function getTarjeta() {
        return $this->_tarjeta;
    }

    public function setPaypal($paypal) {
        if ($paypal!==null && (!is_numeric($paypal) || $paypal<=0 || $paypal>=2147483647 || $this->_paypal!==null)) {
            throw new userException("Error en cuenta de Paypal");
        }
        $this->_paypal=$paypal;
    }

    public function setTarjeta($tarjeta) {
        if ($tarjeta!==null && (!is_numeric($tarjeta) || $tarjeta<=0 || $tarjeta>=2147483647 || $this->_tarjeta!==null)) {
            throw new userException("Error en el numero de tarjeta de credito");
        }
        $this->_tarjeta=$tarjeta;
    }
}

?>