<?php

    // Inicializa a sess�o
    session_start();

    // O usu�rio est� logado?
    require_once("comuns/seguro.php");

    // Carrega o arquivo de configura��o
    require_once("comuns/config.php");

    // Carrega o arquivo que inicializa a conex�o com o banco de dados
    require_once("comuns/bd.php");

    // Carrega as fun��es
    require_once("comuns/funcoes.php");

    // Logout?
    if( isset($_GET["acao"]) && $_GET["acao"]==="logout" ) {
        // Limpa a sess�o
        session_destroy();
        // Redireciona para a p�gina de login
        irPara("login.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Agenda Pessoal | TreinaWeb Cursos</title>
    <!-- Estilos CSS -->
    <link href="<?=SITE_URL;?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=SITE_URL;?>/css/agenda.css" rel="stylesheet">
  </head>

  <body>

    <div class="container" id="container-logo">
        <a href="<?=SITE_URL;?>">
          <img src="<?=SITE_URL;?>/img/logo-treinaweb.png" alt="TreinaWeb Cursos">
        </a>
    </div>

    <div class="container" id="container-agenda">

    <div class="masthead">
      <ul class="nav nav-pills pull-right">
        <li <?=paginaAtual('home');?>><a href="<?=SITE_URL;?>/index.php">Home</a></li>
        <li <?=paginaAtual('cadastro');?>><a href="<?=SITE_URL;?>/index.php?secao=cadastro">Cadastro</a></li>
        <li class="logout"><a href="<?=SITE_URL;?>/index.php?acao=logout">Sair</a></li>
      </ul>
        <h3>Agenda Pessoal do <span class="nome"><?=$_SESSION["usuario_nome"];?></span></h3>
    </div>

    <hr>

    <?php

        // Alguma p�gina foi informada para ser inclu�da?
        $secao = isset($_GET['secao']) ? $_GET['secao'] : FALSE;

        // A p�gina informada existe no array $_config["seguras"]?
        if( $secao!=FALSE && in_array($secao, $_config["seguras"]) )
        {
            // Caminho do arquivo PHP
            $pagina = "paginas/{$secao}.php";

            // O arquivo da p�gina informada existe?
            if(file_exists($pagina))
            {
                // Inclui a p�gina informada
                require_once($pagina);
            }
            else
            {
                // Inclui a p�gina padr�o
                require_once("paginas/agenda.php");
            }
        }
        else
        {
            // Se nenhuma p�gina v�lida foi informada, inclui a p�gina padr�o
            require_once("paginas/agenda.php");
        }

    ?>
    </div>

  </body>
</html>