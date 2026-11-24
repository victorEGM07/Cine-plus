
<?php
require_once "conexion.php";

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$editando = $id !== false && $id !== null && $id > 0;

$pelicula = [
    "titulo" => "",
    "genero" => "",
    "año" => "",
    "director" => "",
    "calificacion" => ""
];

if ($editando) {
    $stmt = $conexion->prepare(
        "SELECT titulo, genero, `año`, director, calificacion
         FROM peliculas WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();

    $stmt->close();

    if (!$datos) {
        header("Location: index.php");
        exit;
    }

    $pelicula = $datos;
}

$mensaje = $_GET["mensaje"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $editando ? "Editar película" : "Subir película" ?> | CinePlus</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #101018;
            color: white;
            font-family: Arial, sans-serif;
        }

        .navbar-cine {
            background: #1b1b27;
            border-bottom: 2px solid #c71932;
        }

        .marca {
            color: #ff405b;
            font-size: 26px;
            font-weight: bold;
            text-decoration: none;
        }

        .formulario {
            max-width: 750px;
            margin: 40px auto;
            padding: 28px;
            background: #20202b;
            border: 1px solid #353542;
            border-radius: 16px;
        }

        .form-control {
            background: #292936;
            color: white;
            border: 1px solid #4a4a59;
        }

        .form-control:focus {
            background: #292936;
            color: white;
            border-color: #e32140;
            box-shadow: 0 0 0 .2rem #e3214030;
        }

        .form-control::placeholder {
            color: #b7b7c4;
        }

        .form-label {
            color: #eeeeF4;
        }

        .btn-rojo {
            background: #e32140;
            color: white;
            border: none;
        }

        .btn-rojo:hover {
            background: #b91730;
            color: white;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark navbar-cine">
    <div class="container">
        <a class="marca" href="index.php">🎬 CinePlus</a>

        <a href="index.php" class="btn btn-outline-light">
            Volver al catálogo
        </a>
    </div>
</nav>

<main class="container">

    <section class="formulario">

        <h1 class="h3 mb-2">
            <?= $editando ? "Editar película" : "Subir película" ?>
        </h1>

        <p class="text-secondary mb-4">
            <?= $editando
                ? "Modifica los datos de la película seleccionada."
                : "Completa los datos para agregarla al catálogo." ?>
        </p>

        <?php if ($mensaje === "error"): ?>
            <div class="alert alert-danger">
                Revisa los datos ingresados. No se pudo guardar la película.
            </div>
        <?php endif; ?>

        <form
            action="<?= $editando ? "actualizar.php" : "guardar.php" ?>"
            method="POST"
        >

            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= (int)$id ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>

                <input
                    type="text"
                    class="form-control"
                    id="titulo"
                    name="titulo"
                    maxlength="150"
                    value="<?= e($pelicula["titulo"]) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="genero" class="form-label">Género</label>

                <input
                    type="text"
                    class="form-control"
                    id="genero"
                    name="genero"
                    maxlength="80"
                    value="<?= e($pelicula["genero"]) ?>"
                    placeholder="Ejemplo: Acción, comedia, drama..."
                    required
                >
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label for="anio" class="form-label">
                        Año de estreno
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="anio"
                        name="año"
                        min="1888"
                        max="2100"
                        value="<?= e($pelicula["año"]) ?>"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="calificacion" class="form-label">
                        Calificación (0 a 10)
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="calificacion"
                        name="calificacion"
                        min="0"
                        max="10"
                        step="0.1"
                        value="<?= e($pelicula["calificacion"]) ?>"
                        required
                    >
                </div>

            </div>

            <div class="mb-4">
                <label for="director" class="form-label">
                    Director
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="director"
                    name="director"
                    maxlength="120"
                    value="<?= e($pelicula["director"]) ?>"
                    required
                >
            </div>

            <div class="d-flex flex-wrap gap-2">

                <button type="submit" class="btn btn-rojo">
                    <?= $editando ? "Guardar cambios" : "Registrar película" ?>
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Cancelar
                </a>

            </div>

        </form>

    </section>

</main>

</body>
</html>
