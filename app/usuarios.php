<?php
declare(strict_types=1);
require_once __DIR__.'/autenticacao.php';
function normalizarTelefone(string $texto): string { return trim(substr($texto,0,25)); }
function normalizarCpf(string $cpf): ?string {
$valor = preg_replace('/\D+/', '', $cpf);
if ($valor === '') return null;
if (strlen($valor) !== 11 || preg_match('/^(\d)\1{10}$/', $valor)) throw new InvalidArgumentException('Informe um CPF
válido ou deixe em branco.');
for ($t=9;$t<=10;$t++) {
$soma=0;
for ($i=0;$i<$t;$i++) $soma += (int)$valor[$i]*($t+1-$i);
$digito = ($soma*10)%11;
if ($digito===10) $digito=0;
if ($digito !== (int)$valor[$t]) throw new InvalidArgumentException('Informe um CPF válido ou deixe em branco.');
}
return substr($valor,0,3).'.'.substr($valor,3,3).'.'.substr($valor,6,3).'-'.substr($valor,9,2);
}
function salvarCadastroCliente(PDO $pdo, array $dados): int {
$nome=trim((string)($dados['nome']??''));
$email=strtolower(trim((string)($dados['email']??'')));
$telefone=normalizarTelefone((string)($dados['telefone']??''));
$cpf=normalizarCpf((string)($dados['cpf']??''));
$senha=(string)($dados['senha']??'');
if (!validarNome($nome) || !validarEmail($email) || !validarSenha($senha)) throw new InvalidArgumentException('Confira
nome, e-mail e senha (mínimo de 10 caracteres).');
if ($senha !== ($dados['confirmar_senha'] ?? null)) throw new InvalidArgumentException('As senhas informadas não
coincidem.');
$pdo->beginTransaction();
try {
$perfil=$pdo->query("SELECT id FROM perfis WHERE codigo='CLIENTE' AND status='ATIVO'")->fetchColumn();
if (!$perfil) throw new RuntimeException('Perfil CLIENTE indisponível. Execute os dados iniciais do banco.');
$stmt=$pdo->prepare("INSERT INTO usuarios (perfil_id,nome,email,cpf,telefone,senha_hash,status) VALUES
(?,?,?,?,?,?,'ATIVO')");
$stmt->execute([(int)$perfil,$nome,$email,$cpf,$telefone,password_hash($senha,PASSWORD_DEFAULT)]);
$id=(int)$pdo->lastInsertId();
auditar($pdo,$id,'CADASTRAR','usuarios',$id,'SUCESSO','Cadastro público de cliente');
$pdo->commit();
return $id;
} catch (Throwable $e) {if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function salvarUsuarioAdmin(PDO $pdo, array $dados, int $autor, ?int $id=null): int {
$nome=trim((string)($dados['nome']??''));
$email=strtolower(trim((string)($dados['email']??'')));
$telefone=normalizarTelefone((string)($dados['telefone']??''));
$cpf=normalizarCpf((string)($dados['cpf']??''));
$senha=(string)($dados['senha']??'');
$perfil=(string)($dados['perfil']??'');$status=(string)($dados['status']??'ATIVO');
if (!validarNome($nome)||!validarEmail($email)|| !in_array($perfil,['ADMIN','CAIXA','CLIENTE'],true) ||
!in_array($status,['ATIVO','INATIVO','BAIXA'],true)) throw new InvalidArgumentException('Verifique os dados, perfil e
situação do usuário.');
if (($id===null || $senha!=='') && !validarSenha($senha)) throw new InvalidArgumentException('A senha precisa ter
entre 10 e 200 caracteres.');
if ($id!==null && $id===$autor && ($perfil!=='ADMIN'||$status!=='ATIVO')) throw new InvalidArgumentException('Não é
permitido retirar o próprio acesso administrativo.');
$pdo->beginTransaction();
try {

$pdo->query("SELECT id FROM perfis WHERE codigo='ADMIN' FOR UPDATE")->fetchColumn();
$perfilId=$pdo->prepare("SELECT id FROM perfis WHERE codigo=? AND status='ATIVO' LIMIT 1");
$perfilId->execute([$perfil]);
$pid=$perfilId->fetchColumn();
if (!$pid) throw new InvalidArgumentException('Perfil indisponível.');
if ($id===null) {
$stmt=$pdo->prepare('INSERT INTO usuarios (perfil_id,nome,email,cpf,telefone,senha_hash,status) VALUES
(?,?,?,?,?,?,?)');
$stmt->execute([(int)$pid,$nome,$email,$cpf,$telefone,password_hash($senha,PASSWORD_DEFAULT),$status]);
$id=(int)$pdo->lastInsertId();
$operacao='CRIAR_USUARIO';
} else {
$atual=$pdo->prepare('SELECT u.perfil_id,p.codigo AS perfil,u.status FROM usuarios u JOIN perfis p ON
p.id=u.perfil_id WHERE u.id=? FOR UPDATE');
$atual->execute([$id]);
$existe=$atual->fetch();
if (!$existe) throw new InvalidArgumentException('Usuário não encontrado.');
if ($existe['perfil']==='ADMIN' && ($perfil!=='ADMIN'||$status!=='ATIVO')) {
$qtd=(int)$pdo->query("SELECT COUNT(*) FROM usuarios u JOIN perfis p ON p.id=u.perfil_id WHERE
p.codigo='ADMIN' AND u.status='ATIVO'")->fetchColumn();
if ($qtd<=1) throw new InvalidArgumentException('Não é permitido desativar ou rebaixar o último
administrador ativo.');
}
if ($senha==='') {
$stmt=$pdo->prepare('UPDATE usuarios SET perfil_id=?,nome=?,email=?,cpf=?,telefone=?,status=? WHERE
id=?');
$stmt->execute([(int)$pid,$nome,$email,$cpf,$telefone,$status,$id]);
} else {
$stmt=$pdo->prepare('UPDATE usuarios SET perfil_id=?,nome=?,email=?,cpf=?,telefone=?,status=?,senha_hash=?
WHERE id=?');
$stmt-
execute([(int)$pid,$nome,$email,$cpf,$telefone,$status,password_hash($senha,PASSWORD_DEFAULT),$id]);
}
$operacao='ATUALIZAR_USUARIO';
}
auditar($pdo,$autor,$operacao,'usuarios',$id,'SUCESSO','Perfil '.$perfil.'; situação '.$status);
$pdo->commit();return $id;
} catch (Throwable $e) { if($pdo->inTransaction())$pdo->rollBack();throw $e; }
}
function atualizarPerfilProprio(PDO $pdo, int $id, array $dados): void {
$nome=trim((string)($dados['nome']??''));
$telefone=normalizarTelefone((string)($dados['telefone']??''));
if (!validarNome($nome)) throw new InvalidArgumentException('Nome precisa ter de 3 a 150 caracteres.');
$pdo->beginTransaction();
try {
$stmt=$pdo->prepare('UPDATE usuarios SET nome=?,telefone=? WHERE id=? AND status=\'ATIVO\'');
$stmt->execute([$nome,$telefone,$id]);
auditar($pdo,$id,'EDITAR_PERFIL','usuarios',$id,'SUCESSO','Atualização de nome e telefone');
$pdo->commit();
} catch (Throwable $e) {if($pdo->inTransaction()) $pdo->rollBack();throw $e;}
}