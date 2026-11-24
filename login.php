<?php
session_start();
require_once __DIR__ . '/app/conexao.php';
$pdo = thermoConexao();
$erro = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$_POST['email']]);
$u = $stmt->fetch();

if ($u && password_verify($_POST['senha'], $u['senha_hash'])) {

$_SESSION['usuario_id'] = $u['id'];
$_SESSION['usuario_nome'] = $u['nome'];
$_SESSION['usuario_email'] = $u['email'];
$_SESSION['usuario_cpf'] = $u['cpf'];
$_SESSION['usuario_telefone'] = $u['telefone'];
header("Location: dashboard.php");exit;
} else {
$erro = "E-mail ou senha incorretos.";
}
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="assets/css/Style.css">
    <script src="assets/js/script.js" defer></script>
    <script src="assets/js/formularios.js" defer></script>
</head>
<body>
  <div class="container">
        <h1>Thermo Login</h1>
        <h2>login</h2>
        <?php if ($erro) echo "<div class='alert' style='background:#fee2e2;
        color:#ef4444'>$erro</div>"; ?><?php if (isset($_GET['sucesso'])) echo "<div class='alert'style='background:#d1fae5; color:#065f46'>Cadastro realizado!</div>"; ?>
        <form class="formgrupo" id="formlogin" method="POST" action="login.php">
            <label for="email">Email:</label>
            <input type="email" placeholder="Digite seu email" id="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" placeholder="Digite sua senha" id="senha" name="senha" required>
             <button type="button" id="mostrarSenha">Mostrar senha</button>
            <a href="cadastro.php">Não tem uma conta? Cadastre-se</a>
            <a href="index.php">voltar ao inicio</a>
        
            <button type="submit">Entrar</button>
          
        </form>
    </div>
</body> 

<button id="voltarTopo" class="voltar-topo" type="button">Topo</button>

</html>