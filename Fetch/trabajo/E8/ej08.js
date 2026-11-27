"use strict";
const boton = document.getElementById("boton");
const select = document.getElementById("caso");
const estado = document.getElementById("estado");
const lista = document.getElementById("lista");

async function probar() {
    try {
        lista.textContent = "";
        estado.textContent = "Cargando...";
        boton.disabled = true;
        let r = null;
        if(select.value === "correcto") {
            r = await fetch('../api/alumnos.php');
        }
        else if(select.value === "errorHttp") {
            r = await fetch("../api/fallo.php?tipo=http");
        }
        else if(select.value === "jsonRoto") {
            r = await fetch("../api/fallo.php?tipo=json");
        }
        else {
            r = await fetch("../api/fallo.php?tipo=app");
        }
        if(!r.ok) {throw new Error(`HTTP ${r.status}`)};
        const cuerpo = await r.json();
        if(cuerpo.datos.length > 0) {
            lista.replaceChildren();
            for(alumno of cuerpo.datos) {
                const li = document.createElement("li");
                li.textContent = alumno.nombre;
                lista.append(li);
            }
        } else {estado.textContent = "JSON no válido"};
        if(r.ok) {estado.textContent = "Consulta completada"}
    } catch (error) {
        if(!r.ok) {estado.textContent = "Operación rechazada";}  
    } finally {
        boton.disabled=false;
    }
}
boton.addEventListener("click", probar);