<?php
require_once 'includes/config.php';
$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome']; 
    $email = $_POST['email']; 
    $cpf = $_POST['cpf']; 
    $telefone = $_POST['telefone']; 
    $data_nascimento = $_POST['data-nascimento']; 
    $senha = $_POST['senha'];
    $hash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO cliente (nome, email, cpf, telefone, senha, data_nascimento) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $cpf, $telefone, $hash, $data_nascimento]);
        header("Location: login.php?sucesso=1");
        exit;
    } catch (Exception $e) { 
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
        <form class="formulario-cadastro" method="POST" action="cadastro.php">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" placeholder="Digite seu nome" name="nome" required>

            <label for="email">Email:</label>
            <input type="email" id="email" placeholder="Digite seu email" name="email" required>
            
            <label for="phone">Telefone:</label>
            <input type="text" id="phone" placeholder="Digite seu telefone" name="telefone" required>
            
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" placeholder="Digite seu CPF" name="cpf" required>

            <label for="data_nascimento">data_nascimento:</label>
            <input type="date" id="data" placeholder="Digite seu telefone" name="data-nascimento" required>

             <label for="senha">Senha:</label>
            <input type="password" id="senha" placeholder="Digite sua senha" name="senha" required>
            
              <a href="login.php">volta ao login</a>

            <button type="submit">Cadastrar</button>

        </form>
    </div>
    <button id="voltarTopo" class="voltar-topo" type="button">Topo</button>
</body>
</html>