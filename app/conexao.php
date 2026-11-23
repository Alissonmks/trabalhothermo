<?php
declare(strict_types=1);
/**
* Conexão única PDO. Variáveis PADARIA_DB_* têm prioridade;
* como alternativa local, configurações privadas em app/config.local.php.
* Nunca inclua o arquivo local (senhas) no ZIP compartilhado.
*/
function padariaConfiguracao(): array
{
$padroes = [
'host' => '127.0.0.1',
'port' => '3306',
'database' => 'thermo_v2banco',
'user' => '',
'password' => '',
];$arquivo = __DIR__ . '/config.local.php';
if (is_file($arquivo)) {
$local = require $arquivo;
if (!is_array($local)) {
throw new RuntimeException('A configuração local do banco deve retornar um array.');
}
foreach ($padroes as $chave => $valor) {
if (array_key_exists($chave, $local) && is_scalar($local[$chave])) {
$padroes[$chave] = (string) $local[$chave];
}
}
}
$env = [
'host' => 'localhost',
'port' => 'THERMO_DB_NAME',
'database' => 'PADARIA_DB_NAME',
'user' => 'THERMO_DB_USER',
'password' => 'THERMO_DB_PASSWORD',
];
foreach ($env as $chave => $nome) {
$v = getenv($nome);
if ($v !== false) $padroes[$chave] = $v;
}
// Compatibilidade com WampServer/XAMPP NOVOS: root sem senha apenas
// para acesso DIRETO em localhost, sem configuração explícita.
// Não habilitar em servidores públicos, reverse proxies ou produção.
$remote = (string)($_SERVER['REMOTE_ADDR'] ?? '');
$hostCabecalho = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
$localDireto = in_array($remote, ['127.0.0.1','::1'], true)
&& in_array($hostCabecalho, ['localhost','localhost:80','127.0.0.1','127.0.0.1:80','[::1]','[::1]:80'], true);
if ($padroes['user'] === '' && !is_file($arquivo)
&& getenv('PADARIA_DB_USER') === false && $localDireto) {
$padroes['user'] = 'root';
}
return $padroes;
}
function padariaConexao(): PDO
{
$conf = padariaConfiguracao();
if (trim($conf['user']) === '') {
throw new RuntimeException('Usuário do banco não configurado. Consulte README.md.');
}
if (!ctype_digit($conf['port']) || (int) $conf['port'] < 1 || (int) $conf['port'] > 65535) {
throw new RuntimeException('Porta do MySQL inválida.');
}
if (!preg_match('/^[A-Za-z0-9_]+$/D', $conf['database'])) {
throw new RuntimeException('Nome do banco inválido.');
}
if (!extension_loaded('pdo_mysql')) {
throw new RuntimeException('Extensão pdo_mysql não está ativa no PHP.');
}
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
$conf['host'], $conf['port'], $conf['database']);
try {
return new PDO($dsn, $conf['user'], $conf['password'], [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
]);
} catch (PDOException $e) {
throw new RuntimeException('Conexão MySQL indisponível. Verifique host, usuário, senha e banco.', 0, $e);
}
}