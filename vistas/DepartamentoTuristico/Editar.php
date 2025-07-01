<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../db.php"); // Aquí debe estar tu conexión PDO

class DepartamentoTuristico {
    private $conexion;
    private $idAlojamiento;
    private $numeroHabitaciones;
    private $capacidadMaxima;
    private $amueblado;
    private $cocinaEquipada;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Cargar datos por idAlojamiento
    public function cargarPorId($id) {
        $this->idAlojamiento = $id;
        $stmt = $this->conexion->prepare("SELECT * FROM DepartamentoTuristico WHERE idAlojamiento = :id");
        $stmt->bindParam(":id", $this->idAlojamiento, PDO::PARAM_INT);
        $stmt->execute();
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registro) {
            $this->numeroHabitaciones = $registro['numeroHabitaciones'];
            $this->capacidadMaxima = $registro['capacidadMaxima'];
            $this->amueblado = $registro['amueblado'];
            $this->cocinaEquipada = $registro['cocinaEquipada'];
            return true;
        }
        return false;
    }

    // Actualizar registro
    public function actualizar($numeroHabitaciones, $capacidadMaxima, $amueblado, $cocinaEquipada) {
        $this->numeroHabitaciones = (int)$numeroHabitaciones;
        $this->capacidadMaxima = (int)$capacidadMaxima;
        $this->amueblado = (int)$amueblado; // 0 o 1
        $this->cocinaEquipada = (int)$cocinaEquipada; // 0 o 1

        $stmt = $this->conexion->prepare("
            UPDATE DepartamentoTuristico 
            SET numeroHabitaciones = :numHab,
                capacidadMaxima = :capMax,
                amueblado = :amueblado,
                cocinaEquipada = :cocinaEq
            WHERE idAlojamiento = :id
        ");

        $stmt->bindParam(":numHab", $this->numeroHabitaciones, PDO::PARAM_INT);
        $stmt->bindParam(":capMax", $this->capacidadMaxima, PDO::PARAM_INT);
        $stmt->bindParam(":amueblado", $this->amueblado, PDO::PARAM_INT);
        $stmt->bindParam(":cocinaEq", $this->cocinaEquipada, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->idAlojamiento, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Getters para mostrar datos en el formulario
    public function getIdAlojamiento() {
        return $this->idAlojamiento;
    }

    public function getNumeroHabitaciones() {
        return $this->numeroHabitaciones;
    }

    public function getCapacidadMaxima() {
        return $this->capacidadMaxima;
    }

    public function getAmueblado() {
        return $this->amueblado;
    }

    public function getCocinaEquipada() {
        return $this->cocinaEquipada;
    }
}

// Inicializar objeto
$departamentoObj = new DepartamentoTuristico($conexion);

// Modo edición: cargar datos
if (isset($_GET["idAlojamiento"])) {
    $idAlojamiento = $_GET["idAlojamiento"];
    if (!$departamentoObj->cargarPorId($idAlojamiento)) {
        header("Location: index.php?mensaje=" . urlencode("Departamento turístico no encontrado"));
        exit();
    }
}

// Modo actualización: guardar cambios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idAlojamiento = $_POST["idAlojamiento"];
    $numeroHabitaciones = $_POST["numeroHabitaciones"];
    $capacidadMaxima = $_POST["capacidadMaxima"];
    $amueblado = isset($_POST["amueblado"]) ? 1 : 0;
    $cocinaEquipada = isset($_POST["cocinaEquipada"]) ? 1 : 0;

    $departamentoObj->cargarPorId($idAlojamiento);

    if ($departamentoObj->actualizar($numeroHabitaciones, $capacidadMaxima, $amueblado, $cocinaEquipada)) {
        $mensaje = "Departamento turístico actualizado correctamente";
        header("Location: index.php?mensaje=" . urlencode($mensaje));
        exit();
    } else {
        $error = "Error al actualizar el departamento turístico.";
    }
}
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Editar Departamento Turístico</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="editar.php" method="post">
            <input type="hidden" name="idAlojamiento" value="<?php echo htmlspecialchars($departamentoObj->getIdAlojamiento()); ?>">

            <div class="mb-3">
                <label for="numeroHabitaciones" class="form-label">Número de Habitaciones</label>
                <input type="number" class="form-control" id="numeroHabitaciones" name="numeroHabitaciones" min="1" required
                       value="<?php echo htmlspecialchars($departamentoObj->getNumeroHabitaciones()); ?>">
            </div>

            <div class="mb-3">
                <label for="capacidadMaxima" class="form-label">Capacidad Máxima</label>
                <input type="number" class="form-control" id="capacidadMaxima" name="capacidadMaxima" min="1" required
                       value="<?php echo htmlspecialchars($departamentoObj->getCapacidadMaxima()); ?>">
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="amueblado" name="amueblado"
                    <?php echo $departamentoObj->getAmueblado() ? 'checked' : ''; ?>>
                <label class="form-check-label" for="amueblado">
                    Amueblado
                </label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="cocinaEquipada" name="cocinaEquipada"
                    <?php echo $departamentoObj->getCocinaEquipada() ? 'checked' : ''; ?>>
                <label class="form-check-label" for="cocinaEquipada">
                    Cocina Equipada
                </label>
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
