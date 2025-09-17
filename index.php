<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AutoHub</title>
    <link href="css/style.css" rel="stylesheet">
  </head>

  <body class="index">
    <header>
      <a href="/">
        <img src="images/logo.png" alt="Logo AutoHub" id="logo">
      </a>
      <nav>
        <ul>
          <?php
            session_start();
          if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
              echo '<li><a href="/php/logout.php">Sair</a></li>';
              echo '<li><a class="highlighted-btn" href="painel">Painel</a></li>';
          } else {
              echo '<li><a href="login">Login</a></li>';
              echo '<li><a class="highlighted-btn" href="cadastro">Anuncie</a></li>';
          }
          ?>
        </ul>
      </nav>
    </header>

    <main class="main-container">
      <div class="filtro-container">
        <select id="marca"></select>
        <select id="modelo"></select>
        <select id="cidade"></select>
        <button class="limpar-btn" type="button">Limpar</button>
      </div>

      <div id="cards-container">
        <template id="template-anuncio">
          <div class="card">
            <img class="imagem-anuncio">
            <div class="card-content">
              <p class="marca-modelo"></p>
              <p class="ano"></p>
              <p class="preco"></p>
              <button class="detalhes-btn">Ver detalhes</button>
            </div>
          </div>
        </template>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 AutoHub. Todos os direitos reservados.</p>
    </footer>
    <button id="voltar-topo">↑</button>
    <script type="module" src="js/index.js"></script>
  </body>
</html>
