<?php

// Inicia a sess�o
session_start();

// Requires
require_once("comuns/config.php");
require_once("comuns/bd.php");
require_once("comuns/funcoes.php");
require_once("comuns/seguro.php"); // Usu�rio est� logado?

// � edi��o ou inser��o?
$edicao = ( isset($_POST["id"]) ) ? TRUE : FALSE;

// URL de retorno em caso de erro
$_url_retorno = ( $edicao ) ? SITE_URL . "/index.php?secao=cadastro&id=" . (int) $_POST["id"] : SITE_URL . "/index.php?secao=cadastro";

// Os dados foram realmente recebidos?
if( empty($_POST['email']) || empty($_POST['nome']) || empty($_POST['celular']) ) {
    $_SESSION['erroCadastro'] = 'Informe os dados.';
    irPara($_url_retorno);
}

// Armazena os dados em vari�veis escapando-os
$nome = mysqli_real_escape_string($_conexao, $_POST['nome']);
$email = mysqli_real_escape_string($_conexao, $_POST['email']);
$celular = mysqli_real_escape_string($_conexao, $_POST['celular']);

// O nome deve ter no m�nimo 3 caracteres
if( strlen($nome)<3 ) {
    $_SESSION['erroCadastro'] = 'O nome deve ser maior que 3 caracteres.';
    irPara($_url_retorno);
}

// O e-mail recebido n�o � v�lido?
if( !validaEmail($email) ) {
    $_SESSION['erroCadastro'] = 'E-mail inv�lido.';
    irPara($_url_retorno);
}

// O n�mero do celular recebido n�o � v�lido?
if( !validaCelular($celular) ) {
    $_SESSION['erroCadastro'] = 'N�mero de celular inv�lido.';
    irPara($_url_retorno);
}

// Se for uma inser��o de contato
if( !$edicao ) {

    // Cadastra o contato na tabela de "contatos"
    $query = sprintf("INSERT INTO contatos(id_usuario, nome, email, celular) VALUES(%d, '%s', '%s', '%s')", $_SESSION["usuario_id"], $nome, $email, $celular);

    if( !mysqli_query($_conexao, $query) ) {
        $_SESSION['erroCadastro'] = 'Erro inesperado ao cadastrar o contato. Tente novamente.';
        irPara($_url_retorno);
    }

    // Armazena na vari�vel $id_contato o id do contato rec�m- inserido.
    $id_contato = mysqli_insert_id($_conexao);

}

// Se for edi��o, atualiza os dados.
if( $edicao ) {

    // Atualiza a vari�vel $id_contato com o ID do contato que est� sendo alterado.
    $id_contato = (int) $_POST["id"];

    // Monta o Array com os novos dados.
    $contato = array(
        $nome,                      // Nome
        $email,                     // E-mail
        $celular,                   // Celular
        $_SESSION["usuario_id"],    // Id do usu�rio logado
        $id_contato                 // Id do contato a ser alterado
    );

    // Monta a Query
    $query = vsprintf("
        UPDATE
            contatos
        SET
            nome='%s',
            email='%s',
            celular='%s'
        WHERE
            id_usuario=%d AND
            id=%d", $contato);

    // Executa a Query
    mysqli_query($_conexao, $query);

    // O registro foi realmente alterado?
    if( mysqli_affected_rows($_conexao)>0 ) {
        // Sucesso na atualiza��o, cria uma sess�o para identificar isso.
        $_SESSION['atualizacaoOk'] = 'Dados alterados com sucesso!';
    }

}

// Se uma foto foi selecionada, tenta up�-la.
if( !empty($_FILES['foto']['tmp_name']) || $_FILES['foto']['tmp_name']!='none' ) {
    uploadFoto($_FILES['foto'], $id_contato);
}

// Se for uma inser��o, volta para a index; se for uma edi��o, volta para a p�gina que edita o contato.
if( !$edicao ) {
    irPara(SITE_URL . "/index.php");
} else {
    irPara($_url_retorno);
}
