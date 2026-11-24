<?php

require_once __DIR__ . '/../../app/conexao.php';
require_once __DIR__ . '/../../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;                              
}

$codigo         = trim(isset($_POST['codigo']) ? $_POST['codigo'] : '');
$nome           = trim($_POST['nome'] ?? '');
$descricao      = trim($_POST['descricao'] ?? '');
$categoria      = trim($_POST['categoria'] ?? '');
$marca          = trim($_POST['marca'] ?? '');
$unidade_medida = trim($_POST['unidade_medida'] ?? 'UN');
$preco_custo    = trim($_POST['preco_custo'] ?? '');
$preco_venda    = trim($_POST['preco_venda'] ?? '');
$quantidade     = trim($_POST['quantidade'] ?? '');
$estoque_minimo = trim($_POST['estoque_minimo'] ?? '');
$fornecedor     = trim($_POST['fornecedor'] ?? '');
$data_validade  = trim($_POST['data_validade'] ?? '');

$ativo = isset($_POST['ativo']) ? 1 : 0;


$preco_custo = str_replace(',', '.', $preco_custo);
$preco_venda = str_replace(',', '.', $preco_venda);

$erros = array(); 

if ($codigo === '' || contar_letras($codigo) > 20) {
    $erros[] = 'Código é obrigatório e deve ter até 20 caracteres.';
}
if (contar_letras($nome) < 3 || contar_letras($nome) > 100) {
    $erros[] = 'Nome deve ter entre 3 e 100 caracteres.';
}

if (!in_array($categoria, lista_categorias(), true)) {
    $erros[] = 'Categoria inválida.';
}

if (!in_array($unidade_medida, array_keys(lista_unidades()), true)) {
    $erros[] = 'Unidade de medida inválida.';
}

if (!is_numeric($preco_custo) || $preco_custo < 0) {
    $erros[] = 'Preço de custo deve ser um número maior ou igual a zero.';
}
if (!is_numeric($preco_venda) || $preco_venda < 0) {
    $erros[] = 'Preço de venda deve ser um número maior ou igual a zero.';
}

if (!ctype_digit($quantidade)) {
    $erros[] = 'Quantidade deve ser um número inteiro maior ou igual a zero.';
}
if (!ctype_digit($estoque_minimo)) {
    $erros[] = 'Estoque mínimo deve ser um número inteiro maior ou igual a zero.';
}

if ($data_validade !== '') {
    $partes = explode('-', $data_validade);
    if (count($partes) !== 3 || !checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])) {
        $erros[] = 'Data de validade inválida.';
    }
}

$sql = 'INSERT INTO produtos
            (codigo, nome, descricao, categoria, marca, unidade_medida,
             preco_custo, preco_venda, quantidade, estoque_minimo,
             fornecedor, data_validade, ativo)
        VALUES
            (:codigo, :nome, :descricao, :categoria, :marca, :unidade_medida,
             :preco_custo, :preco_venda, :quantidade, :estoque_minimo,
             :fornecedor, :data_validade, :ativo)';

$valores = array(
    ':codigo'         => $codigo,
    ':nome'           => $nome,
    ':descricao'      => ($descricao !== '') ? $descricao : null,
    ':categoria'      => $categoria,
    ':marca'          => ($marca !== '') ? $marca : null,
    ':unidade_medida' => $unidade_medida,
    ':preco_custo'    => $preco_custo,
    ':preco_venda'    => $preco_venda,
    ':quantidade'     => (int) $quantidade,
    ':estoque_minimo' => (int) $estoque_minimo,
    ':fornecedor'     => ($fornecedor !== '') ? $fornecedor : null,
    ':data_validade'  => ($data_validade !== '') ? $data_validade : null,
    ':ativo'          => $ativo,
);

$sucesso   = false; 
$novoId    = null;  
$enviouSql = false;  

if (count($erros) === 0) {
    $pdo = thermoConexao();

    try {
        
        $comando = $pdo->prepare($sql);
        $comando->execute($valores);
        $enviouSql = true;

        $novoId  = $pdo->lastInsertId();
        $sucesso = true;

    } catch (PDOException $e) {
        $enviouSql = true;
     
        if ($e->getCode() == 23000) {
            $erros[] = "Já existe um produto com o código \"$codigo\". Use outro código.";
        } else {
            $erros[] = 'Erro ao gravar no banco: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Cadastro</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
    
</head>
<body>
    <header class="topo">
        <h1>💾 Resultado do Cadastro</h1>
    </header>
    
    <nav class="menu-container">
        <div class="menu-direito">
            <a href="../index.php" class="logo">
                <img src="../../img/logo.png" alt="Logo Thermo">
            </a>
        </div>
        <ul class="menu-centro">
            <li><a href="cadastrar_produto.php">Cadastrar Produto</a></li>
             <li><a href="consulta.php">Consultar Estoque</a></li>
             <li><a href="../../produtos.php">Ver Loja (Cliente)</a></li>
            <li><a href="../../dashboard.php">Minha Conta</a></li>
        </ul>
        <div class="menu-esquerdo">
            <a href="../../logout.php" class="link-login">Sair</a>
        </div>
    </nav>
    <main class="container">

        <?php if ($sucesso): ?>
     
            <div class="alerta alerta-sucesso">
                <strong>Produto cadastrado com sucesso!</strong><br>
                "<?= e($nome) ?>" foi gravado no banco com o <strong>id <?= e($novoId) ?></strong>.
            </div>
        <?php else: ?>
            <div class="alerta alerta-erro">
                <strong>Não foi possível cadastrar:</strong>
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="acoes">
            <?php if (!$sucesso): ?>
    
                <a href="javascript:history.back()" class="botao">&larr; Corrigir dados</a>
            <?php endif; ?>
            <a href="cadastro.php" class="botao">➕ Cadastrar outro</a>
            <a href="consulta.php" class="botao botao-secundario">🔍 Ver na consulta</a>
            <a href="index.php" class="botao botao-neutro">Início</a>
        </div>

        <?php if (MODO_AULA): ?>
   
        <section class="cartao raio-x">
            <h2>🔬 Raio-X da requisição (modo aula)</h2>

            <h3>1. O que chegou do navegador em <code>$_POST</code></h3>
            <p>Cada linha é um <code>name</code> do formulário e o valor digitado.</p>
            <pre><?= e(print_r($_POST, true)) ?></pre>

            <h3>2. Validação no PHP</h3>
            <pre><?= count($erros) === 0 || $enviouSql
                ? 'OK: todos os campos passaram na validação.'
                : e(count($erros) . " erro(s) encontrado(s). O SQL NÃO foi enviado ao banco.") ?></pre>

            <h3>3. O SQL (com marcadores)</h3>
            <pre><?= e($sql) ?></pre>

            <h3>4. Os valores que preencheram cada marcador</h3>
            <p>Campos opcionais vazios viraram <code>NULL</code>. O checkbox virou 0 ou 1.</p>
            <pre><?= e(var_export($valores, true)) ?></pre>

            <h3>5. Resposta do banco</h3>
            <pre><?php
                if ($sucesso) {
                    echo 'OK: 1 linha inserida. lastInsertId() = ' . e($novoId);
                } elseif ($enviouSql) {
                    echo 'O banco recusou o comando (veja a mensagem em vermelho acima).';
                } else {
                    echo 'Nada foi enviado ao banco.';
                }
            ?></pre>
        </section>
        <?php endif; ?>

    </main>

    <footer class="rodape">
        banco thermo resumo
    </footer>
</body>
</html>
