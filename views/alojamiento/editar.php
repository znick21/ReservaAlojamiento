<?php
include("../../db.php");

if (!isset($_GET['txtID'])) {
    header("Location: index.php");
    exit();
}

$idAlojamiento = $_GET['txtID'];

// Obtener los datos actuales del alojamiento
$stmt = $conexion->prepare("SELECT * FROM Alojamiento WHERE idAlojamiento = :id");
$stmt->bindParam(":id", $idAlojamiento);
$stmt->execute();
$alojamiento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$alojamiento) {
    header("Location: index.php");
    exit();
}

// Procesar el formulario de actualización
if ($_POST) {
    $nombre = $_POST["nombre"];
    $tipo = $_POST["tipo"];
    $precioPorNoche = $_POST["precioPorNoche"];
    $capacidad = $_POST["capacidad"];

    $stmt = $conexion->prepare("UPDATE Alojamiento SET nombre = :nombre, tipo = :tipo, precioPorNoche = :precioPorNoche, capacidad = :capacidad WHERE idAlojamiento = :id");
    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":tipo", $tipo);
    $stmt->bindParam(":precioPorNoche", $precioPorNoche);
    $stmt->bindParam(":capacidad", $capacidad);
    $stmt->bindParam(":id", $idAlojamiento);

    try {
        $stmt->execute();
        $mensaje = "Alojamiento actualizado correctamente";
        header("Location: index.php?mensaje=" . urlencode($mensaje));
        exit();
    } catch (PDOException $e) {
        $error = "Error al actualizar el alojamiento: " . $e->getMessage();
    }
}
?>

<?php include("../../templates/header.php"); ?>

<h2 class="text-center">Editar Alojamiento</h2>

<div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="idAlojamiento" class="form-label">ID Alojamiento:</label>
                <input type="number" class="form-control" id="idAlojamiento" value="<?php echo $alojamiento['idAlojamiento']; ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" required value="<?php echo htmlspecialchars($alojamiento['nombre']); ?>">
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo:</label>
                <input type="text" class="form-control" name="tipo" id="tipo" maxlength="50" required value="<?php echo htmlspecialchars($alojamiento['tipo']); ?>">
            </div>

            <div class="mb-3">
                <label for="precioPorNoche" class="form-label">Precio por Noche:</label>
                <input type="number" step="0.01" min="0" class="form-control" name="precioPorNoche" id="precioPorNoche" required value="<?php echo $alojamiento['precioPorNoche']; ?>">
            </div>

            <div class="mb-3">
                <label for="capacidad" class="form-label">Capacidad:</label>
                <input type="number" min="1" class="form-control" name="capacidad" id="capacidad" required value="<?php echo $alojamiento['capacidad']; ?>">
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>
