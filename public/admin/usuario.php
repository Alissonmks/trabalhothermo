<?php
declare(strict_types=1);
require_once __DIR__ . '/../../app/usuarios.php';
require_once __DIR__ . '/../../app/visual.php';
[$pdo, $usuario] = exigirAcesso(['ADMIN'], 'ADMINISTRAR_USUARIOS');
$id = null;
if (isset($_GET['id'])) {
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT,
['options' => ['min_range' => 1]]);
if (!$id) {
http_response_code(400);
exit('Identificador inválido.');
}
}
$registro = [];
$erro = '';
if ($id !== null) {
$sql = 'SELECT u.nome, u.email, u.cpf, u.telefone, u.status,'
. ' p.codigo AS perfil FROM usuarios u'
. ' JOIN perfis p ON p.id = u.perfil_id WHERE u.id = ?';
$consulta = $pdo->prepare($sql);
$consulta->execute([$id]);
$registro = $consulta->fetch();
if (!$registro) {
http_response_code(404);
exit('Usuário não encontrado.');
}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
validarCsrf();
try {
salvarUsuarioAdmin($pdo, $_POST, (int) $usuario['id'], $id);
irPara(urlPublica('admin/usuarios_guia.php'));
} catch (InvalidArgumentException $e) {
$erro = $e->getMessage();
} catch (PDOException $e) {
error_log('Guia: erro PDO ao salvar: ' . $e->getCode());
$erro = $e->getCode() === '23000'
? 'E-mail ou CPF já cadastrado.' : 'Não foi possível salvar.';
} catch (Throwable $e) {
error_log('Guia: falha ao salvar: ' . get_class($e));
$erro = 'Não foi possível salvar.';
}
}
