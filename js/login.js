import { FormErrorHandler } from "./utils/errorHandler.js";

function sendForm(form) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", form.getAttribute("action"));
  xhr.responseType = "json";

  xhr.onload = function () {
    if (xhr.status != 200 || xhr.response === null) {
      console.log("A resposta não pode ser obtida");
      console.log(xhr.status);
      return;
    }

    const response = xhr.response;
    const validator = new FormErrorHandler(form);

    validator.clearAll();

    switch (response.status) {
      case "SUCCESS":
        window.location = response.newLocation;
        break;
      case "EMAIL_NOT_FOUND":
        validator.setError("email", "E-mail não encontrado.");
        form.senha.value = "";
        break;
      case "INCORRECT_PASSWORD":
        validator.setError("senha", "Senha incorreta");
        break;
      case "ERROR":
      default:
        alert("Erro inesperado. Tente novamente.");
    }
  };

  xhr.onerror = function () {
    console.error("Erro de rede - requisição não finalizada");
  };

  xhr.send(new FormData(form));
}

const form = document.querySelector("#formLogin");

form.onsubmit = function (e) {
  e.preventDefault();
  sendForm(form);
};
