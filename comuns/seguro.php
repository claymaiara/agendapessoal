<?php

/*
 * Verifica se o usu�rio est� logado. Se n�o estiver, redireciona-o para a p�gina de login
 */

if( !isset($_SESSION["logado"]) ) {
    session_destroy();
    header("Location: login.php");
    exit;
}
