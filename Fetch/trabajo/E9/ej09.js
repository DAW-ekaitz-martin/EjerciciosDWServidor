const formulario = document.getElementById("formulario");
const nombre = document.getElementById("name");
const email = document.getElementById("email");
const parrafo = document.getElementById("estado");
const boton = document.getElementById("enviar");

/*De esta manera lo haría si el botón del formulario no fuese de tipo submit.
async function enviar() {}
boton.addEventListener("click", enviar);*/
//Si el botón del form es type=submit, tengo que poner el addEventListener al formulario y hacer que el evento(el submit) no se ejecute:
formulario.addEventListener("submit", async(evento) => {
    evento.preventDefault();
    const cuerpo = new FormData(formulario);
    const r = await fetch('../api/ecoform.php', {
        method:'POST',
        body:cuerpo,
    });
});