<?php
include("../../db.php");

// Procesar el formulario cuando se envíe
if ($_POST) {
    $idServicioHotel = $_POST["idServicioHotel"];
    $idAlojamiento = $_POST["idAlojamiento"];
    $servicio = $_POST["servicio"];

    // Insertar el nuevo servicio en la base de datos
    $stmt = $conexion->prepare("INSERT INTO ServicioHotel (idServicioHotel, idAlojamiento, servicio) VALUES (:idServicioHotel, :idAlojamiento, :servicio)");
    $stmt->bindParam(":idServicioHotel", $idServicioHotel);
    $stmt->bindParam(":idAlojamiento", $idAlojamiento);
    $stmt->bindParam(":servicio", $servicio);

    try {
        $stmt->execute();
        $mensaje = "Servicio creado correctamente";
        header("Location: index.php?mensaje=" . urlencode($mensaje));
        exit();
    } catch (PDOException $e) {
        $error = "Error al crear el servicio: " . $e->getMessage();
    }
}
?>

<?php include("../../templates/header.php"); ?>

<h2 class="text-center">Nuevo Servicio de Hotel</h2>

<div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="idServicioHotel" class="form-label">ID Servicio Hotel:</label>
                <input type="number" class="form-control" name="idServicioHotel" id="idServicioHotel" required>
            </div>

            <div class="mb-3">
                <label for="idAlojamiento" class="form-label">ID Alojamiento (Hotel):</label>
                <input type="number" class="form-control" name="idAlojamiento" id="idAlojamiento" required>
            </div>

            <div class="mb-3">
                <label for="servicio" class="form-label">Nombre del Servicio:</label>
                <input type="text" class="form-control" name="servicio" id="servicio" maxlength="100" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Crear Servicio
                </button>
            </div>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>
