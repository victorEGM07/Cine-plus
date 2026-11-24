
<?php
require_once "conexion.php";

function e($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        "UTF-8"
    );
}

function mostrarCalificacion($valor) {
    return rtrim(
        rtrim(number_format((float)$valor, 1, '.', ''), '0'),
        '.'
    );
}

$busqueda = trim($_GET["buscar"] ?? "");

if ($busqueda !== "") {
    $sql = "SELECT id, titulo, genero, `año`, director, calificacion
            FROM peliculas
            WHERE titulo LIKE ?
               OR genero LIKE ?
               OR director LIKE ?
            ORDER BY id DESC";

    $stmt = $conexion->prepare($sql);
    $texto = "%" . $busqueda . "%";
    $stmt->bind_param("sss", $texto, $texto, $texto);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexion->query(
        "SELECT id, titulo, genero, `año`, director, calificacion
         FROM peliculas
         ORDER BY id DESC"
    );
}

$total = $resultado->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CinePlus | Catálogo de películas</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #101018;
            color: #f5f5f5;
            font-family: Arial, sans-serif;
            min-height: 100vh;
        }

        .navbar-cine {
            background: #1b1b27;
            border-bottom: 2px solid #c71932;
        }

        .marca {
            color: #ff304f !important;
            font-size: 27px;
            font-weight: bold;
        }

        .hero {
            background: linear-gradient(135deg, #25202c, #15151e);
            border-radius: 18px;
            padding: 35px;
            margin-top: 30px;
            margin-bottom: 25px;
        }

        .texto-rojo {
            color: #ff405b;
        }

        .form-control {
            background: #292936;
            border: 1px solid #494958;
            color: white;
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

        .form-select {
            background-color: #292936;
            border-color: #494958;
            color: white;
        }

        .form-select:focus {
            background-color: #292936;
            border-color: #e32140;
            color: white;
            box-shadow: 0 0 0 .2rem #e3214030;
        }

        .pelicula {
            background: #20202b;
            border: 1px solid #333341;
            border-radius: 14px;
            height: 100%;
            transition: transform .2s, border-color .2s;
        }

        .pelicula:hover {
            transform: translateY(-5px);
            border-color: #c71932;
        }

        .pelicula-cuerpo {
            padding: 22px;
        }

        .genero {
            display: inline-block;
            background: #3a2029;
            color: #ff8293;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
        }

        .calificacion {
            color: #ffd166;
            font-weight: bold;
            font-size: 18px;
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

        .seccion-titulo {
            border-left: 4px solid #e32140;
            padding-left: 12px;
        }

        .sin-resultados {
            background: #20202b;
            border-radius: 12px;
            padding: 35px;
            text-align: center;
        }

        footer {
            color: #9292a0;
            border-top: 1px solid #30303a;
            margin-top: 50px;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-cine">
    <div class="container">
        <a class="navbar-brand marca" href="index.php">
            🎬 CinePlus
        </a>

        <a href="editar.php" class="btn btn-rojo">
            + Subir película
        </a>
    </div>
</nav>

<main class="container">

    <section class="hero">
        <h1 class="fw-bold">
            Tu cine, <span class="texto-rojo">a tu manera.</span>
        </h1>

        <p class="text-secondary mb-4">
            Explora, consulta y administra tu colección de películas.
        </p>

        <form action="index.php" method="GET" id="formulario-busqueda">
            <div class="row g-2">
                <div class="col-md-9">
                    <input
                        type="search"
                        name="buscar"
                        id="busqueda-peliculas"
                        class="form-control form-control-lg"
                        placeholder="Buscar por título, género o director..."
                        value="<?= e($busqueda) ?>"
                    >
                </div>

                <div class="col-md-3 d-grid">
                    <button class="btn btn-rojo btn-lg" type="submit">
                        Buscar películas
                    </button>
                </div>
            </div>
        </form>
        <small class="text-secondary d-block mt-2">
            Al escribir se filtra la lista actual; pulsa “Buscar películas” para consultar el catálogo.
        </small>
    </section>

    <div class="d-flex justify-content-between align-items-center
                flex-wrap gap-2 mb-4">

        <h2 class="seccion-titulo h4 mb-0">
            <?= $busqueda !== "" ? "Resultados de búsqueda" : "Catálogo de películas" ?>
        </h2>

        <span class="text-secondary" id="contador-peliculas" aria-live="polite">
            <?= $total ?> película(s)
        </span>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <label for="orden-peliculas" class="text-secondary">Ordenar por:</label>
        <select id="orden-peliculas" class="form-select w-auto">
            <option value="recientes">Más recientes</option>
            <option value="calificacion">Mejor calificadas</option>
            <option value="anio">Año más reciente</option>
            <option value="titulo">Título A–Z</option>
        </select>

        <button
            type="button"
            class="btn btn-outline-warning"
            id="filtro-favoritas"
            aria-pressed="false"
        >
            ☆ Solo favoritas
        </button>
    </div>

    <div class="row g-4" id="catalogo-peliculas">

        <?php if ($total > 0): ?>

            <?php while ($p = $resultado->fetch_assoc()): ?>

                <div
                    class="col-12 col-sm-6 col-lg-4 col-xl-3"
                    data-pelicula
                    data-id="<?= (int)$p["id"] ?>"
                    data-titulo="<?= e($p["titulo"]) ?>"
                    data-genero="<?= e($p["genero"]) ?>"
                    data-director="<?= e($p["director"]) ?>"
                    data-anio="<?= (int)$p["año"] ?>"
                    data-calificacion="<?= e($p["calificacion"]) ?>"
                >

                    <article class="pelicula">

                        <div class="pelicula-cuerpo">

                            <span class="genero">
                                <?= e($p["genero"]) ?>
                            </span>

                            <h3 class="h5 fw-bold mt-3 mb-2">
                                <?= e($p["titulo"]) ?>
                            </h3>

                            <p class="text-secondary mb-2">
                                Año: <?= e($p["año"]) ?>
                            </p>

                            <p class="mb-3">
                                <span class="text-secondary">
                                    Director:
                                </span>
                                <?= e($p["director"]) ?>
                            </p>

                            <p class="calificacion mb-4">
                                ★ <?= mostrarCalificacion($p["calificacion"]) ?>/10
                            </p>

                            <div class="d-flex gap-2">

                                <button
                                    type="button"
                                    class="btn btn-outline-warning btn-sm boton-favorita"
                                    aria-label="Agregar <?= e($p["titulo"]) ?> a favoritas"
                                    title="Agregar a favoritas"
                                >
                                    ☆
                                </button>

                                <a
                                    href="editar.php?id=<?= (int)$p["id"] ?>"
                                    class="btn btn-warning btn-sm flex-fill"
                                >
                                    Editar
                                </a>

                                <form
                                    action="eliminar.php"
                                    method="POST"
                                    class="flex-fill"
                                    data-confirmar-eliminacion
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$p["id"] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm w-100"
                                    >
                                        Eliminar
                                    </button>
                                </form>

                            </div>

                        </div>

                    </article>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12">
                <div class="sin-resultados" id="estado-vacio">
                    <h3 class="h5">No se encontraron películas</h3>

                    <p class="text-secondary">
                        Prueba con otro título, género o director.
                    </p>

                    <a href="index.php" class="btn btn-outline-light">
                        Ver todo el catálogo
                    </a>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <footer>
        <p class="mb-1">🎬 CinePlus</p>
        <small>Tu entretenimiento, tu película.</small>
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/cineplus.js" defer></script>

</body>
</html>
<?php
if (isset($stmt)) {
    $stmt->close();
}
$conexion->close();
?>
