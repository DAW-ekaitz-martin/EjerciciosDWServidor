# Pruebas con Fetch y PHP

1. Copia el contenido de esta carpeta a `C:/xampp/htdocs/tema4_pruebas`. Conserva `index.html`, `app.js` y la carpeta `api` juntos.
2. Inicia Apache en XAMPP con PHP 8 o posterior.
3. Abre `http://localhost/tema4_pruebas/`. Si Apache usa otro puerto, inclúyelo en la URL.
4. Abre Network/Red en las herramientas del navegador y pulsa los botones de la página.

PHP necesita un servidor HTTP. Abrir `index.html` con doble clic mediante `file://` no ejecuta los endpoints PHP.

El campo «Retardo (ms)» es un parámetro técnico de la petición a `api/lento.php`: determina la espera del servidor entre 0 y 2000 milisegundos.
