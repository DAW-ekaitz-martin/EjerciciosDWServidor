<?php
    session_start();
    require_once "funciones.php";
    comprobarSesion();
    require_once "db.php";

    // ---------- Filtros GET (vacío = todos) ----------
    $q = trim($_GET["q"] ?? '');
    $tallerFiltro = $_GET["taller"] ?? '';
    $errores = [];

    if ($tallerFiltro !== '' && !array_key_exists($tallerFiltro, $precios)) {
        $errores[] = "El taller indicado no está permitido.";
    }

    $filas = [];
    $numPlazas = 0;
    $precioTotal = 0;

    // Solo se consulta si los filtros son válidos
    if (count($errores) === 0) {
        // LIKE '%%' devuelve todas las filas, así que el filtro de nombre siempre puede ir
        $sql = "SELECT * FROM inscripciones WHERE nombre LIKE ?";
        $tipos = "s";
        $params = ["%" . $q . "%"];

        if ($tallerFiltro !== '') {
            $sql .= " AND taller = ?";
            $tipos .= "s";
            $params[] = $tallerFiltro;
        }
        $sql .= " ORDER BY id ASC";

        $stmt = $cn->prepare($sql);
        $stmt->bind_param($tipos, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($r = $res->fetch_assoc()) {
            // Si hubiera un taller fuera del catálogo en la BD, su precio cuenta como 0
            $precio = $precios[$r["taller"]] ?? 0;
            $r["importe"] = calcularImporte($precio, $r["plazas"]);
            $numPlazas += $r["plazas"];
            $precioTotal += $r["importe"];
            $filas[] = $r;
        }
    }

    $hayFiltros = ($q !== '' || $tallerFiltro !== '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripciones</title>
</head>
<body>
    <?php barraUsuario(); ?>
    <h1>Inscripciones existentes</h1>

    <!-- Un solo formulario: los dos filtros se combinan -->
    <form action="index.php" method="get">
        <label for="q">Nombre</label>
        <input type="text" name="q" id="q" value="<?= e($q) ?>">

        <label for="taller">Taller</label>
        <select name="taller" id="taller">
            <option value="">Todos</option>
<?php foreach ($precios as $clave => $precio): ?>
            <option value="<?= e($clave) ?>" <?= $tallerFiltro === $clave ? 'selected' : '' ?>><?= e(strtoupper($clave)) ?></option>
<?php endforeach; ?>
        </select>

        <button type="submit">Filtrar</button>
<?php if ($hayFiltros): ?>
        <a href="index.php">Limpiar filtros</a>
<?php endif; ?>
    </form>
    <hr>

<?php if ($errores): ?>
    <ul style="color:red">
    <?php foreach ($errores as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
<?php elseif (count($filas) === 0): ?>
    <p><?= $hayFiltros ? "No existen coincidencias para los filtros aplicados." : "No existen inscripciones en la base de datos." ?></p>
<?php else: ?>
    <table border="2" cellpadding="6">
        <tr>
            <th>ID</th><th>Nombre</th><th>Email</th><th>Taller</th><th>Turno</th><th>Plazas</th><th>Importe</th><th>Detalle</th>
        </tr>
    <?php foreach ($filas as $r): ?>
        <tr>
            <td><?= e($r["id"]) ?></td>
            <td><?= e($r["nombre"]) ?></td>
            <td><?= e($r["email"]) ?></td>
            <td><?= e($r["taller"]) ?></td>
            <td><?= e($r["turno"]) ?></td>
            <td><?= e($r["plazas"]) ?></td>
            <td><?= euros($r["importe"]) ?></td>
            <td><a href="ficha.php?id=<?= (int)$r["id"] ?>">Detalle</a></td>
        </tr>
    <?php endforeach; ?>
    </table><br>
    <p>Número de inscripciones: <?= count($filas) ?></p>
    <p>Número de plazas: <?= $numPlazas ?></p>
    <p>Importe total: <?= euros($precioTotal) ?></p>
<?php endif; ?>
</body>
</html>
