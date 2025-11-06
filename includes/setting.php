<?php
require_once __DIR__ . '/../config/client.php';

$config = require __DIR__ . '/../config/clients/' . CURRENT_CLIENT . '.php';

define("USUARIO", $config['USUARIO']);
define("PASSWORD", $config['PASSWORD']);
define("DATABASE", $config['DATABASE']);
define("SERVIDOR", $config['SERVIDOR']);
?>