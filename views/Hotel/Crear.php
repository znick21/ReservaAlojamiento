<?php 
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../db.php");

// Obtener alojamientos disponibles (que no están ya registrados como hoteles)
$sentencia = $conexion->prepare("SELECT idAlojamiento FROM Alojamiento WHERE idAlojamiento NOT IN (SELECT idAlojamiento FROM Hotel)");
$sentencia->execute();
$alojamientos_disponibles = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Insertar nuevo hotel
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idAlojamiento = $_POST["idAlojamiento"];
    $estrellas = $_POST["estrellas"];
    $telefono = trim($_POST["telefonoContacto"]);

    if ($idAlojamiento && $estrellas && $telefono) {
        $stmt = $conexion->prepare("INSERT INTO Hotel (idAlojamiento, estrellas, telefonoContacto) VALUES (:id, :estrellas, :telefono)");
        $stmt->bindParam(":id", $idAlojamiento);
        $stmt->bindParam(":estrellas", $estrellas);
        $stmt->bindParam(":telefono", $telefono);

        if ($stmt->execute()) {
            header("Location: index.php?mensaje=" . urlencode("Hotel creado correctamente"));
            exit();
        } else {
            $error = "Error al crear el hotel.";
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Registrar Nuevo Hotel</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="crear.php">
            <div class="mb-3">
                <label for="idAlojamiento" class="form-label">Alojamiento</label>
                <select name="idAlojamiento" id="idAlojamiento" class="form-select" required>
                    <option value="">-- Selecciona un alojamiento --</option>
                    <?php foreach ($alojamientos_disponibles as $alojamiento): ?>
                        <option value="<?php echo $alojamiento['idAlojamiento']; ?>">
                            <?php echo "Alojamiento #" . $alojamiento['idAlojamiento']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="estrellas" class="form-label">Estrellas</label>
                <input type="number" class="form-control" name="estrellas" id="estrellas" min="1" max="5" required>
            </div>

            <div class="mb-3">
                <label for="telefonoContacto" class="form-label">Teléfono de Contacto</label>
                <input type="text" class="form-control" name="telefonoContacto" id="telefonoContacto" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar Hotel</button>
            </div>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($error)) : ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo htmlspecialchars($error); ?>',
        confirmButtonColor: '#d33'
    });
</script>
<?php endif; ?>
