<?php
session_start();
// Página protegida: sem login, volta para a tela de login
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }
// Mascara o e-mail: "ma*****@etepd.com"
$email = $_SESSION['usuario_email'];
$email_mascarado = substr($email, 0, 2) . "*****" . strstr($email, "@");
// Mascara o CPF: "123.***.***-09"
$cpf = $_SESSION['usuario_cpf'];
$cpf_mascarado = substr($cpf, 0, 4) . "***.***-" . substr($cpf, -2);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="menu-container">
   <div class="menu-direito">
  <a href="index.html" class="logo">
    <img src="img/logo.png" alt="Logo Thermo">
  </a>
</div>
  <ul class="menu-centro">
    <li><a href="index.php">Início</a></li>
    <li class="tem-submenu">
      <a href="produtos.php">produtos</a>
      
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="sobre.php">Sobre</a></li>
    <li><a href="contato.php">Contato</a></li>
     <li><a href="perfil.php">perfil</a></li>
    <li> <button id="btn-tema" type="button">☀️</button></li>
    </ul>
  <div class="menu-esquerdo">
    <a href="logout.php" class="link-login">Sair / Logout</a>
    <a href="login.php" class="link-login">Entrar / Login</a>
  </div>
</nav>
<div class="container">
  <h2>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></h2>
<p class="dashboard"><strong>E-mail:</strong> <?php echo htmlspecialchars($email_mascarado); ?>
</p>
<p class="dashboard"><strong>CPF:</strong> <?php echo htmlspecialchars($cpf_mascarado); ?></p>
<p class="dashboard"><strong>Tel:</strong> <?php echo
htmlspecialchars($_SESSION['usuario_telefone']); ?></p>
</div>