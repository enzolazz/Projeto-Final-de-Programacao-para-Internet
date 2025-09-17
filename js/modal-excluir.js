export function buttonExclude(botao, url, id) {
  botao.addEventListener("click", (event) => {
    event.preventDefault();

    const modal = document.getElementById("modal-excluir");
    const confirmarBtn = document.getElementById("confirmar-excluir");
    const cancelarBtn = document.getElementById("cancelar-excluir");

    modal.style.display = "block";
    document.body.classList.add("stop-scrolling");

    const anuncio_interesse =
      event.target.closest(".anuncio") || event.target.closest(".interesse");

    confirmarBtn.onclick = async () => {
      try {
        let formData = new FormData();
        formData.append("id", id);

        const response = await fetch(url, {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (!response.ok || anuncio_interesse === null) {
          throw new Error(data.erro || "Erro ao excluir");
        }

        anuncio_interesse.remove();

        const contadorTotal = document.querySelector(".total");
        if (
          contadorTotal &&
          anuncio_interesse.classList.contains("interesse")
        ) {
          const novoTotal = parseInt(contadorTotal.textContent) - 1;
          contadorTotal.textContent = novoTotal;
        }

        modal.style.display = "none";
        document.body.classList.remove("stop-scrolling");
      } catch (error) {
        console.error("Erro ao excluir:", error);
      }
    };

    cancelarBtn.onclick = () => {
      modal.style.display = "none";
      document.body.classList.remove("stop-scrolling");
    };
  });
}
