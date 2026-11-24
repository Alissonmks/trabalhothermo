<?php

define('MODO_AULA', true);

function lista_categorias()
{
    return array('Alimentos', 'Bebidas', 'Limpeza', 'Higiene', 'Papelaria', 'Outros');
}

function lista_unidades()
{
    return array(
        'UN'  => 'UN - Unidade',
        'KG'  => 'KG - Quilograma',
        'L'   => 'L - Litro',
        'CX'  => 'CX - Caixa',
        'PCT' => 'PCT - Pacote',
    );
}
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function contar_letras($texto)
{
    $total = preg_match_all('/./us', (string) $texto);
    
    return ($total === false) ? strlen($texto) : $total;
}

function marcado($valorAtual, $valorOpcao)
{
    return ((string) $valorAtual === (string) $valorOpcao) ? ' selected' : '';
}

function moeda($valor)
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function data_br($dataDoBanco)
{
    if ($dataDoBanco === null || $dataDoBanco === '') {
        return '—';
    }
    
    $partes = explode('-', substr($dataDoBanco, 0, 10));
    return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
}

function tela_de_erro($titulo, $dicas, $detalhe)
{
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
       . '<title>Mini Estoque - Erro</title><link rel="stylesheet" href="css/estilo.css"></head><body>'
       . '<header class="topo"><h1>Mini Estoque</h1></header><main class="container">'
       . '<div class="alerta alerta-erro"><strong>' . e($titulo) . '</strong></div>'
       . '<section class="cartao"><h2>O que verificar</h2><ol>';
    foreach ($dicas as $dica) {
        echo '<li>' . $dica . '</li>'; 
    }
    echo '</ol><p class="legenda">Detalhe técnico:</p><pre class="detalhe">' . e($detalhe) . '</pre>'
       . '<p><a class="botao botao-neutro" href="diagnostico.php">Abrir o diagnóstico</a> '
       . '<a class="botao" href="index.php">Voltar ao início</a></p>'
       . '</section></main></body></html>';
    exit;
}
