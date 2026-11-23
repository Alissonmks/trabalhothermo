<?php
declare(strict_types=1);
require_once __DIR__ . '/../../app/visual.php';
[$pdo, $usuario] = exigirAcesso(['ADMIN'], 'ADMINISTRAR_USUARIOS');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT,
['options' => ['min_range' => 1]]);
if (!$id) {
http_response_code(400);
exit('Identificador inválido.');
}
$consulta = $pdo->prepare(
'SELECT u.id, u.nome, u.email, u.status, p.codigo AS perfil'
. ' FROM usuarios u JOIN perfis p ON p.id = u.perfil_id WHERE u.id = ?'
);
$consulta->execute([$id]);
$alvo = $consulta->fetch();
if (!$alvo) {
http_response_code(404);
exit('Usuário não encontrado.');
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
validarCsrf();
try {
if (($_POST['confirmar'] ?? '') !== 'SIM') {
throw new InvalidArgumentException('Confirmação obrigatória.');
}
$pdo->beginTransaction();
// Mesmo bloqueio utilizado na rotina administrativa original.
$pdo->query("SELECT id FROM perfis WHERE codigo='ADMIN' FOR UPDATE")
->fetchColumn();
$sql = 'SELECT u.id, u.status, p.codigo AS perfil FROM usuarios u'
. ' JOIN perfis p ON p.id = u.perfil_id WHERE u.id = ? FOR UPDATE';
$bloqueio = $pdo->prepare($sql);
$bloqueio->execute([$id]);
$atual = $bloqueio->fetch();
if (!$atual) {
throw new InvalidArgumentException('Usuário não encontrado.');
}
if ((int) $id === (int) $usuario['id']) {
throw new InvalidArgumentException('Você não pode excluir a própria conta.');
}
if ($atual['status'] === 'BAIXA') {
throw new InvalidArgumentException('Este usuário já foi baixado.');
}
if ($atual['perfil'] === 'ADMIN' && $atual['status'] === 'ATIVO') {
$contagem = $pdo->query(
"SELECT COUNT(*) FROM usuarios u JOIN perfis p"
. " ON p.id = u.perfil_id WHERE p.codigo='ADMIN'"
. " AND u.status='ATIVO'"
);
if ((int) $contagem->fetchColumn() <= 1) {
throw new InvalidArgumentException(
'Não é permitido excluir o último administrador ativo.'
);
}
}
// EXCLUSÃO LÓGICA: preserva vínculos e o histórico comercial.
$baixa = $pdo->prepare(
"UPDATE usuarios SET status='BAIXA' WHERE id=? AND status<>'BAIXA'"
);
$baixa->execute([$id]);
if ($baixa->rowCount() !== 1) {
throw new RuntimeException('A baixa não foi confirmada.');
}
auditar($pdo, (int) $usuario['id'], 'BAIXAR_USUARIO',
'usuarios', (int) $id, 'SUCESSO', 'Baixa confirmada pelo administrador');
$pdo->commit();
irPara(urlPublica('admin/usuarios_guia.php?mensagem=baixado'));
} catch (InvalidArgumentException $e) {
if ($pdo->inTransaction()) {
$pdo->rollBack();
}
$erro = $e->getMessage();
try {
auditar($pdo, (int) $usuario['id'], 'BAIXAR_USUARIO',
'usuarios', (int) $id, 'NEGADO', 'Tentativa de baixa não autorizada');
} catch (Throwable $falhaAuditoria) {
error_log('Guia: falha de auditoria: ' . get_class($falhaAuditoria));
}
} catch (Throwable $e) {
if ($pdo->inTransaction()) {
$pdo->rollBack();
}
error_log('Guia: erro na baixa: ' . get_class($e));
$erro = 'Não foi possível concluir a exclusão.';
try {
auditar($pdo, (int) $usuario['id'], 'BAIXAR_USUARIO',
'usuarios', (int) $id, 'ERRO', 'Falha no processamento da baixa');
} catch (Throwable $falhaAuditoria) {
error_log('Guia: falha de auditoria: ' . get_class($falhaAuditoria));
}
}
}
cabecalhoPagina('Confirmar exclusão de usuário', $usuario);
?>

<section class="painel-secao">
<h2>Deseja realmente excluir este usuário?</h2>
<p>Nome: <strong><?= html($alvo['nome']) ?></strong></p>
<p>E-mail: <?= html($alvo['email']) ?></p>
<p>Perfil: <?= html($alvo['perfil']) ?></p>
<p>Situação atual: <?= html($alvo['status']) ?></p>
<p class="nota">
A exclusão é lógica: o cadastro permanece no banco, com status BAIXA.
</p>
<?php if ($erro !== ''): ?>
<p class="aviso aviso-erro" role="alert"><?= html($erro) ?></p>
<?php endif; ?>
<?php if ($alvo['status'] !== 'BAIXA'): ?>
<form method="post" action="?id=<?= html($id) ?>"
onsubmit="return confirm('Deseja realmente excluir este usuário?');">
<input type="hidden" name="csrf" value="<?= html(tokenCsrf()) ?>">
<input type="hidden" name="confirmar" value="SIM">
<button class="btn btn-principal" type="submit">
Confirmar exclusão
</button>
<a class="btn btn-secundario" href="usuarios_guia.php">
Cancelar
</a>
</form>
<?php else: ?>
<a class="btn btn-secundario" href="usuarios_guia.php">Voltar</a>
<?php endif; ?>
</section>
<?php rodapePagina(); ?>