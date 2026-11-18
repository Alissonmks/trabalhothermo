<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/Style.css">
    <title>Dashboard</title>
</head>
<body>
        <nav class="menu-container">

   <div class="menu-direito">
  <a href="index.php" class="logo">
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
  </ul>


  <div class="menu-esquerdo">
    <a href="login.php" class="link-login">Entrar / Login</a>
  </div>
</nav>
<section class="pagina-topo">
        <div class="conteiner-pagina-topo-gri">
          <h1>dashboard</h1>
          <p>painel de controle administrativo</p>

        </div>
    </section>
     
    
    <div class="titulo-com-nota">
        <h2 class="titulo-secao">indicadores do dia</h2>
         <p class="subtitulo"></p>
    </div>
     
    <div class="kpis-container">
  <div class="kpi">
    <span>Pedidos hoje</span>
    <strong>10</strong>
  </div>
   
  <div class="kpi">
    <span>Produtos</span>
    <strong>3</strong>
  </div>

  <div class="kpi">
    <span>Clientes</span>
    <strong>123</strong>
  </div>

  <div class="kpi">
    <span>Faturamento</span>
    <strong>R$ 1.600</strong>
  </div>
</div>


<div class="titulo-com-nota">
  <div class="titulo-secao">
    <p class="subtitulo">ultimos pedidos</p>
  </div>
    
    <div class="tabela-bloco">
      <div class="tabela-area">
        <table class="tabela">
          <thead>
            <tr><th>Pedido</th><th>cliente</th><th>produto</th><th>preço</th><th>status</th></tr>
          </thead>
         <tbody>
          <tr>
            <td>#001</td><td>maria</td><td>Guarrafa 500ml</td><td>R$ 20,00</td>
            <td><span class="status-ok">Entregue</span></td>
          </tr>

          <tr>
            <td>#002</td><td>Pedro</td><td>Guarrafa 750ml</td><td>R$ 35,00</td>
            <td><span class="status-ok">Entregue</span></td>
          </tr>
          <tr>
            <td>#003</td><td>Julia</td><td>Guarrafa 800ml</td><td>R$ 50,00</td>
            <td><span class="status-ok">Entregue</span></td>
           </tbody>
        </table>
     </div>
      </div>
      <button id="voltarTopo" class="voltar-topo" type="button">Topo</button>
</body>
</html>