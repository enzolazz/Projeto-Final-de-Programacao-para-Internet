import { buttonExclude } from "./modal-excluir.js";

function renderizarAnuncios(novosAnuncios) {
  const secaoAnuncios = document.querySelector(".anuncios-grid");
  const template = document.getElementById("template-anuncio");

  for (const anuncio of novosAnuncios) {
    const novoCard = template.content.cloneNode(true);
    const card = novoCard.querySelector(".anuncio");
    card.dataset.id = anuncio.id;

    card.querySelector(".anuncio-imagem").src =
      anuncio.fotos[0] || "placeholder.png";
    card.querySelector(".anuncio-imagem").alt =
      `${anuncio.marca} ${anuncio.modelo}`;
    card.querySelector(".anuncio-titulo").textContent =
      `${anuncio.marca} ${anuncio.modelo}`;
    card.querySelector(".marca-value").textContent = anuncio.marca;
    card.querySelector(".modelo-value").textContent = anuncio.modelo;
    card.querySelector(".ano-value").textContent = anuncio.ano;
    card.querySelector(".preco-value").textContent = `R$ ${anuncio.valor}`;

    card.querySelector(".detalhes-btn").addEventListener("click", () => {
      window.location.href = `/detalhamento?id=${anuncio.id}`;
    });

    card.querySelector(".interesses-btn").addEventListener("click", () => {
      window.location.href = `/interesses?id=${anuncio.id}`;
    });

    const deleteButton = card.querySelector(".excluir-btn");
    buttonExclude(
      deleteButton,
      "/php/controladorRestrito.php?acao=excluirAnuncio",
      anuncio.id,
    );

    secaoAnuncios.appendChild(card);
  }
}

window.onload = async function () {
  try {
    const response = await fetch(
      "/php/controladorRestrito.php?acao=meusAnuncios",
    );
    const data = await response.json();

    if (!response.ok) {
      console.error("Erro ao carregar anúncios:", data.erro);
      return;
    }

    renderizarAnuncios(data.anuncios);
  } catch (error) {
    console.error("Erro ao carregar anúncios:", error);
  }
};
