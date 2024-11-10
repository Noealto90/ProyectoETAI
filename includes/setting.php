<?php
if (!defined('USUARIO')) {
    define('USUARIO', 'postgres'); //En postgres diria que este es el por defecto
}

if (!defined('PASSWORD')) {
    define('PASSWORD', 'de2002'); //La contraseña que tienen en su postgresql
}

if (!defined('DATABASE')) {
    define('DATABASE', 'administracion'); //Nombre de su base de datos
}

if (!defined('SERVIDOR')) {
    define('SERVIDOR', 'localhost'); //Se deja asi porque se corre local
}
?>