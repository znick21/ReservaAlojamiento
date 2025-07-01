<?php 
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../db.php");

// Clase Hostal
class Hostal {
    private $conexion;
    private $idAlojamiento;
    private $horaCheckIn;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function cargarPorId($id) {
        $this->idAlojamiento = $id;
        $stmt = $this->conexion->prepare("SELECT * FROM Hostal WHERE idAlojamiento = :id");
        $stmt->bindParam(":id", $this->idAlojamiento);
        $stmt->execute();
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registro) {
            $this->horaCheckIn = $registro['horaCheckIn'];
            return true;
        }
        return false;
    }

    public function actualizar($horaCheckIn) {
        $this->horaCheckIn = trim($horaCheckIn);
        $stmt = $this->conexion->prepare("UPDATE Hostal SET horaCheckIn = :horaCheckIn WHERE idAlojamiento = :id");
        $stmt->bindParam(":horaCheckIn", $this->horaCheckIn);
        $stmt->bindParam(":id", $this->idAlojamiento);
        return $stmt->execute();
    }

    public function getHoraCheckIn() {
        return $this->horaCheckIn;
    }

    public function getId() {
        return $this->idAlojamiento;
    }
}

// Inicializar
$hostalObj = new Hostal($conexion);

// Cargar datos para edición
if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];
    
    if (!$hostalObj->cargarPorId($txtID)) {
        header("Location: index.php?mensaje=" . urlencode("Hostal no encontrado"));
        exit();
    }
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $txtID = $_POST["txtID"];
    $horaCheckIn = $_POST["horaCheckIn"];

    $hostalObj->cargarPorId($txtID); // Confirmar existencia
    if ($hostalObj->actualizar($horaCheckIn)) {
        $mensaje = "Hostal actualizado correctamente";
        header("Location: index.php?mensaje=" . urlencode($mensaje));
        exit();
    } else {
        $error = "Error al actualizar el hostal.";
    }
}
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Editar Hostal</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="editar.php" method="post">
            <input type="hidden" name="txtID" value="<?php echo htmlspecialchars($hostalObj->getId()); ?>">

            <div class="mb-3">
                <label for="horaCheckIn" class="form-label">Hora de Check-In</label>
                <input type="text" class="form-control" id="horaCheckIn" name="horaCheckIn" 
                       value="<?php echo htmlspecialchars($hostalObj->getHoraCheckIn()); ?>" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

<!-- Mostrar error con SweetAlert2 si ocurre -->
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
