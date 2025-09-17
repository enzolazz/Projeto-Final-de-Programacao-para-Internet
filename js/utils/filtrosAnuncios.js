export class FiltrosAnuncio {
  constructor() {
    this.marca = null;
    this.modelo = null;
    this.cidade = null;
    this.offset = 0;
    this.limit = 20;
  }

  setFiltro(campo, valor) {
    if (this.hasOwnProperty(campo)) {
      this[campo] = valor;
      if (["marca", "modelo", "cidade"].includes(campo)) this.offset = 0;
    }
  }

  toFormData() {
    const formData = new FormData();
    for (const key in this) {
      if (this[key] !== null) formData.append(key, this[key]);
    }
    return formData;
  }
}

export function carregarOpcoes(select, titulo, dados) {
  select.innerHTML = "";

  const placeHolder = document.createElement("option");
  placeHolder.value = "";
  placeHolder.textContent = titulo;
  placeHolder.disabled = true;
  placeHolder.selected = true;
  select.appendChild(placeHolder);

  dados.forEach((opcao) => {
    const option = document.createElement("option");
    option.textContent = opcao;
    option.value = opcao;

    select.appendChild(option);
  });
}
