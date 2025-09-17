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
    <title>Adicionar anúncio | AutoHub</title>
    <link href="css/error.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/forms.css" rel="stylesheet">
  </head>

  <body>
    <header>
      <a href="/">
        <img src="images/logo.png" alt="Logo AutoHub" id="logo">
      </a>
      <nav>
        <ul>
          <li><a class="highlighted-btn" href="anuncios">Voltar</a></li>
        </ul>
      </nav>
    </header>

    <main class="main-container transparent-bg">
      <h1>Adicionar anúncio</h1>
      <form
        enctype="multipart/form-data"
        id="add-ad-form"
        action="/php/controladorRestrito.php?acao=adicionarAnuncio"
        method="POST"
      >
        <fieldset>
          <legend>Dados veículo</legend>
          <div class="form-group">
            <label for="marca">Marca:</label>
            <input
              type="text"
              id="marca"
              name="marca"
              required
              placeholder="Marca do veículo"
              autofocus
            >
          </div>
          <div class="form-group">
            <label for="modelo">Modelo:</label>
            <input
              type="text"
              id="modelo"
              name="modelo"
              placeholder="Modelo do veículo"
              required
            >
          </div>
          <div class="form-group">
            <label for="ano">Ano de fabricação:</label>
            <input
              type="number"
              id="ano"
              name="ano"
              min="1900"
              max="2100"
              required
              placeholder="Ano de fabricação do veículo"
            >
          </div>

          <div class="form-group">
            <label for="cor">Cor:</label>
            <input
              type="text"
              id="cor"
              name="cor"
              required
              placeholder="Cor do veículo"
            >
          </div>
          <div class="form-group">
            <label for="quilometragem">Quilometragem:</label>
            <input
              type="number"
              id="quilometragem"
              name="quilometragem"
              placeholder="Quilometragem do veículo em km"
              required
            >
          </div>
          <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea
              id="descricao"
              name="descricao"
              required
              placeholder="Descreva os detalhes da situação do veículo"
              rows="4"
            ></textarea>
          </div>
          <div class="form-group">
            <label for="valor">Valor:</label>
            <div class="currency-input">
              <span class="currency-symbol">R$</span>
              <input
                class="currency-field"
                type="number"
                id="valor"
                name="valor"
                placeholder="Valor do veículo"
                required
              >
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Localização</legend>
          <div class="form-group">
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" required>
              <option value="" disabled selected hidden>
                Selecione o estado
              </option>
              <option value="AC">Acre</option>
              <option value="AL">Alagoas</option>
              <option value="AP">Amapá</option>
              <option value="AM">Amazonas</option>
              <option value="BA">Bahia</option>
              <option value="CE">Ceará</option>
              <option value="DF">Distrito Federal</option>
              <option value="ES">Espírito Santo</option>
              <option value="GO">Goiás</option>
              <option value="MA">Maranhão</option>
              <option value="MT">Mato Grosso</option>
              <option value="MS">Mato Grosso do Sul</option>
              <option value="MG">Minas Gerais</option>
              <option value="PA">Pará</option>
              <option value="PB">Paraíba</option>
              <option value="PR">Paraná</option>
              <option value="PE">Pernambuco</option>
              <option value="PI">Piauí</option>
              <option value="RJ">Rio de Janeiro</option>
              <option value="RN">Rio Grande do Norte</option>
              <option value="RS">Rio Grande do Sul</option>
              <option value="RO">Rondônia</option>
              <option value="RR">Roraima</option>
              <option value="SC">Santa Catarina</option>
              <option value="SP">São Paulo</option>
              <option value="SE">Sergipe</option>
              <option value="TO">Tocantins</option>
            </select>
          </div>
          <div class="form-group">
            <label for="cidade">Cidade:</label>
            <input
              type="text"
              id="cidade"
              name="cidade"
              required
              placeholder="Cidade onde o veículo está localizado"
            >
          </div>
          <div class="form-group">
            <label for="fotos">Imagens do carro:</label>
            <input
              type="file"
              id="fotos"
              name="fotos[]"
              accept="image/jpeg,image/png,image/webp"
              multiple
              required
            >
          </div>
        </fieldset>
        <button type="submit">Criar anúncio</button>
      </form>
    </main>

    <footer>
      <p>&copy; 2025 AutoHub. Todos os direitos reservados.</p>
    </footer>
    <script type="module" src="js/anuncio.js"></script>
  </body>
</html>
