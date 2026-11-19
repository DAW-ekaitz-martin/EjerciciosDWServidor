const boton = document.getElementById("buscar");
const estado = document.getElementById("estado");
const lista = document.getElementById("lista");
const input = document.getElementById("q");

async function llamar() {
    try {
        lista.textContent = "";
        estado.textContent="Cargando";
        boton.disabled = true;
        const nombre = input.value;
        const  parametro = new URLSearchParams({q:nombre});
        const r = await fetch(`../api/alumnos2.php?${parametro}`);
        if(!r.ok) throw new Error("No se pudo pedir el saludo");
        const cuerpo = await r.json();
        if(cuerpo.datos.length > 0) {estado.textContent="Consulta completada";}
        else {
            estado.textContent="Sin resultados";
        }
        lista.replaceChildren();
        for(alumno of cuerpo.datos) {
            const li = document.createElement("li");
            li.textContent = alumno.nombre;
            lista.append(li);
        }
    } catch (error) {
        lista.textContent = "";
        estado.textContent = " No se pudieron cargar los alumnos";
    } finally {
        boton.disabled = false;
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
