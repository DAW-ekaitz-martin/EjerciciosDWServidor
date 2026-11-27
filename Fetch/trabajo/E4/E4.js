"use strict";
const boton = document.getElementById("pedir");
const salida = document.getElementById("salida");


async function llamar() {
    const r = await fetch('../api/saludo.php');
    if(!r.ok) throw new Error("No se pudo pedir el saludo");
    const texto = await r.text();
    salida.textContent = texto;
}
boton.addEventListener("click", llamar);
