<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../../login.php");
  exit;
}

require_once __DIR__ . '/../../app/conexao.php';
require_once __DIR__ . '/../../includes/funcoes.php';

$pdo = thermoConexao();

$busca     = trim($_GET['busca'] ?? '');
$categoria = $_GET['categoria'] ?? '';
$precoMin  = str_replace(',', '.', trim($_GET['preco_min'] ?? ''));
$precoMax  = str_replace(',', '.', trim($_GET['preco_max'] ?? ''));
$situacao  = $_GET['situacao'] ?? '';
$ativo     = $_GET['ativo'] ?? '';
$ordem     = $_GET['ordem'] ?? 'nome';

$condicoes  = [];
$parametros = [];

if ($busca !== '') {
  $condicoes[] = '(LOWER(nome) LIKE LOWER(:busca_nome) OR LOWER(codigo) LIKE LOWER(:busca_codigo))';
  $parametros[':busca_nome']   = '%' . $busca . '%';
  $parametros[':busca_codigo'] = '%' . $busca . '%';
}

if (in_array($categoria, lista_categorias(), true)) {
  $condicoes[] = 'categoria = :categoria';
  $parametros[':categoria'] = $categoria;
}

if ($precoMin !== '' && is_numeric($precoMin)) {
  $condicoes[] = 'preco_venda >= :preco_min';
  $parametros[':preco_min'] = $precoMin;
}

if ($precoMax !== '' && is_numeric($precoMax)) {
  $condicoes[] = 'preco_venda <= :preco_max';
  $parametros[':preco_max'] = $precoMax;
}

if ($situacao === 'zerado') {
  $condicoes[] = 'quantidade = 0';
} elseif ($situacao === 'repor') {
  $condicoes[] = 'quantidade < estoque_minimo';
} elseif ($situacao === 'ok') {
  $condicoes[] = 'quantidade >= estoque_minimo';
}

if ($ativo === '1' || $ativo === '0') {
  $condicoes[] = 'ativo = :ativo';
  $parametros[':ativo'] = (int) $ativo;
}

$ordens = [
  'nome'        => 'nome ASC',
  'preco_menor' => 'preco_venda ASC',
  'preco_maior' => 'preco_venda DESC',
  'quantidade'  => 'quantidade ASC',
  'recentes'    => 'data_cadastro DESC',
];
$ordemSql = $ordens[$ordem] ?? 'nome ASC';

$sql = 'SELECT id, codigo, nome, categoria, marca, unidade_medida,
               preco_custo, preco_venda, quantidade, estoque_minimo,
               fornecedor, data_validade, ativo
        FROM produtos';

if (count($condicoes) > 0) {
  $sql .= ' WHERE ' . implode(' AND ', $condicoes);
}
$sql .= ' ORDER BY ' . $ordemSql;

$produtos  = [];
$erroBanco = '';

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($parametros);
  $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
  $erroBanco = $e->getMessage();
}

$total = count($produtos);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thermo Admin - Consultar Produtos</title>
  <link rel="stylesheet" href="../../assets/css/Style.css">
</head>
<body>

<nav class="menu-container">
  <div class="menu-direito">
    <a href="../../index.php" class="logo">
      <img src="../../img/logo.png" alt="Logo Thermo">
    </a>
  </div>
  <ul class="menu-centro">
    <li><a href="cadastrar_produto.php">Cadastrar Produto</a></li>
    <li><a href="consulta.php">Consultar Estoque</a></li>
    <li><a href="../../produtos.php">Ver Loja</a></li>
    <li><a href="../../dashboard.php">Minha Conta</a></li>
  </ul>
  <div class="menu-esquerdo">
    <a href="../../logout.php" class="link-login">Sair</a>
  </div>
</nav>

<header class="topo">
  <h1>Consultar Produtos</h1>
  <p>Use os filtros para buscar itens no estoque</p>
</header>

<main class="container container-largo">
  <form action="consulta.php" method="get" id="formFiltro" class="cartao filtros">
    <div class="grade">
      <div class="campo campo-largo">
        <label for="busca">Nome ou código</label>
        <input type="text" id="busca" name="busca" value="<?= e($busca) ?>" placeholder="Ex.: Garrafa ou THR-001">
      </div>

      <div class="campo">
        <label for="categoria">Categoria</label>
        <select id="categoria" name="categoria">
          <option value="">Todas</option>
          <?php foreach (lista_categorias() as $cat): ?>
            <option value="<?= e($cat) ?>"<?= marcado($categoria, $cat) ?>><?= e($cat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="campo">
        <label for="situacao">Situação</label>
        <select id="situacao" name="situacao">
          <option value=""<?= marcado($situacao, '') ?>>Todas</option>
          <option value="repor"<?= marcado($situacao, 'repor') ?>>Abaixo do mínimo</option>
          <option value="zerado"<?= marcado($situacao, 'zerado') ?>>Zerado</option>
          <option value="ok"<?= marcado($situacao, 'ok') ?>>OK</option>
        </select>
      </div>
    </div>

    <div class="grade">
      <div class="campo">
        <label for="preco_min">Preço de (R$)</label>
        <input type="number" id="preco_min" name="preco_min" step="0.01" min="0" value="<?= e($precoMin) ?>" placeholder="0.00">
      </div>

      <div class="campo">
        <label for="preco_max">Preço até (R$)</label>
        <input type="number" id="preco_max" name="preco_max" step="0.01" min="0" value="<?= e($precoMax) ?>" placeholder="999.99">
      </div>

      <div class="campo">
        <label for="ativo">Status</label>
        <select id="ativo" name="ativo">
          <option value=""<?= marcado($ativo, '') ?>>Todos</option>
          <option value="1"<?= marcado($ativo, '1') ?>>Ativos</option>
          <option value="0"<?= marcado($ativo, '0') ?>>Inativos</option>
        </select>
      </div>

      <div class="campo">
        <label for="ordem">Ordenar por</label>
        <select id="ordem" name="ordem">
          <option value="nome"<?= marcado($ordem, 'nome') ?>>Nome (A-Z)</option>
          <option value="preco_menor"<?= marcado($ordem, 'preco_menor') ?>>Menor preço</option>
          <option value="preco_maior"<?= marcado($ordem, 'preco_maior') ?>>Maior preço</option>
          <option value="quantidade"<?= marcado($ordem, 'quantidade') ?>>Menor quantidade</option>
          <option value="recentes"<?= marcado($ordem, 'recentes') ?>>Mais recentes</option>
        </select>
      </div>
    </div>

    <div class="acoes">
      <button type="submit" class="botao">Filtrar</button>
      <button type="button" id="btnLimpar" class="botao botao-neutro">Limpar filtros</button>
      <a href="cadastrar_produto.php" class="botao botao-secundario">Novo produto</a>
    </div>
  </form>

  <section class="cartao">
    <?php if ($erroBanco !== ''): ?>
      <div class="alerta alerta-erro">
        <strong>Erro ao consultar o banco:</strong>
        <pre class="detalhe"><?= e($erroBanco) ?></pre>
      </div>
    <?php endif; ?>

    <h2><?= $total ?> produto(s) encontrado(s)</h2>

    <?php if ($total === 0): ?>
      <p class="vazio">Nenhum produto encontrado.</p>
    <?php else: ?>
      <div class="tabela-rolagem">
        <table class="tabela" id="tabelaProdutos">
          <thead>
            <tr>
              <th>Código</th>
              <th>Nome</th>
              <th>Categoria</th>
              <th>Marca</th>
              <th>Un.</th>
              <th class="num">Custo</th>
              <th class="num">Venda</th>
              <th class="num">Qtd.</th>
              <th class="num">Mín.</th>
              <th>Validade</th>
              <th>Situação</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($produtos as $p): ?>
              <?php
                if ((int) $p['quantidade'] === 0) {
                  $classe = 'linha-zerado';
                  $situacaoTexto = 'Zerado';
                } elseif ((int) $p['quantidade'] < (int) $p['estoque_minimo']) {
                  $classe = 'linha-repor';
                  $situacaoTexto = 'Repor';
                } else {
                  $classe = '';
                  $situacaoTexto = 'OK';
                }

                if ((int) $p['ativo'] === 0) {
                  $classe .= ' linha-inativo';
                  $situacaoTexto .= ' (inativo)';
                }
              ?>
              <tr class="<?= $classe ?>">
                <td><?= e($p['codigo']) ?></td>
                <td><?= e($p['nome']) ?></td>
                <td><?= e($p['categoria']) ?></td>
                <td><?= e($p['marca'] ?? '—') ?></td>
                <td><?= e($p['unidade_medida']) ?></td>
                <td class="num"><?= moeda($p['preco_custo']) ?></td>
                <td class="num"><?= moeda($p['preco_venda']) ?></td>
                <td class="num"><?= (int) $p['quantidade'] ?></td>
                <td class="num"><?= (int) $p['estoque_minimo'] ?></td>
                <td><?= e(data_br($p['data_validade'])) ?></td>
                <td><?= $situacaoTexto ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>

<footer class="rodape">
  ETE PORTO DIGITAL — THERMO ADMIN
</footer>

<script src="../../assets/js/consulta.js"></script>
</body>
</html>


