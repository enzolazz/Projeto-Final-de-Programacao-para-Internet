const botoesDetalhes = document.querySelectorAll(".detalhes-btn");
botoesDetalhes.forEach((botao) => {
  botao.addEventListener("click", () => {
    window.location.href = "/detalhamento";
  });
});
