<?php
require 'php/conexaoMysql.php';
require 'php/sessionVerification.php';

session_start();

exitWhenNotLoggedIn();
?>

<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >
    <title>Anúncios | AutoHub</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/interesses.css" rel="stylesheet">
  </head>

  <body>
    <header>
      <a href="/">
        <img src="images/logo.png" alt="Logo AutoHub" id="logo">
      </a>
      <nav>
        <ul>
          <li><a href="anuncios">Anúncios</a></li>
          <li><a class="highlighted-btn" href="painel">Início</a></li>
        </ul>
      </nav>
    </header>

    <main class="interesses">
      <h1>Interesses</h1>
      <div class="count">
        Total: <span class="total"></span> interesse(s) registrado(s)
      </div>
      <ul class="lista-interesses">
        <template id="template-interesse">
          <li class="interesse">
            <div class="user_avatar">
              <img class="avatar-img" src="" alt="">
            </div>
            <div class="user_info">
              <h3 class="user-nome"></h3>
              <p class="user-telefone"></p>
            </div>
            <div class="user_message">
              <p>
                <strong>Mensagem de Interesse:</strong>
                <span class="mensagem-texto"></span>
              </p>
            </div>
            <div class="interesse-actions">
              <i class="fas fa-trash excluir-btn"></i>
            </div>
          </li>
        </template>
      </ul>

      <div id="modal-excluir">
        <div>
          <h2>⚠️ Excluir Interesse</h2>
          <p>Tem certeza que deseja excluir este interesse?</p>
          <p>
            <button id="confirmar-excluir">Excluir</button>
            <button id="cancelar-excluir">Cancelar</button>
          </p>
        </div>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 AutoHub. Todos os direitos reservados.</p>
    </footer>

    <script type="module" src="js/interesses.js"></script>
  </body>
</html>
