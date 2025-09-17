import { FormErrorHandler } from "./utils/errorHandler.js";

function sendForm(form) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", form.getAttribute("action"));
  xhr.responseType = "json";

  const validator = new FormErrorHandler(form);

  validator.clearAll();
  xhr.onload = () => {
    const resposta = xhr.response;

    if (xhr.status === 200 && resposta.sucesso) {
      window.location.href = resposta.redirect || "/login";
    } else if (xhr.status === 400 && resposta.erro) {
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
  };

  xhr.onerror = () => {
    console.error("Erro de rede - requisição não finalizada");
    alert("Erro de rede. Tente novamente.");
  };

  xhr.send(new FormData(form));
}

const form = document.querySelector("#form-change-password");
form.onsubmit = function (e) {
  e.preventDefault();
  sendForm(form);
};
