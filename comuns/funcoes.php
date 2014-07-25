<?php

/*
 * Valida um e-mail
 */
function validaEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/*
 * Redireciona
 */
function irPara($pagina) {
    header("Location: {$pagina}");
    exit;
}

/*
 * Valida n�mero de telefone/celular
 * Formatos aceitos: (99) 99999-9999 | (99) 9999-9999 | 99 9999-9999
 *                    99 9999-9999   | 999999-9999    | (99) 9999 9999
 */
function validaCelular($numero) {
    return preg_match('/^(\(?[0-9]{2}\)?[ ]?[0-9]{4,5}[ -]?[0-9]{4})$/', $numero);
}

/*
 * Fun��o que retorna uma classe 'active' para os links do menu
 */
function paginaAtual($pagina)
{
    // Se a p�gina informada for a p�gina que est� atualmente aberta
    if(
        ( isset($_GET['secao']) && $_GET['secao']==$pagina )
        || ( !isset($_GET['secao']) && $pagina=='home' )
    )
    {
        // Retorna o atributo 'class'
        return 'class="active"';
    }
    else
    {
        // N�o retorna nada
        return '';
    }
}

/*
 * Fun��o que recebe o id do contato e pesquisa na pasta de imagem se ele tem foto
 * Retorno: Caminho para a imagem (string).
 */
function contatoFoto($id_contato) {
    // Pesquisa a foto pelo hash md5 do id do contato
    $foto = glob(DIR_FOTO . md5($id_contato) . "*.{jpg,gif,png,jpeg}", GLOB_BRACE);

    // Se encontrou, $foto recebe um array, caso contr�rio, array vazio ou FALSE (em caso de erro)
    if( $foto ) {
        $caminho = SITE_URL . "/" . $foto[0];
    } else {
        $caminho = SITE_URL . "/" . FOTO_TEMP;
    }

    // Retorna o caminho
    return $caminho;
}

/*
 * Fun��o para o Upload da foto do contato
 */
function uploadFoto($arquivo, $novo_nome) {

    // Constante com o tamanho m�ximo permitido para upload
    define('MAX_UPLOAD_SIZE', '5000');

    // Vari�vel que armazenar� o resultado do Upload (A descri��o em caso de erro e TRUE em caso de sucesso.)
    $resultado = '';

    // Array com os tipos (mime types) aceitos para esse upload
    $tipos_aceitos = array('image/gif', 'image/jpg', 'image/jpeg', 'image/png', 'image/x-png', 'image/pjpeg');

    // Obt�m a extens�o do arquivo (gif, jpg, jpeg, png)
    $extensao = explode('.', $arquivo['name']);
    $extensao = strtolower(end($extensao));

    // Algum erro no Upload? Verificamos ent�o poss�veis erros.
    if(!empty($arquivo['error']))
    {
        switch($arquivo['error'])
        {
            case '1':
                $resultado = 'O arquivo excedeu o tamanho m�ximo permitido pelas configura��es do servidor.';
                break;
            case '2':
                $resultado = 'O arquivo excedeu o tamanho m�ximo permitido pelas configura��es do formul�rio.';
                break;
            case '3':
                $resultado = 'O arquivo foi parcialmente upado (incompleto). Upe novamente.';
                break;
            case '4':
                $resultado = 'Nenhum arquivo para upar.';
                break;
            case '6':
                $resultado = 'Faltando uma pasta tempor�ria.';
                break;
            case '7':
                $resultado = 'Falha ao escrever o arquivo no disco.';
                break;
            case '8':
                $resultado = 'Arquivo com extens�o n�o aceita.';
                break;
            default:
                $resultado = 'Houve um erro desconhecido. Tente novamente.';
        }
    }
    elseif( !is_uploaded_file($arquivo['tmp_name']) )
    {
        $resultado = 'O arquivo n�o foi upado.';
    }
    elseif( !in_array($arquivo['type'], $tipos_aceitos) )
    {
        $resultado = "O arquivo deve ser uma imagem GIF, JPG, JPEG ou PNG.";
    }
    elseif( filesize($arquivo['tmp_name']) > MAX_UPLOAD_SIZE*1024 )
    {
        $resultado = 'Imagem muito grande. N�o pode ter mais que 5MB.';
    }
    elseif( !getimagesize($arquivo['tmp_name']) )
    {
        $resultado = 'Esse arquivo n�o � uma imagem.';
    }
    elseif( !preg_match('/^[gif|jpg|png|jpeg]{3,4}$/', $extensao) ) {
        $resultado = 'Extens�o de arquivo inv�lida.';
    }
    else
    {
        // Novo nome do arquivo upado
        $nome = md5($novo_nome) . '.' . $extensao;

        // Move o arquivo que antes era tempor�rio para a pasta correta
        if( move_uploaded_file($arquivo["tmp_name"], DIR_FOTO . $nome) ) {
            $resultado = TRUE;
            //Remove imagens antigas que o usu�rio possa ter de outras extens�es
            removeImagemAntiga($nome, $novo_nome);
        } else {
            $resultado = 'Erro ao mover o arquivo para a pasta.';
        }
    }

    return $resultado;
}

/*
 * Fun��o para remover as imagens antigas
 */
function removeImagemAntiga($arquivoAtual, $id)
{
    $fotos = glob(DIR_FOTO . md5($id) . "*.{jpg,gif,png,jpeg}", GLOB_BRACE);
    foreach ($fotos as $foto) {
        if ($foto != DIR_FOTO . $arquivoAtual)
            unlink($foto);
    }
}
