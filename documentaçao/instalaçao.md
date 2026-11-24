# Instalação do CantinaWeb
## Requisitos
- XAMPP 8.2 ou superior (Apache + PHP 8.2 + MySQL/MariaDB)
- Navegador atualizado (Chrome, Firefox ou Edge)
## Passo a passo
1. Abra o XAMPP Control Panel e clique em **Start** no Apache e no MySQL.
2. Copie a pasta `cantinaweb` para `C:\xampp\htdocs\`.
3. Acesse http://localhost/phpmyadmin
4. Clique em **Novo** e crie o banco `banco-thermo`
(agrupamento `utf8mb4_general_ci`).
5. Com o banco selecionado, vá em **Importar** e escolha
`database/thermo_bancov2`. Clique em **Executar**.
6. Abra `config/conexao.php` e confira os dados de acesso.
7. Acesse http://localhost/cantinaweb
## Configuração (config/conexao.php)
| Variável | Valor padrão no XAMPP |
|----------|-----------------------|
| $host | localhost |
| $banco | banco-thermo |
| $usuario | root |
| $senha | (vazio) |
## Teste
Entre com aluno@teste.com / 123456. Se os produto aparecer,
está tudo certo.
## Problemas comuns
| Erro | Causa | Solução |
|------|-------|---------|

ESCOLA TÉCNICA ESTADUAL PORTO DIGITAL
Curso Técnico em Desenvolvimento de Sistemas
Disciplinas: Programação em Novas Tecnologias · Engenharia de Software
Novembro de 2026

Página 18 de 27

| "Access denied for user root" | Senha do MySQL diferente | Ajuste $senha em conexao.php |
| "Unknown database cantinaweb" | Banco não foi criado | Refaça os passos 4 e 5 |
| Apache não inicia | Porta 80 ocupada (Skype, IIS) | Feche o programa ou mude a porta |
| Acentos aparecem errados | Banco sem utf8mb4 | Recrie o banco com utf8mb4 |