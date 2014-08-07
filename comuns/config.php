<?php

/*
 * Arquivo de configura��o
 */

/* Banco de dados */
$_config["bd"]["host"] = "localhost";
$_config["bd"]["usuario"] = "root";
$_config["bd"]["senha"] = "root";
$_config["bd"]["database"] = "agendapessoal";

/* P�ginas seguras */
$_config["seguras"] = array(
    "agenda",
    "cadastro"
);

/* Agenda */
define("SITE_URL", "http://localhost/agendapessoal");
define("DIR_FOTO", "img/contatos/");
define("FOTO_TEMP", "img/usuario.png");