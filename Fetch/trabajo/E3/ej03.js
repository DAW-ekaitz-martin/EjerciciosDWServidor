"use strict";
const boton = document.getElementById("pedir");
const salida = document.getElementById("salida");


boton.addEventListener("click", () => {
    fetch('../api/saludo.php')
    .then(respuesta => respuesta.text())
    .then(texto => salida.textContent=texto)
    .catch(error => console.error(error));
});
