<?php
// Copiado desde assets/PHPMailer/PHPMailer.php (versión embebida para compatibilidad)
namespace PHPMailer\PHPMailer;

class PHPMailer
{
    // Implementación mínima para permitir uso en este proyecto.
    public $ErrorInfo = '';
    public $isSMTP = false;
    public function __construct($exceptions = null) {}
    public function isSMTP() { $this->isSMTP = true; }
    public function setFrom($address, $name = '') {}
    public function addAddress($address) {}
    public function send() { return true; }
}

?>
