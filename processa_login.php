<?php

// Inicia a sessгo
session_start();

// Requires
require_once("comuns/config.php");
require_once("comuns/bd.php");
require_once("comuns/funcoes.php");

// URL de retorno em caso de erro
$_url_retorno = SITE_URL . '/login.php';

// Os dados foram realmente recebidos?
if( empty($_POST['email']) || empty($_POST['senha']) ) {
    $_SESSION['erroLogin'] = 'Informe os dados.';
    irPara($_url_retorno);
}

// Escapa o e-mail recebido (se necessбrio, por precauзгo).
$email = mysqli_real_escape_string($_conexao, trim($_POST["email"]));

// O e-mail recebido nгo й vбlido?
if( !validaEmail($email) ) {
    $_SESSION['erroLogin'] = 'E-mail invбlido.';
    irPara($_url_retorno);
}

// Cria um hash SHA1 da senha
$senha = sha1($_POST['senha']);

// Й para lembrar o e-mail?
if( isset($_POST['lembrar-email']) ) {
    // Cria um cookie com o e-mail que expira em 20 dias.
    setcookie('usuarioEmail', $email, strtotime('+20days'));
} else {
    // Se nгo for, destrуi o Cookie antes criado (se existir)
    setcookie('usuarioEmail');
}

// Monta a Query
$query = sprintf("SELECT id, nome, email FROM usuarios WHERE email='%s' AND senha='%s'", $email, $senha);

// Executa a Query
$resultado = mysqli_query($_conexao, $query);

// Se nгo retornou nenhum registro, й porque o usuбrio nгo foi encontrado na tabela.
if( mysqli_num_rows($resultado)<=0 ) {
    $_SESSION['erroLogin'] = 'Usuбrio nгo encontrado no sistema.';
    irPara($_url_retorno);
}

// Obtйm os dados do usuбrio na forma de um array associativo
$usuario = mysqli_fetch_array($resultado, MYSQLI_ASSOC);

// Armazena na sessгo o id, nome e e-mail do usuбrio.
$_SESSION['usuario_id'] = $usuario["id"];
$_SESSION['usuario_email'] = $usuario["email"];
$_SESSION['usuario_nome'] = $usuario["nome"];

// Cria a sessгo que identifica que o login foi realizado.
$_SESSION['logado'] = TRUE;

// Redireciona o usuбrio para a agenda.
irPara('index.php?secao=agenda');