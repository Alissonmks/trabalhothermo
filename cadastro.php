
<?php
require_once 'includes/config.php';
$erro = "";
// Só processa quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$nome = $_POST['nome'];
$email = $_POST['email'];
$cpf = $_POST['cpf'];
$telefone = $_POST['telefone'];
$senha = $_POST['senha'];
// Nunca guardamos a senha pura: guardamos o hash dela
$hash = password_hash($senha, PASSWORD_DEFAULT);
try {
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, cpf,telefone, senha_hash) VALUES (?, ?, ?, ?, ?)");


$stmt->execute([$nome, $email, $cpf, $telefone, $hash]);
header("Location: login.php?sucesso=1");
exit;} catch (Exception $e) {
// A tabela não aceita e-mail ou CPF repetidos (UNIQUE)
$erro = "E-mail ou CPF já cadastrados.";
}
}
?>

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
        <form class="formgrupo" method="POST" action="cadastro.php">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" placeholder="Digite seu nome" name="nome" required>

            <label for="email">Email:</label>
            <input type="email" id="email" placeholder="Digite seu email" name="email" required>
            
            <label for="phone">Telefone:</label>
            <input type="text" id="phone" placeholder="Digite seu telefone" name="telefone" required>
            
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" placeholder="Digite seu CPF" name="cpf" required>
        
             <label for="senha">Senha:</label>
            <input type="password" id="senha" placeholder="Digite sua senha" name="senha" required>

             <label>Confirmar</label>
             <input type="password" id="confirma_senha" placeholder="Confirme senha" name="confirma_senha" required>
            
              <a href="login.php">volta ao login</a>

            <button type="submit">Cadastrar</button>

        </form>
    </div>
    <button id="voltarTopo" class="voltar-topo" type="button">Topo</button>
</body>
</html>