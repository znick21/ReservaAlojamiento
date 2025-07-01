<?php 
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../db.php");

$error = "";
$mensaje = "";

// Clase para insertar un Hostal
class Hostal {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function insertar($idAlojamiento, $horaCheckIn) {
        // Validar si el alojamiento ya está registrado como Hostal
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM Hostal WHERE idAlojamiento = :id");
        $stmt->bindParam(":id", $idAlojamiento);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return "El alojamiento ya está registrado como hostal.";
        }

        // Insertar nuevo hostal
        $stmt = $this->conexion->prepare("INSERT INTO Hostal (idAlojamiento, horaCheckIn) VALUES (:id, :hora)");
        $stmt->bindParam(":id", $idAlojamiento);
        $stmt->bindParam(":hora", $horaCheckIn);
        if ($stmt->execute()) {
            return true;
        }
        return "Error al insertar el hostal.";
    }

    public function obtenerAlojamientosDisponibles() {
        // Filtrar los alojamientos que no están registrados en Hostal
        $stmt = $this->conexion->prepare("
            SELECT idAlojamiento FROM Alojamiento 
            WHERE idAlojamiento NOT IN (SELECT idAlojamiento FROM Hostal)
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$hostalObj = new Hostal($conexion);

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idAlojamiento = $_POST["idAlojamiento"];
    $horaCheckIn = trim($_POST["horaCheckIn"]);

    $resultado = $hostalObj->insertar($idAlojamiento, $horaCheckIn);
    if ($resultado === true) {
        header("Location: index.php?mensaje=" . urlencode("Hostal creado correctamente"));
        exit();
    } else {
        $error = $resultado;
    }
}

$alojamientosDisponibles = $hostalObj->obtenerAlojamientosDisponibles();
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Registrar Nuevo Hostal</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="crear.php" method="post">
            <div class="mb-3">
                <label for="idAlojamiento" class="form-label">ID del Alojamiento</label>
                <select class="form-select" name="idAlojamiento" id="idAlojamiento" required>
                    <option value="">-- Selecciona un alojamiento --</option>
                    <?php foreach ($alojamientosDisponibles as $alojamiento): ?>
                        <option value="<?php echo $alojamiento["idAlojamiento"]; ?>">
                            <?php echo $alojamiento["idAlojamiento"]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="horaCheckIn" class="form-label">Hora de Check-In</label>
                <input type="text" class="form-control" id="horaCheckIn" name="horaCheckIn" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Crear Hostal</button>
            </div>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>
