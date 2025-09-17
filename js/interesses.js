import { buttonExclude } from "./modal-excluir.js";

const listaInteresses = document.querySelector(".lista-interesses");

function renderizarInteresses(novosInteresses) {
  const template = document.getElementById("template-interesse");

  for (const interesse of novosInteresses) {
    const novoItem = template.content.cloneNode(true);
    const item = novoItem.querySelector(".interesse");

    item.dataset.id = interesse.id;

    const avatarImg = item.querySelector(".avatar-img");
    avatarImg.src = interesse.avatar || "images/interesses/avatar.png";
    avatarImg.alt = `Avatar do usuário: ${interesse.nome}`;

    item.querySelector(".user-nome").textContent = interesse.nome;
    item.querySelector(".user-telefone").textContent = interesse.telefone;
    item.querySelector(".mensagem-texto").textContent = interesse.mensagem;

    const deleteButton = item.querySelector(".excluir-btn");
    buttonExclude(
      deleteButton,
      "/php/controladorRestrito.php?acao=excluirInteresse",
      interesse.id,
    );

    listaInteresses.appendChild(item);
  }

  atualizarContador();
}

function atualizarContador() {
  const total = document.querySelector(".total");
  if (total) {
    const interesses = document.querySelectorAll(".interesse");
    total.textContent = interesses.length;
  }
}

async function fetchInteresses() {
  try {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    if (!id) {
      throw new Error("ID do anúncio não fornecido na URL");
    }

    const response = await fetch(
      "/php/controladorRestrito.php?acao=meusInteresses&id=" + id,
    );
    const data = await response.json();

    if (!response.ok) {
      if (response.status === 404) window.location.href = "/notfound";
      else if (response.status === 403) window.location.href = "/forbidden";
      throw new Error(data.erro || "Erro ao buscar interesses");
    }

    const interessesData = data.interesses.map((interesse) => ({
      id: interesse.id,
      avatar: "images/interesses/avatar.png",
      nome: interesse.nome,
      telefone: interesse.telefone,
      mensagem: interesse.mensagem,
    }));

    renderizarInteresses(interessesData);
  } catch (error) {
    console.error("Erro ao buscar interesses:", error);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  atualizarContador();
  fetchInteresses();
});
