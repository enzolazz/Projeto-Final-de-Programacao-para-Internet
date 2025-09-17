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
    <title>AutoHub</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/painel.css" rel="stylesheet">
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    >
  </head>

  <body>
    <div class="sidebar">
      <img src="images/sidebar2.png" alt="Veículo em destaque">
    </div>

    <div class="main-area">
      <header>
        <a href="/">
          <img src="images/logo.png" alt="Logo AutoHub" id="logo">
        </a>
        <nav>
          <ul>
            <li><a href="/">Início</a></li>
            <li><a class="highlighted-btn" href="/php/logout.php">Sair</a></li>
          </ul>
        </nav>
      </header>

      <main>
        <div>
          <h1>Olá, <span class="nome"><?php echo htmlspecialchars($_SESSION['user']->nome) ?></span>!</h1>
          <p>O que você gostaria de fazer hoje?</p>
        </div>

        <div class="menu">
          <a href="anuncio" class="option-card create">
            <span
              class="material-icons"
              style="font-size: 3rem; margin-bottom: 1rem"
              >create</span
            >
            <h3>Criar Anúncios</h3>
            <p>
              Cadastre novos veículos e publique seus anúncios de forma rápida e
              fácil
            </p>
          </a>

          <a href="anuncios" class="option-card list">
            <span
              class="material-icons"
              style="font-size: 3rem; margin-bottom: 1rem"
              >list</span
            >
            <h3>Verificar Anúncios</h3>
            <p>Visualize e gerencie todos os seus anúncios publicados</p>
          </a>
        </div>
      </main>

      <footer>
        <p>&copy; 2025 AutoHub. Todos os direitos reservados.</p>
      </footer>
    </div>
  </body>
</html>
