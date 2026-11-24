
<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$titulo = trim($_POST["titulo"] ?? "");
$genero = trim($_POST["genero"] ?? "");
$director = trim($_POST["director"] ?? "");

$anio = filter_var(
    $_POST["año"] ?? "",
    FILTER_VALIDATE_INT
);

$calificacion = $_POST["calificacion"] ?? "";

if (
    $titulo === "" ||
    mb_strlen($titulo) > 150 ||
    $genero === "" ||
    mb_strlen($genero) > 80 ||
    $director === "" ||
    mb_strlen($director) > 120 ||
    $anio === false ||
    $anio === null ||
    $anio < 1888 ||
    $anio > 2100 ||
    $calificacion === "" ||
    !is_numeric($calificacion) ||
    !is_finite((float)$calificacion) ||
    (float)$calificacion < 0 ||
    (float)$calificacion > 10 ||
    !preg_match('/^\d{1,2}(\.\d)?$/', (string)$calificacion)
) {
    header("Location: editar.php?mensaje=error");
    exit;
}

$calificacion = (float)$calificacion;

$stmt = $conexion->prepare(
    "INSERT INTO peliculas
    (titulo, genero, `año`, director, calificacion)
    VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssisd",
    $titulo,
    $genero,
    $anio,
    $director,
    $calificacion
);

if ($stmt->execute()) {
    $stmt->close();
    $conexion->close();

    header("Location: index.php?mensaje=guardada");
    exit;
}

$stmt->close();
$conexion->close();

header("Location: editar.php?mensaje=error");
exit;
?>
