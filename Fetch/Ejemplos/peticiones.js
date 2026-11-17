"use strict";
class ErrorPeticion extends Error {
  constructor(tipo, mensaje) { super(mensaje); this.tipo = tipo; }
}

function estado(id, tipo, mensaje) {
  $(id).dataset.estado = tipo;
  $(id).textContent = mensaje;
}

// Una respuesta HTTP 404/500 no rechaza por sí sola la promesa de fetch.
async function pedirJSON(url, opciones = {}) {
  let respuesta;
  try {
    respuesta = await fetch(url, { credentials: "same-origin", ...opciones });
  } catch (error) {
    if (error.name === "AbortError") throw error;
    throw new ErrorPeticion("Red", "No se pudo completar la petición. Revisa conexión, URL y Network.");
  }
  let cuerpo;
  try {
    cuerpo = await respuesta.json();
  } catch (error) {
    if (error.name === "AbortError") throw error;
    if (!respuesta.ok) throw new ErrorPeticion("HTTP", `${respuesta.status}. La respuesta de error no es JSON válido.`);
    if (!(error instanceof SyntaxError)) {
      throw new ErrorPeticion("Red", "La lectura de la respuesta se interrumpió. Revisa la conexión y Network.");
    }
    throw new ErrorPeticion("JSON", "PHP respondió, pero el cuerpo no contiene JSON válido.");
  }
  if (!respuesta.ok) {
    let mensaje = cuerpo?.error?.mensaje ?? "La petición falló.";
    if (cuerpo?.error?.campos) mensaje += " " + Object.values(cuerpo.error.campos).join(" ");
    throw new ErrorPeticion("HTTP", `${respuesta.status}. ${mensaje}`);
  }
  if (cuerpo?.ok !== true) {
    throw new ErrorPeticion("Aplicación", cuerpo?.error?.mensaje ?? "El servidor no confirmó la operación.");
  }
  return cuerpo.datos;
}

