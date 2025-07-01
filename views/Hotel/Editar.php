<?php 
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../db.php");

// Clase Hotel
class Hotel {
    private $conexion;
    private $idAlojamiento;
    private $estrellas;
    private $telefonoContacto;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function cargarPorId($idAlojamiento) {
        $this->idAlojamiento = $idAlojamiento;
        $stmt = $this->conexion->prepare("SELECT * FROM Hotel WHERE idAlojamiento = :id");
        $stmt->bindParam(":id", $this->idAlojamiento);
        $stmt->execute();
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registro) {
            $this->estrellas = $registro['estrellas'];
            $this->telefonoContacto = $registro['telefonoContacto'];
            return true;
        }
        return false;
    }

    public function actualizar($estrellas, $telefonoContacto) {
        $this->estrellas = intval($estrellas);
        $this->telefonoContacto = trim($telefonoContacto);

        $stmt = $this->conexion->prepare("UPDATE Hotel SET estrellas = :estrellas, telefonoContacto = :telefono WHERE idAlojamiento = :id");
        $stmt->bindParam(":estrellas", $this->estrellas);
        $stmt->bindParam(":telefono", $this->telefonoContacto);
        $stmt->bindParam(":id", $this->idAlojamiento);

        return $stmt->execute();
    }

    public function getIdAlojamiento() {
        return $this->idAlojamiento;
    }

    public function getEstrellas() {
        return $this->estrellas;
    }

    public function getTelefonoContacto() {
        return $this->telefonoContacto;
    }
}

// Inicializar objeto
$hotelObj = new Hotel($conexion);

// Cargar datos si se recibe un ID (modo edición)
if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];
    
    if (!$hotelObj->cargarPorId($txtID)) {
        header("Location: index.php?mensaje=" . urlencode("Hotel no encontrado"));
        exit();
    }
}

// Guardar cambios (modo POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $txtID = $_POST["txtID"];
    $estrellas = $_POST["estrellas"];
    $telefono = $_POST["telefonoContacto"];

    $hotelObj->cargarPorId($txtID); // Asegurarse de que exista

    if ($hotelObj->actualizar($estrellas, $telefono)) {
        header("Location: index.php?mensaje=" . urlencode("Hotel actualizado correctamente"));
        exit();
    } else {
        $error = "Error al actualizar el hotel.";
    }
}
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Editar Hotel</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="editar.php" method="post">
            <input type="hidden" name="txtID" value="<?php echo htmlspecialchars($hotelObj->getIdAlojamiento()); ?>">

            <div class="mb-3">
                <label for="estrellas" class="form-label">Estrellas</label>
                <input type="number" class="form-control" id="estrellas" name="estrellas" min="1" max="5"
                    value="<?php echo htmlspecialchars($hotelObj->getEstrellas()); ?>" required>
            </div>

            <div class="mb-3">
                <label for="telefonoContacto" class="form-label">Teléfono de Contacto</label>
                <input type="text" class="form-control" id="telefonoContacto" name="telefonoContacto"
                    value="<?php echo htmlspecialchars($hotelObj->getTelefonoContacto()); ?>" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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
