<?php
include("../../db.php");

// Obtener ID del servicio desde la URL
if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];

    // Obtener datos del servicio
    $stmt = $conexion->prepare("SELECT * FROM ServicioHotel WHERE idServicioHotel = :id");
    $stmt->bindParam(":id", $txtID);
    $stmt->execute();
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        $idAlojamiento = $registro["idAlojamiento"];
        $servicio = $registro["servicio"];
    } else {
        header("Location: index.php?mensaje=" . urlencode("Servicio no encontrado."));
        exit();
    }
}

// Actualizar datos al enviar el formulario
if ($_POST) {
    $idServicioHotel = $_POST["idServicioHotel"];
    $idAlojamiento = $_POST["idAlojamiento"];
    $servicio = $_POST["servicio"];

    $stmt = $conexion->prepare("UPDATE ServicioHotel SET idAlojamiento = :idAlojamiento, servicio = :servicio WHERE idServicioHotel = :idServicioHotel");
    $stmt->bindParam(":idAlojamiento", $idAlojamiento);
    $stmt->bindParam(":servicio", $servicio);
    $stmt->bindParam(":idServicioHotel", $idServicioHotel);
    $stmt->execute();

    $mensaje = "Servicio actualizado correctamente";
    header("Location: index.php?mensaje=" . urlencode($mensaje));
    exit();
}
?>

<?php include("../../templates/header.php"); ?>

<h2 class="text-center">Editar Servicio de Hotel</h2>

<div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="idServicioHotel" value="<?php echo htmlspecialchars($txtID); ?>">

            <div class="mb-3">
                <label for="idAlojamiento" class="form-label">ID Alojamiento:</label>
                <input type="number" class="form-control" name="idAlojamiento" id="idAlojamiento" value="<?php echo htmlspecialchars($idAlojamiento); ?>" required>
            </div>

            <div class="mb-3">
                <label for="servicio" class="form-label">Nombre del Servicio:</label>
                <input type="text" class="form-control" name="servicio" id="servicio" value="<?php echo htmlspecialchars($servicio); ?>" maxlength="100" required>
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
