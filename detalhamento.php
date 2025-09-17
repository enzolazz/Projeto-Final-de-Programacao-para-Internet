<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exibição Detalhada | AutoHub</title>
    <link href="css/error.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/detalhamento.css" rel="stylesheet">
  </head>

  <body>
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

              $id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
              $urlInteresses = 'interesses?id=' . $id;
              echo '<li><a href="' . $urlInteresses . '">Interesses</a></li>';

              echo '<li><a class="highlighted-btn" href="painel">Painel</a></li>';
          } else {
              echo '<li><a class="highlighted-btn" href="/">Início</a></li>';
          }
          ?>
        </ul>
      </nav>
    </header>

    <main class="details-container">
      <template id="template-detalhamento">
        <div class="details-caroussel">
          <div class="main-image">
            <img>
          </div>
          <div class="caroussel">
            <button class="arrow left">&#10094;</button>
            <div class="thumbnails"></div>
            <button class="arrow right">&#10095;</button>
          </div>
        </div>
        <div class="details-content">
          <h1></h1>

          <ul class="car-details">
            <li>
              <span class="titulo">Marca</span>
              <span class="valor" id="marca"></span>
            </li>
            <li>
              <span class="titulo">Modelo</span>
              <span class="valor" id="modelo"></span>
            </li>
            <li>
              <span class="titulo">Ano de fabricação</span>
              <span class="valor" id="ano"></span>
            </li>
            <li>
              <span class="titulo">Cor</span>
              <span class="valor" id="cor"></span>
            </li>
            <li>
              <span class="titulo">Quilometragem</span>
              <span class="valor" id="quilometragem"></span>
            </li>
            <li>
              <span class="titulo">Valor</span>
              <span class="valor" id="valor"></span>
            </li>
            <li>
              <span class="titulo">Estado</span>
              <span class="valor" id="estado"></span>
            </li>
            <li>
              <span class="titulo">Cidade</span>
              <span class="valor" id="cidade"></span>
            </li>
            <li>
              <span class="titulo">Descrição</span>
              <span class="valor" id="descricao"></span>
            </li>
          </ul>
          <button id="interesse">Entrar em Contato</button>
        </div>
      </template>
    </main>

    <div class="modal" id="interesse-modal">
      <div class="modal-content">
        <span class="close" id="close-modal">&times;</span>
        <h2>Entrar em Contato</h2>
        <form
          id="interesse-form"
          method="POST"
          action="/php/controlador.php?acao=adicionarInteresse"
        >
          <div class="form-group">
            <label for="nome">Nome completo:</label>
            <input
              type="text"
              id="nome"
              name="nome"
              required
              placeholder="Digite seu nome completo"
              autofocus
            >
          </div>

          <div class="form-group">
            <label for="telefone">Telefone:</label>
            <input
              type="tel"
              id="telefone"
              name="telefone"
              required
              placeholder="(11) 99999-9999"
            >
          </div>
          <div class="form-group">
            <label for="mensagem">Mensagem:</label>
            <textarea
              id="mensagem"
              name="mensagem"
              required
              placeholder="Escreva sua mensagem aqui..."
            ></textarea>
          </div>

          <button type="submit">Registrar interesse</button>
        </form>
      </div>
    </div>

    <footer>
      <p>&copy; 2025 AutoHub. Todos os direitos reservados.</p>
    </footer>
    <script type="module" src="js/detalhamento.js"></script>
  </body>
</html>
