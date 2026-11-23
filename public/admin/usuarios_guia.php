<?php
declare(strict_types=1);
require_once __DIR__ . '/../../app/visual.php';
[$pdo, $usuario] = exigirAcesso(['ADMIN'], 'ADMINISTRAR_USUARIOS');
$busca = trim((string) ($_GET['busca'] ?? ''));
$busca = mb_substr($busca, 0, 100);
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$limite = 10;
$offset = ($pagina - 1) * $limite;
$where = '';
$parametros = [];
if ($busca !== '') {
$where = ' WHERE u.nome LIKE :nome OR u.email LIKE :email';
$parametros = [
'nome' => '%' . $busca . '%',
'email' => '%' . $busca . '%',
];
}
try {
$contagem = $pdo->prepare('SELECT COUNT(*) FROM usuarios u' . $where);
$contagem->execute($parametros);
$total = (int) $contagem->fetchColumn();
$sql = 'SELECT u.id, u.nome, u.email, u.status, p.nome AS perfil'
. ' FROM usuarios u JOIN perfis p ON p.id = u.perfil_id'
. $where . ' ORDER BY u.id DESC LIMIT :limite OFFSET :offset';
$consulta = $pdo->prepare($sql);
foreach ($parametros as $chave => $valor) {
$consulta->bindValue(':' . $chave, $valor, PDO::PARAM_STR);
}
$consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
$consulta->bindValue(':offset', $offset, PDO::PARAM_INT);
$consulta->execute();
$usuarios = $consulta->fetchAll();
} catch (Throwable $e) {
error_log('Guia: falha ao consultar usuarios: ' . get_class($e));
falhaBanco();
}
cabecalhoPagina('Laboratorio - usuarios', $usuario);
?>
<section class="painel-secao">
<h2>Cadastro e gerenciamento de usuários</h2>
<?php if (($_GET['mensagem'] ?? '') === 'baixado'): ?>
<p class="aviso">Usuário colocado em situação BAIXA.</p>
<?php endif; ?>
<div class="painel-acoes">
<a class="btn btn-principal" href="usuario_guia.php">Novo usuário</a>
<a class="btn btn-secundario" href="usuarios.php">Voltar ao painel original</a>
</div>
<form method="get" class="filtros">
<label for="busca">Nome ou e-mail</label>
<input id="busca" name="busca" maxlength="100"
value="<?= html($busca) ?>">
<button class="btn btn-principal" type="submit">Pesquisar</button>
</form>
<div class="tabela-rolagem">
<table class="tabela-sistema">
<thead><tr>
<th>ID</th><th>Nome</th><th>E-mail</th>
<th>Perfil</th><th>Situação</th><th>Ações</th>
</tr></thead>
<tbody>
<?php foreach ($usuarios as $u): ?>
<tr>
<td><?= html($u['id']) ?></td>
<td><?= html($u['nome']) ?></td>
<td><?= html($u['email']) ?></td>
<td><?= html($u['perfil']) ?></td><td><?= html($u['status']) ?></td>
<td>

<a href="usuario_guia.php?id=<?= html($u['id']) ?>">Editar</a>

<?php if ($u['status'] !== 'BAIXA'

&& (int) $u['id'] !== (int) $usuario['id']): ?>
| <a href="excluir_usuario_guia.php?id=<?= html($u['id']) ?>">
Excluir
</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<p>Total: <?= html($total) ?> registro(s).</p>
<div class="paginacao">
<?php if ($pagina > 1): ?>
<a href="?pagina=<?= html($pagina - 1) ?>&amp;busca=<?= urlencode($busca) ?>">
Anterior
</a>
<?php endif; ?>
<span>Página <?= html($pagina) ?> de <?= html(max(1, (int) ceil($total / $limite))) ?></span>
<?php if ($pagina * $limite < $total): ?>
<a href="?pagina=<?= html($pagina + 1) ?>&amp;busca=<?= urlencode($busca) ?>">
Próxima
</a>
<?php endif; ?>
</div>
</section>
<?php rodapePagina(); ?>