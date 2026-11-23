<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="assets/css/Style.css">
    
</head>
<body>
   <div class="container">
        <h1>Cadastro</h1>
        <?php if (!empty($erro)): ?>
      <p style="color: red; font-weight: bold;"><?php echo $erro; ?></p>
      <?php endif; ?>
        <form method="post" class="formgrupo" id="formCadastro">
 <input type="hidden" name="csrf" value="<?= html(tokenCsrf()) ?>">
 <label for="nome">Nome completo</label>
 <input id="nome" name="nome" required minlength="3" maxlength="150">
 <label for="email">E-mail</label>
 <input id="email" name="email" type="email" required>
 <label for="senha">Senha</label>
 <input id="senha" name="senha" type="password" minlength="10" required>
 <label for="confirmar_senha">Confirme a senha</label>
 <input id="confirmar_senha" name="confirmar_senha" type="password" required>
 <button type="submit">Criar conta</button>
</form>

    </div>
    <button id="voltarTopo" class="voltar-topo" type="button">Topo</button>
</body>
</html>