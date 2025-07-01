<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../db.php");

class DepartamentoTuristico {
    private $conexion;

    // Propiedades para Alojamiento
    public $nombre;
    public $tipo = "DepartamentoTuristico"; // fijo para este tipo
    public $precioPorNoche;
    public $capacidad;

    // Propiedades para DepartamentoTuristico
    public $numeroHabitaciones;
    public $capacidadMaxima;
    public $amueblado;
    public $cocinaEquipada;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function crearDepartamento() {
        try {
            // Empezamos transacción porque son dos tablas relacionadas
            $this->conexion->beginTransaction();

            // Insertar primero en Alojamiento
            $sqlAlojamiento = "INSERT INTO Alojamiento (nombre, tipo, precioPorNoche, capacidad) 
                               VALUES (:nombre, :tipo, :precioPorNoche, :capacidad)";
            $stmt = $this->conexion->prepare($sqlAlojamiento);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':tipo', $this->tipo);
            $stmt->bindParam(':precioPorNoche', $this->precioPorNoche);
            $stmt->bindParam(':capacidad', $this->capacidad);
            $stmt->execute();

            // Obtener el id generado en Alojamiento
            $idAlojamiento = $this->conexion->lastInsertId();

            // Insertar en DepartamentoTuristico con ese id
            $sqlDepto = "INSERT INTO DepartamentoTuristico (idAlojamiento, numeroHabitaciones, capacidadMaxima, amueblado, cocinaEquipada) 
                         VALUES (:idAlojamiento, :numeroHabitaciones, :capacidadMaxima, :amueblado, :cocinaEquipada)";
            $stmt2 = $this->conexion->prepare($sqlDepto);
            $stmt2->bindParam(':idAlojamiento', $idAlojamiento);
            $stmt2->bindParam(':numeroHabitaciones', $this->numeroHabitaciones);
            $stmt2->bindParam(':capacidadMaxima', $this->capacidadMaxima);
            
            // Para BIT en MySQL, pasar 1 o 0
            $amuebladoBit = $this->amueblado ? 1 : 0;
            $cocinaEquipadaBit = $this->cocinaEquipada ? 1 : 0;
            $stmt2->bindParam(':amueblado', $amuebladoBit, PDO::PARAM_INT);
            $stmt2->bindParam(':cocinaEquipada', $cocinaEquipadaBit, PDO::PARAM_INT);

            $stmt2->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error creando departamento turístico: " . $e->getMessage());
            return false;
        }
    }
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departamentoObj = new DepartamentoTuristico($conexion);

    $departamentoObj->nombre = trim($_POST['nombre']);
    $departamentoObj->precioPorNoche = floatval($_POST['precioPorNoche']);
    $departamentoObj->capacidad = intval($_POST['capacidad']);

    $departamentoObj->numeroHabitaciones = intval($_POST['numeroHabitaciones']);
    $departamentoObj->capacidadMaxima = intval($_POST['capacidadMaxima']);
    $departamentoObj->amueblado = isset($_POST['amueblado']) ? true : false;
    $departamentoObj->cocinaEquipada = isset($_POST['cocinaEquipada']) ? true : false;

    // Validar mínimo el nombre y campos obligatorios
    if (empty($departamentoObj->nombre)) {
        $error = "El nombre es obligatorio.";
    } elseif ($departamentoObj->precioPorNoche <= 0) {
        $error = "El precio por noche debe ser mayor que cero.";
    } elseif ($departamentoObj->capacidad <= 0) {
        $error = "La capacidad debe ser mayor que cero.";
    } else {
        if ($departamentoObj->crearDepartamento()) {
            $mensaje = "Departamento turístico creado exitosamente";
            header("Location: index.php?mensaje=" . urlencode($mensaje));
            exit();
        } else {
            $error = "Error al crear el departamento turístico.";
        }
    }
}
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Crear Nuevo Departamento Turístico</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="crear.php" method="post">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" required>
            </div>

            <div class="mb-3">
                <label for="precioPorNoche" class="form-label">Precio por Noche</label>
                <input type="number" step="0.01" min="0" class="form-control" name="precioPorNoche" id="precioPorNoche" required>
            </div>

            <div class="mb-3">
                <label for="capacidad" class="form-label">Capacidad</label>
                <input type="number" min="1" class="form-control" name="capacidad" id="capacidad" required>
            </div>

            <hr>

            <div class="mb-3">
                <label for="numeroHabitaciones" class="form-label">Número de Habitaciones</label>
                <input type="number" min="1" class="form-control" name="numeroHabitaciones" id="numeroHabitaciones" required>
            </div>

            <div class="mb-3">
                <label for="capacidadMaxima" class="form-label">Capacidad Máxima</label>
                <input type="number" min="1" class="form-control" name="capacidadMaxima" id="capacidadMaxima" required>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="amueblado" name="amueblado">
                <label class="form-check-label" for="amueblado">
                    Amueblado
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="cocinaEquipada" name="cocinaEquipada">
                <label class="form-check-label" for="cocinaEquipada">
                    Cocina Equipada
                </label>
            </div>

            <br>
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Regresar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

<!-- SweetAlert2 para errores -->
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
