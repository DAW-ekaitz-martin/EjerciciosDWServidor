"use strict";
const boton = document.getElementById("cargar");
const estado = document.getElementById("estado");
const lista = document.getElementById("lista");

async function llamar() {
    try {
        const r = await fetch('../api/alumnos.php');
        if(!r.ok) throw new Error("No se pudo pedir el saludo");
        const cuerpo = await r.json();
        lista.replaceChildren();
        for(alumno of cuerpo.datos) {
            const li = document.createElement("li");
            li.textContent = alumno.nombre;
            lista.append(li);
        }
    } catch (error) {
        estado.textContent = error;
    }
    
}

/*async function llamar() {
    boton.disabled = true;
    estado.textContent = 'Cargando...';
    try {
        const r = await fetch('../api/alumnos.php');
        if(!r.ok) throw new Error("No se pudo pedir el saludo");
        const cuerpo = await r.json();
        lista.replaceChildren();
        for(alumno of cuerpo.datos) {
            const li = document.createElement("li");
            li.textContent = alumno.nombre;
            lista.append(li);
        }
        
        estado.textContent = '';
    } catch (error) {
        estado.textContent = error;
    }finally {
        boton.disabled = false;
    } 
}*/
boton.addEventListener("click", llamar);
