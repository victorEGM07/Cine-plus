
<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

$titulo = trim($_POST["titulo"] ?? "");
$genero = trim($_POST["genero"] ?? "");
$director = trim($_POST["director"] ?? "");

$anio = filter_var(
    $_POST["año"] ?? "",
    FILTER_VALIDATE_INT
);

$calificacion = $_POST["calificacion"] ?? "";

if (
    $id === false || $id === null || $id < 1 ||
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
    header("Location: index.php");
    exit;
}

$calificacion = (float)$calificacion;

$stmt = $conexion->prepare(
    "UPDATE peliculas
     SET titulo = ?,
         genero = ?,
         `año` = ?,
         director = ?,
         calificacion = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "ssisdi",
    $titulo,
    $genero,
    $anio,
    $director,
    $calificacion,
    $id
);

$guardado = $stmt->execute();

$stmt->close();
$conexion->close();

header("Location: index.php?mensaje=" . ($guardado ? "guardada" : "error_guardar"));
exit;
?>
