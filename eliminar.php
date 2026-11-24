
<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $conexion->prepare(
    "DELETE FROM peliculas WHERE id = ?"
);

$stmt->bind_param("i", $id);
$eliminado = $stmt->execute() && $stmt->affected_rows > 0;

$stmt->close();
$conexion->close();

header("Location: index.php?mensaje=" . ($eliminado ? "eliminada" : "error_eliminar"));
exit;
?>

