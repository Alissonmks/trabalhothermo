<?php
session_start();
require_once 'includes/config.php';
// Página protegida
if (!isset($_SESSION['usuario_id'])) {
header("Location: login.php");
exit;
}
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Atualiza SOMENTE o telefone do usuário logado
$stmt = $pdo->prepare("UPDATE usuarios SET telefone = ? WHERE id = ?");
if ($stmt->execute([$_POST['telefone'], $_SESSION['usuario_id']])) {
// Mantém a sessão igual ao banco
$_SESSION['usuario_telefone'] = $_POST['telefone'];
$msg = "Dados atualizados!";
}
}
?>

<?php if ($msg) echo "<div class='alert' style='background:#d1fae5;
color:#065f46'>$msg</div>"; ?>
...
<input type="text" value="<?php echo
htmlspecialchars($_SESSION['usuario_nome']); ?>" disabled
style="background:#eee">
...
<input type="text" id="telefone" name="telefone" maxlength="15" value="<?php
echo htmlspecialchars($_SESSION['usuario_telefone']); ?>" required>