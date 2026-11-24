<?php
/*
 * =====================================================================
 *  salvar.php - RECEBE O FORMULÁRIO E GRAVA NO BANCO
 * =====================================================================
 *  O PHP roda NO SERVIDOR. O usuário nunca vê este código:
 *  ele só vê o HTML que o PHP "imprime" como resposta.
 *
 *  Caminho da informação neste arquivo:
 *   1. Conferir se a página foi chamada pelo formulário (POST).
 *   2. Ler os dados de $_POST (um array com os campos do form).
 *   3. Limpar e validar cada dado (de novo! o JS pode ter sido burlado).
 *   4. Montar o INSERT com "marcadores" (:nome, :codigo...).
 *   5. Enviar ao MySQL com os valores separados (prepared statement).
 *   6. Mostrar ao usuário se deu certo ou errado.
 * =====================================================================
 */

// conexao.php traz a função conectar_ou_parar() e, junto, o funcoes.php
// (e(), lista_categorias(), lista_unidades(), contar_letras()...).
require_once __DIR__ . '/app/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

// ---------------------------------------------------------------------
// PASSO 1: A PÁGINA VEIO DE UM FORMULÁRIO COM method="post"?
// ---------------------------------------------------------------------
// $_SERVER['REQUEST_METHOD'] informa o método usado: 'GET' ou 'POST'.
// Se alguém digitar salvar.php direto na barra de endereço, o método
// será GET. Nesse caso, mandamos a pessoa de volta para o formulário.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php'); // redireciona o navegador
    exit;                              // e encerra este script
}

// ---------------------------------------------------------------------
// PASSO 2: LER CADA CAMPO DE $_POST
// ---------------------------------------------------------------------
// $_POST é um ARRAY ASSOCIATIVO criado automaticamente pelo PHP.
// A CHAVE é o name="" do input; o VALOR é o que o usuário digitou.
//    <input name="nome">  ->  $_POST['nome']
//
// O operador ?? significa: "use $_POST['x'] se existir; se não, use o
// valor da direita". trim() remove espaços extras do começo e do fim.
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
// (Na linha do $codigo usamos isset + operador ternário "? :" só para
//  mostrar a forma "antiga" de fazer o mesmo que o ?? faz nas outras.)

// CHECKBOX: se estava desmarcado, a chave 'ativo' NEM EXISTE em $_POST.
// isset() verifica se existe. Se existe -> 1 (ativo); se não -> 0.
$ativo = isset($_POST['ativo']) ? 1 : 0;

// Preço com vírgula? Trocamos por ponto ("12,90" -> "12.90"),
// porque o MySQL só entende ponto como separador decimal.
$preco_custo = str_replace(',', '.', $preco_custo);
$preco_venda = str_replace(',', '.', $preco_venda);

// ---------------------------------------------------------------------
// PASSO 3: VALIDAR (o PHP é a barreira de verdade)
// ---------------------------------------------------------------------
$erros = array(); // cada problema encontrado vira um item desta lista

if ($codigo === '' || contar_letras($codigo) > 20) {
    $erros[] = 'Código é obrigatório e deve ter até 20 caracteres.';
}
if (contar_letras($nome) < 3 || contar_letras($nome) > 100) {
    $erros[] = 'Nome deve ter entre 3 e 100 caracteres.';
}
// in_array(valor, lista) -> true se o valor estiver na lista oficial.
// Alguém poderia alterar o HTML pelo F12 e mandar "Categoria Hacker".
if (!in_array($categoria, lista_categorias(), true)) {
    $erros[] = 'Categoria inválida.';
}
// array_keys pega só as chaves (UN, KG, L, CX, PCT)
if (!in_array($unidade_medida, array_keys(lista_unidades()), true)) {
    $erros[] = 'Unidade de medida inválida.';
}
// is_numeric -> true se o texto representa um número ("12.90" sim, "abc" não)
if (!is_numeric($preco_custo) || $preco_custo < 0) {
    $erros[] = 'Preço de custo deve ser um número maior ou igual a zero.';
}
if (!is_numeric($preco_venda) || $preco_venda < 0) {
    $erros[] = 'Preço de venda deve ser um número maior ou igual a zero.';
}
// ctype_digit -> true se o texto tiver SÓ algarismos (0 a 9).
// Então "30" passa; "2.5", "-1" e "" não passam.
if (!ctype_digit($quantidade)) {
    $erros[] = 'Quantidade deve ser um número inteiro maior ou igual a zero.';
}
if (!ctype_digit($estoque_minimo)) {
    $erros[] = 'Estoque mínimo deve ser um número inteiro maior ou igual a zero.';
}
// A data é opcional; mas, se veio, precisa ser uma data real (AAAA-MM-DD).
if ($data_validade !== '') {
    $partes = explode('-', $data_validade); // "2026-12-31" -> ["2026","12","31"]
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

        // lastInsertId(): pergunta ao banco qual id ele acabou de gerar.
        $novoId  = $pdo->lastInsertId();
        $sucesso = true;

    } catch (PDOException $e) {
        $enviouSql = true;
        // Código 23000 = violação de integridade. Aqui: "codigo" repetido
        // (a coluna tem índice UNIQUE).
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
    <title>Mini Estoque - Resultado do Cadastro</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topo">
        <h1>💾 Resultado do Cadastro</h1>
    </header>

    <main class="container">

        <?php if ($sucesso): ?>
            <!-- Este bloco só é enviado ao navegador se $sucesso for true -->
            <div class="alerta alerta-sucesso">
                <strong>Produto cadastrado com sucesso!</strong><br>
                "<?= e($nome) ?>" foi gravado no banco com o <strong>id <?= e($novoId) ?></strong>.
            </div>
        <?php else: ?>
            <!-- Este bloco só aparece se houve erro -->
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
                <!-- history.back() volta ao formulário com os dados ainda preenchidos -->
                <a href="javascript:history.back()" class="botao">&larr; Corrigir dados</a>
            <?php endif; ?>
            <a href="cadastro.php" class="botao">➕ Cadastrar outro</a>
            <a href="consulta.php" class="botao botao-secundario">🔍 Ver na consulta</a>
            <a href="index.php" class="botao botao-neutro">Início</a>
        </div>

        <?php if (MODO_AULA): ?>
        <!-- RAIO-X (modo aula): o que aconteceu "por baixo dos panos" -->
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
        ETE Porto Digital &middot; Desenvolvimento de Sistemas &middot; Prof. Luiz Carlos José da Silva
    </footer>
</body>
</html>
