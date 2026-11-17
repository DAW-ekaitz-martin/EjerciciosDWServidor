"use strict";

const elemento = (id) => document.getElementById(id);

function mostrarEstado(id, estado, mensaje) {
  elemento(id).dataset.estado = estado;
  elemento(id).textContent = mensaje;
}

elemento("pedir-saludo").addEventListener("click", async (evento) => {
  const boton = evento.currentTarget;
  boton.disabled = true;
  elemento("saludo").textContent = "";
  mostrarEstado("estado-saludo", "cargando", "Esperando respuesta de PHP…");
  try {
    const respuesta = await fetch("api/saludo.php");
    if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
    const texto = await respuesta.text();
    elemento("saludo").textContent = texto;
    mostrarEstado("estado-saludo", "exito", "Texto recibido sin recargar la página.");
  } catch (error) {
    mostrarEstado("estado-saludo", "error", `No se pudo obtener el saludo. ${error.message}`);
  } finally {
    boton.disabled = false;
  }
});

let contador = 0;
elemento("contar-local").addEventListener("click", () => {
  contador += 1;
  elemento("contar-local").textContent = `Contador local: ${contador}`;
});

elemento("pedir-respuesta").addEventListener("click", async (evento) => {
  const boton = evento.currentTarget;
  boton.disabled = true;
  elemento("respuesta").textContent = "";
  mostrarEstado("estado-respuesta", "cargando", "Esperando… Puedes seguir escribiendo y usar el contador.");
  try {
    const parametros = new URLSearchParams({ ms: elemento("retardo").value });
    const respuesta = await fetch(`api/lento.php?${parametros}`);
    if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
    const cuerpo = await respuesta.json();
    if (cuerpo.ok !== true) throw new Error("El servidor no confirmó la respuesta.");
    elemento("respuesta").textContent = `${cuerpo.datos.mensaje} Retardo aplicado: ${cuerpo.datos.espera_ms} ms.`;
    mostrarEstado("estado-respuesta", "exito", "Respuesta recibida. El campo y el contador conservan sus valores.");
  } catch (error) {
    mostrarEstado("estado-respuesta", "error", `No se pudo obtener la respuesta. ${error.message}`);
  } finally {
    boton.disabled = false;
  }
});
