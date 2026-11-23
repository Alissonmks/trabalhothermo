<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['usuario_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, telefone = ? WHERE id = ?");
        if ($stmt->execute([$_POST['nome'], $_POST['telefone'], $_SESSION['usuario_id']])) {
        $_SESSION['usuario_nome'] = $_POST['nome'];
        $_SESSION['usuario_telefone'] = $_POST['telefone'] ;
        $msg = "Dados atualizados!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil - Sistema</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>
        <?php if($msg) echo "<div class='alert' style='background:#d1fae5; color:#065f46'>$msg</div>"; ?>
        <form method="POST" class="formgrupo">
            <label>Nome</label><input type="text" name="nome" value="<?php echo htmlspecialchars ($_SESSION['usuario_nome']); ?>">
            <label>Telefone</label><input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars ($_SESSION['usuario_telefone']); ?>" required>
            <button class="btn-principal" type="submit">Salvar Alterações</button>
               <div class="link-action"><a href="dashboard.php">Voltar ao Painel</a></div>
        </form>
        
    </div>
    <script src="script.js"></script>
</body>
</html>