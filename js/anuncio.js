import { FormErrorHandler } from "./utils/errorHandler.js";

function sendForm(form) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", form.getAttribute("action"));
  xhr.responseType = "json";

  const validator = new FormErrorHandler(form);

  validator.clearAll();

  xhr.onload = () => {
    const resposta = xhr.response;
    if (resposta === null) {
      console.error("Resposta inválida do servidor.");
      console.log(resposta);
      return;
    }
    if (xhr.status === 200 && resposta.sucesso) {
      window.location.href = resposta.redirect;
    } else if (xhr.status === 400 && resposta.erros) {
      let firstErrorField = null;

      for (let [campo, mensagem] of Object.entries(resposta.erros)) {
        campo = campo === "fotos" ? "fotos[]" : campo;

        const input = validator.setError(campo, mensagem);

        if (!firstErrorField) firstErrorField = input;
      }

      if (firstErrorField) firstErrorField.focus();
    } else {
      console.error("Erro inesperado:", resposta.erro || resposta.erros);
      alert(resposta.erro || "Erro inesperado. Tente novamente.");
    }
  };

  xhr.onerror = function () {
    console.error("Erro de rede - requisição não finalizada");
    alert("Erro de rede. Tente novamente.");
  };

  xhr.send(new FormData(form));
}

const form = document.querySelector("#add-ad-form");
form.onsubmit = function (e) {
  e.preventDefault();
  sendForm(form);
};
