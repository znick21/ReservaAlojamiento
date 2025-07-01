<?php
// Clase para manejar operaciones de la base de datos (CRUD de DepartamentoTuristico)
class DepartamentoTuristico
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Obtener todos los departamentos turísticos
    public function obtenerDepartamentos()
    {
        $sql = "SELECT * FROM DepartamentoTuristico";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar departamento turístico por idAlojamiento
    public function eliminarDepartamento($idAlojamiento)
    {
        // Aquí podrías agregar una verificación si está relacionado con otra tabla

        $sql = "DELETE FROM DepartamentoTuristico WHERE idAlojamiento = :idAlojamiento";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAlojamiento', $idAlojamiento, PDO::PARAM_INT);
        $stmt->execute();

        return "Departamento Turístico eliminado";
    }
}

// Conexión a la base de datos
include("../../db.php");

// Crear instancia de la clase DepartamentoTuristico
$departamentoTuristicoObj = new DepartamentoTuristico($conexion);

// Eliminar departamento si es necesario
if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];
    $mensaje = $departamentoTuristicoObj->eliminarDepartamento($txtID);
    header("Location: index.php?mensaje=" . urlencode($mensaje));
    exit();
}

// Obtener la lista de departamentos turísticos
$lista_departamentos = $departamentoTuristicoObj->obtenerDepartamentos();
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Lista de Departamentos Turísticos</h2>

<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-success" href="crear.php" role="button">
        <i class="bi bi-building-add"></i> Nuevo Departamento Turístico
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4>Departamentos Turísticos Registrados</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabla_id">
                <thead class="table-dark">
                    <tr>
                        <th>ID Alojamiento</th>
                        <th>Número Habitaciones</th>
                        <th>Capacidad Máxima</th>
                        <th>Amueblado</th>
                        <th>Cocina Equipada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_departamentos as $departamento) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($departamento["idAlojamiento"]); ?></td>
                            <td><?php echo htmlspecialchars($departamento["numeroHabitaciones"]); ?></td>
                            <td><?php echo htmlspecialchars($departamento["capacidadMaxima"]); ?></td>
                            <td><?php echo $departamento["amueblado"] ? 'Sí' : 'No'; ?></td>
                            <td><?php echo $departamento["cocinaEquipada"] ? 'Sí' : 'No'; ?></td>
                            <td>
                                <button class="btn btn-outline-info btn-sm btn-edit" data-id="<?php echo $departamento["idAlojamiento"]; ?>">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm btn-delete" data-id="<?php echo $departamento["idAlojamiento"]; ?>">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    <?php if (isset($_GET["mensaje"])): ?>
        const mensaje = '<?php echo htmlspecialchars($_GET["mensaje"]); ?>';
        const esError = mensaje.toLowerCase().includes('error');

        Swal.fire({
            icon: esError ? 'error' : 'success',
            title: esError ? '¡Error!' : '¡Éxito!',
            text: mensaje,
            confirmButtonColor: '#3085d6'
        }).then(() => {
            const url = new URL(window.location);
            url.searchParams.delete('mensaje');
            window.history.replaceState({}, document.title, url.toString());
        });
    <?php endif; ?>

    // Confirmación al eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `index.php?txtID=${id}`;
                }
            });
        });
    });

    // Confirmación al editar
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Deseas editar este departamento turístico?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, editar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "editar.php?txtID=" + id;
                }
            });
        });
    });
</script>

<style>
    /* Mantén tu CSS existente para estilos */
    body {
        background-color: #f5f9fc;
        font-family: 'Poppins', sans-serif;
    }
    h2 {
        font-weight: 600;
        color: #0984e3;
        text-align: center;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
    }
    .btn-success {
        background-color: #00cec9;
        border: none;
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .btn-success:hover {
        background-color: #00b894;
    }
    .btn-sm {
        border-radius: 6px;
        padding: 0.35rem 0.8rem;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    .btn-info {
        background-color: #0984e3;
        border-color: #0984e3;
        color: white;
    }
    .btn-info:hover {
        background-color: #0652dd;
        border-color: #0652dd;
    }
    .btn-danger:hover {
        background-color: #c0392b;
    }
    .card {
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
        background-color: #ffffff;
    }
    table.table {
        border-radius: 12px;
        overflow: hidden;
    }
    thead.table-dark th {
        background-color: #2d3436;
        color: white;
        vertical-align: middle;
    }
    tbody tr:hover {
        background-color: #f0fbfc;
    }
    .table-responsive {
        margin-top: 1rem;
    }
    @media (max-width: 768px) {
        .btn {
            font-size: 0.8rem;
        }
    }
</style>
