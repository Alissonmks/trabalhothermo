<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topo">
        <h1>➕ Cadastrar Produto</h1>
        <p>Preencha os dados e clique em <strong>Salvar</strong></p>
    </header>

    <main class="container">
        <a href="index.php" class="link-voltar">&larr; Voltar ao início</a>

        <form action="salvar.php" method="post" id="formCadastro" class="cartao" novalidate>
            <div id="caixaErros" class="alerta alerta-erro escondido"></div>

            <fieldset>
                <legend>Identificação</legend>

                <div class="grade">
                    <div class="campo">
                        <label for="codigo">Código *</label>
                        <input type="text" id="codigo" name="codigo" maxlength="20" placeholder="Ex.: ALI-003" required>
                    </div>

                    <div class="campo campo-largo">
                        <label for="nome">Nome do produto *</label>
                        <input type="text" id="nome" name="nome" maxlength="100" placeholder="Ex.: Macarrão Espaguete 500 g" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="3" placeholder="Detalhes do produto (opcional)"></textarea>
                </div>

                <div class="grade">
                    <div class="campo">
                        <label for="categoria">Categoria *</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">-- Selecione --</option>
                            <?php
                            foreach (lista_categorias() as $categoria) {
                                echo '<option value="' . e($categoria) . '">' . e($categoria) . '</option>' . PHP_EOL;
                            }
                            ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" maxlength="50">
                    </div>

                    <div class="campo">
                        <label for="unidade_medida">Unidade *</label>
                        <select id="unidade_medida" name="unidade_medida" required>
                            <?php
                            foreach (lista_unidades() as $sigla => $texto) {
                                echo '<option value="' . e($sigla) . '"' . marcado($sigla, 'UN') . '>' . e($texto) . '</option>' . PHP_EOL;
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Preços e estoque</legend>

                <div class="grade">
                    <div class="campo">
                        <label for="preco_custo">Preço de custo (R$) *</label>
                        <input type="number" id="preco_custo" name="preco_custo" step="0.01" min="0" placeholder="0.00" required>
                    </div>

                    <div class="campo">
                        <label for="preco_venda">Preço de venda (R$) *</label>
                        <input type="number" id="preco_venda" name="preco_venda" step="0.01" min="0" placeholder="0.00" required>
                    </div>

                    <div class="campo">
                        <label for="margem">Margem de lucro</label>
                        <input type="text" id="margem" readonly placeholder="calculada automaticamente">
                    </div>
                </div>

                <div class="grade">
                    <div class="campo">
                        <label for="quantidade">Quantidade em estoque *</label>
                        <input type="number" id="quantidade" name="quantidade" step="1" min="0" value="0" required>
                    </div>

                    <div class="campo">
                        <label for="estoque_minimo">Estoque mínimo *</label>
                        <input type="number" id="estoque_minimo" name="estoque_minimo" step="1" min="0" value="0" required>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Outras informações</legend>

                <div class="grade">
                    <div class="campo campo-largo">
                        <label for="fornecedor">Fornecedor</label>
                        <input type="text" id="fornecedor" name="fornecedor" maxlength="100">
                    </div>

                    <div class="campo">
                        <label for="data_validade">Data de validade</label>
                        <input type="date" id="data_validade" name="data_validade">
                    </div>
                </div>

                <div class="campo campo-check">
                    <input type="checkbox" id="ativo" name="ativo" value="1" checked>
                    <label for="ativo">Produto ativo (disponível para venda)</label>
                </div>
            </fieldset>

            <div class="acoes">
                <button type="submit" class="botao">💾 Salvar</button>
                <button type="reset" class="botao botao-neutro">Limpar</button>
            </div>
        </form>
    </main>

    <footer class="rodape">
        ETE PORTO DIGITAL THERMO 
    </footer>

       <script src="assets/js/cadastro.js"></script>
</body>
</html>