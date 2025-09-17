import { FormErrorHandler } from "./utils/errorHandler.js";

const modal = document.getElementById("interesse-modal");
const interestForm = document.getElementById("interesse-form");

const closeButton = document.getElementById("close-modal");
closeButton.addEventListener("click", () => {
  modal.classList.remove("active-modal");
  document.body.classList.remove("stop-scrolling");
  interestForm.reset();
});

interestForm.onsubmit = async (event) => {
  event.preventDefault();
  const nome = document.getElementById("nome").value.trim();
  const telefone = document.getElementById("telefone").value.trim();
  const mensagem = document.getElementById("mensagem").value.trim();

  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  const form = new FormData(interestForm);
  form.append("idAnuncio", id);

  const validator = new FormErrorHandler(interestForm);

  try {
    if (nome && telefone && mensagem) {
      interestForm.reset();
      const response = await fetch(interestForm.action, {
        method: "POST",
        body: form,
      });
      const data = await response.json();
      if (response.ok && data.sucesso) {
        window.location.href = "/";
        modal.classList.remove("active-modal");
      } else if (response.status === 400 && data.erros) {
        let firstErrorField = null;

        for (let [campo, mensagem] of Object.entries(data.erros)) {
          const input = validator.setError(campo, mensagem);

          if (!firstErrorField) firstErrorField = input;
        }

        if (firstErrorField) firstErrorField.focus();
      } else {
        const campos = form.querySelectorAll("input");

        campos.forEach((campo) => {
          campo.value = "";
        });

        console.error("Erro inesperado:", resposta.erro || resposta.erros);

        const errorElement = document.createElement("p");
        errorElement.classList.add("form-error-msg");
        errorElement.textContent =
          resposta.erro || "Erro inesperado. Tente novamente.";

        form.appendChild(errorElement);
      }
    } else {
      alert("Por favor, preencha todos os campos.");
    }
  } catch (error) {
    console.error("Erro ao registrar interesse:", error);
  }
};

async function buscarAnuncio(id) {
  try {
    const response = await fetch(
      `/php/controlador.php?acao=buscarAnuncio&id=${id}`,
    );
    if (!response.ok) {
      if (response.status === 404) {
        window.location.href = "/notfound";
        return;
      }
      throw new Error(data.erro || "Erro ao buscar anuncio");
    }
    const anuncio = await response.json();

    renderizarDetalhes(anuncio);
  } catch (error) {
    console.error("Erro ao buscar anuncio:", error);
  }
}

function renderizarDetalhes(anuncio) {
  const main = document.querySelector(".details-container");
  const template = document.getElementById("template-detalhamento");

  const clone = template.content.cloneNode(true);

  clone.querySelector("h1").textContent = `${anuncio.marca} ${anuncio.modelo}`;
  clone.querySelector("#marca").textContent = anuncio.marca;
  clone.querySelector("#modelo").textContent = anuncio.modelo;
  clone.querySelector("#ano").textContent = anuncio.ano;
  clone.querySelector("#cor").textContent = anuncio.cor;
  clone.querySelector("#quilometragem").textContent = anuncio.quilometragem;
  clone.querySelector("#valor").textContent = anuncio.valor;
  clone.querySelector("#estado").textContent = anuncio.estado;
  clone.querySelector("#cidade").textContent = anuncio.cidade;
  clone.querySelector("#descricao").textContent = anuncio.descricao;

  const mainImage = clone.querySelector(".main-image img");
  const thumbnailsContainer = clone.querySelector(".thumbnails");

  let currentIndex = 0;

  function updateMainImage(index) {
    const thumbs = thumbnailsContainer.querySelectorAll("img");
    mainImage.src = thumbs[index].src;
    mainImage.alt = thumbs[index].alt;

    thumbs.forEach((img) => img.classList.remove("selected"));
    thumbs[index].classList.add("selected");
  }

  anuncio.fotos.forEach((imgUrl, index) => {
    const thumb = document.createElement("img");
    thumb.src = imgUrl;
    thumb.alt = `${anuncio.marca} ${anuncio.modelo} - ${index + 1}`;

    thumb.addEventListener("click", () => {
      currentIndex = index;
      updateMainImage(currentIndex);
    });

    thumbnailsContainer.appendChild(thumb);
  });

  updateMainImage(currentIndex);

  const leftArrow = clone.querySelector(".arrow.left");
  const rightArrow = clone.querySelector(".arrow.right");

  leftArrow.addEventListener("click", () => {
    const thumbs = thumbnailsContainer.querySelectorAll("img");
    currentIndex = (currentIndex - 1 + thumbs.length) % thumbs.length;
    updateMainImage(currentIndex);
  });

  rightArrow.addEventListener("click", () => {
    const thumbs = thumbnailsContainer.querySelectorAll("img");
    currentIndex = (currentIndex + 1) % thumbs.length;
    updateMainImage(currentIndex);
  });

  const interesseBtn = clone.querySelector("#interesse");
  interesseBtn.addEventListener("click", () => {
    modal.classList.add("active-modal");
    document.body.classList.add("stop-scrolling");
  });

  main.innerHTML = "";
  main.appendChild(clone);
}

window.onload = () => {
  const params = new URLSearchParams(window.location.search);

  const id = params.get("id");

  if (!id) window.location.href = "/";

  buscarAnuncio(id);
};

window.onclick = (event) => {
  if (event.target === modal) {
    modal.classList.remove("active-modal");
    document.body.classList.remove("stop-scrolling");
    interestForm.reset();
  }
};
