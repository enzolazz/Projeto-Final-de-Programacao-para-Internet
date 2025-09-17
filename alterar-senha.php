<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alterar Senha | AutoHub</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/forms.css" rel="stylesheet">
    <link href="css/error.css" rel="stylesheet">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >
  </head>

  <body>
    <header>
      <a href="/">
        <img src="images/logo.png" alt="Logo AutoHub" id="logo">
      </a>
      <nav>
        <ul>
          <li><a href="/">Início</a></li>
          <li><a class="highlighted-btn" href="cadastro">Anuncie</a></li>
        </ul>
      </nav>
    </header>

    <main class="form-container">
      <section>
        <h1>Alterar senha</h1>
        <form
          id="form-change-password"
          action="/php/controladorRestrito.php?acao=alterarSenha"
          method="POST"
        >
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">

          <div class="form-group">
            <label for="email">E-mail:</label>
            <input
              type="email"
              id="email"
              name="email"
              required
              autocomplete="email"
              autofocus
              placeholder="exemplo@mail.com"
            >
          </div>
          <div class="form-group">
            <label for="novaSenha">Nova senha:</label>
            <input
              type="password"
              id="novaSenha"
              name="novaSenha"
              required
              placeholder="Sua nova senha"
              autocomplete="current-password"
            >
          </div>
          <button type="submit">Entrar</button>
        </form>
      </section>
      <div class="form-image">
        <img src="images/sidebar3.png" alt="Imagem de cadastro" id="form-image">
      </div>
    </main>

    <footer>
      <p>&copy; 2025 AutoHub. Todos os direitos reservados.</p>
    </footer>
    <script type="module" src="js/alterar-senha.js"></script>
  </body>
</html>
