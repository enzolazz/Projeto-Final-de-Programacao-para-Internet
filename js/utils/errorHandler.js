export class FormErrorHandler {
  constructor(form) {
    this.form = form;
  }

  setError(fieldName, message) {
    const formGroup = this.form[fieldName].parentNode;
    const input = formGroup.querySelector("input");
    const label = formGroup.querySelector("label");

    if (!input || !label) throw new Error("Failed to find form fields.");

    this.clearError(fieldName);

    if (message) {
      input.classList.add("input-error");
      label.classList.add("label-error");

      const errorElement = document.createElement("p");
      errorElement.classList.add("error-msg");
      errorElement.textContent = message;
      formGroup.appendChild(errorElement);

      input.value = "";

      return input;
    }
  }

  clearError(fieldName) {
    const formGroup = this.form[fieldName].parentNode;
    formGroup
      .querySelectorAll(".input-error, .label-error")
      .forEach((elemento) =>
        elemento.classList.remove("input-error", "label-error"),
      );
    formGroup
      .querySelectorAll(".error-msg")
      .forEach((elemento) => elemento.remove());
  }

  clearAll() {
    this.form
      .querySelectorAll(".input-error, .label-error")
      .forEach((elemento) =>
        elemento.classList.remove("input-error", "label-error"),
      );
    this.form
      .querySelectorAll(".error-msg, .form-error-msg")
      .forEach((elemento) => elemento.remove());
  }
}
