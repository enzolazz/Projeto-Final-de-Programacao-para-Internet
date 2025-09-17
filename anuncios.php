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
    <title>Anúncios | AutoHub</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <link href="css/style.css" rel="stylesheet">
    <link href="css/anuncios.css" rel="stylesheet">
  </head>

  <body>
    <header>
      <a href="/">
        <img src="images/logo.png" alt="Logo AutoHub" id="logo">
      </a>
      <nav>
        <ul>
          <li><a href="painel">Voltar</a></li>
          <li><a class="highlighted-btn" href="/">Início</a></li>
        </ul>
      </nav>
    </header>

    <main class="anuncios">
      <div class="top">
        <div class="fx"></div>
        <h1>Anuncios</h1>
        <div class="fx"></div>
      </div>

      <div class="anuncios-grid">
        <div id="modal-excluir">
          <div>
            <h2>⚠️ Excluir Anúncio</h2>
            <p>Tem certeza que deseja excluir este anúncio?</p>
            <p>
              <button id="confirmar-excluir">Excluir</button>
              <button id="cancelar-excluir">Cancelar</button>
            </p>
          </div>
          <template id="template-anuncio">
            <div class="anuncio">
              <img class="anuncio-imagem" src="" alt="">
              <i class="fas fa-trash excluir-btn"></i>
              <div class="anuncio-content">
                <div class="anuncio-header">
                  <h3 class="anuncio-titulo"></h3>
                </div>
                <div class="anuncio-info">
                  <div class="info-row">
                    <span class="info-label marca">Marca:</span>
                    <span class="info-value marca-value"></span>
                  </div>
                  <div class="info-row">
                    <span class="info-label modelo">Modelo:</span>
                    <span class="info-value modelo-value"></span>
                  </div>
                  <div class="info-row">
                    <span class="info-label ano">Ano do Modelo:</span>
                    <span class="info-value ano-value"></span>
                  </div>
                  <div class="info-row">
                    <span class="info-label preco">Preço:</span>
                    <span class="info-value preco-value destaque"></span>
                  </div>
                </div>
                <div class="anuncio-actions">
                  <button class="detalhes-btn">Detalhes</button>
                  <button class="interesses-btn">Interesses</button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 AutoHub. Todos os direitos reservados.</p>
    </footer>
    <script type="module" src="js/anuncios.js"></script>
  </body>
</html>
