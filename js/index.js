import {carregarOpcoes, FiltrosAnuncio} from "./utils/filtrosAnuncios.js";

const filtros = new FiltrosAnuncio();

let carregandoAnuncios = false;
let temMaisAnuncios = true;

function renderizarAnuncios(novosAnuncios) {
  const secaoAnuncios = document.getElementById("cards-container");
  const cardTemplate =
    document.getElementById("template-anuncio").content.firstElementChild;
  for (let anuncio of novosAnuncios) {
    const novoCard = cardTemplate.cloneNode(true);
    novoCard.dataset.id = anuncio.id;
    novoCard.querySelector(".imagem-anuncio").src = anuncio.fotos[0];
    novoCard.querySelector(
      ".marca-modelo"
    ).textContent = `${anuncio.marca} ${anuncio.modelo}`;
    novoCard.querySelector(
      ".ano"
    ).textContent = `Ano: ${anuncio.ano} • ${anuncio.cidade}`;
    novoCard.querySelector(".preco").textContent = `R$ ${anuncio.valor}`;

    novoCard.querySelector(".detalhes-btn").addEventListener("click", () => {
      window.location.href = `/detalhamento?id=${anuncio.id}`;
    });

    secaoAnuncios.appendChild(novoCard);
  }
}

async function carregarAnuncios() {
  if (carregandoAnuncios || !temMaisAnuncios) {
    return;
  }

  carregandoAnuncios = true;

  try {
    const params = new URLSearchParams(filtros.toFormData()).toString();

    const data = await chamadaControlador(`maisAnuncios&${params}`);

    if (!data || !data.sucesso)
      throw new Error(data?.mensagem || "Falha ao carregar anuncios.");

    filtros.setFiltro("offset", data.proxOffset);

    const anuncios = data.anuncios;

    if (!anuncios || anuncios.length === 0) {
      temMaisAnuncios = false;
      return;
    }

    renderizarAnuncios(anuncios);
  } catch (e) {
    console.error(e);
  } finally {
    carregandoAnuncios = false;
  }
}

function limparCards() {
  const container = document.getElementById("cards-container");
  const template = container.querySelector("template");

  container.innerHTML = "";
  container.appendChild(template);

  carregandoAnuncios = false;
  temMaisAnuncios = true;
  filtros.setFiltro("offset", 0);
}

async function chamadaControlador(acao) {
  try {
    const response = await fetch(`/php/controlador.php?acao=${acao}`);

    if (!response.ok) {
      console.log("A repsosta não pode ser obtida.", response.status);
      return;
    }

    return await response.json();
  } catch (error) {
    console.error("Erro na requisição:", error);
  }
}

const selectMarca = document.getElementById("marca");
selectMarca.addEventListener("change", async () => {
  const valor = selectMarca.value;
  filtros.setFiltro("marca", valor);
  filtros.setFiltro("modelo", null);
  filtros.setFiltro("cidade", null);

  const selectModelos = document.getElementById("modelo");
  const selectCidades = document.getElementById("cidade");

  const promiseModelos = chamadaControlador(`modelos&marca=${valor}`);
  const promiseCidades = chamadaControlador(`cidades&marca=${valor}`);

  const [modelos, cidades] = await Promise.all([
    promiseModelos,
    promiseCidades,
  ]);

  carregarOpcoes(selectModelos, "Modelos", modelos || []);
  carregarOpcoes(selectCidades, "Cidades", cidades || []);

  limparCards();
  carregarAnuncios();
});

const selectModelo = document.getElementById("modelo");
selectModelo.addEventListener("change", async () => {
  const valor = selectModelo.value;
  filtros.setFiltro("modelo", valor);
  filtros.setFiltro("cidade", null);

  const selectCidades = document.getElementById("cidade");

  const cidades = await chamadaControlador(
    `cidades&marca=${filtros.marca}&modelo=${valor}`
  );

  carregarOpcoes(selectCidades, "Cidades", cidades || []);

  limparCards();
  carregarAnuncios();
});

const selectCidade = document.getElementById("cidade");
selectCidade.addEventListener("change", () => {
  const valor = selectCidade.value;
  filtros.setFiltro("cidade", valor);

  limparCards();
  carregarAnuncios();
});

document.getElementById("voltar-topo").addEventListener("click", () => {
  window.scrollTo({top: 0, behavior: "smooth"});
});

window.onload = async () => {
  carregarAnuncios();

  try {
    const marcas = await chamadaControlador("marcas");

    if (!marcas) throw new Error("Não foi possível carregar as marcas.");

    const selectMarca = document.getElementById("marca");
    const selectModelo = document.getElementById("modelo");
    const selectCidade = document.getElementById("cidade");

    carregarOpcoes(selectMarca, "Marca", marcas);
    carregarOpcoes(selectModelo, "Modelo", []);
    carregarOpcoes(selectCidade, "Cidade", []);
  } catch (err) {
    console.error("Erro ao carregar marcas:", err);
    carregarOpcoes(selectMarca, ["error"], []);
    carregarOpcoes(selectModelo, "Modelo", []);
    carregarOpcoes(selectCidade, "Cidade", []);
  }
};

window.onscroll = function () {
  if (carregandoAnuncios || !temMaisAnuncios) return;

  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 50) {
    carregarAnuncios();
  }

  const botao = document.getElementById("voltar-topo");
  if (window.scrollY > 300) {
    botao.classList.add("visible");
  } else {
    botao.classList.remove("visible");
  }
};

const btnLimpar = document.querySelector(".filtro-container button");
btnLimpar.addEventListener("click", async () => {
  filtros.setFiltro("marca", null);
  filtros.setFiltro("modelo", null);
  filtros.setFiltro("cidade", null);

  const selectMarca = document.getElementById("marca");
  const selectModelo = document.getElementById("modelo");
  const selectCidade = document.getElementById("cidade");

  if (selectMarca && selectMarca.options.length) selectMarca.selectedIndex = 0;

  carregarOpcoes(selectModelo, "Modelo", []);
  carregarOpcoes(selectCidade, "Cidade", []);

  limparCards();
  await carregarAnuncios();
});
