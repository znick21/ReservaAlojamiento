<?php
// Conexión a la base de datos
include("../../db.php");

// Clase para manejar operaciones CRUD de ServicioHotel
class ServicioHotel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerServicios()
    {
        $stmt = $this->conexion->prepare("SELECT * FROM ServicioHotel");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarServicio($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM ServicioHotel WHERE idServicioHotel = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return "Servicio eliminado correctamente.";
    }
}

// Instanciar la clase ServicioHotel
$servicioObj = new ServicioHotel($conexion);

// Eliminar si se recibe un ID por GET
if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];
    $mensaje = $servicioObj->eliminarServicio($txtID);
    header("Location: index.php?mensaje=" . urlencode($mensaje));
    exit();
}

// Obtener lista de servicios
$lista_servicios = $servicioObj->obtenerServicios();
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Lista de Servicios de Hotel</h2>

<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-success" href="crear.php" role="button">
        <i class="bi bi-plus-circle"></i> Nuevo Servicio
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4>Servicios Registrados</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabla_id">
                <thead class="table-dark">
                    <tr>
                        <th>ID Servicio</th>
                        <th>ID Alojamiento</th>
                        <th>Servicio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_servicios as $servicio) { ?>
                        <tr>
                            <td><?php echo $servicio["idServicioHotel"]; ?></td>
                            <td><?php echo $servicio["idAlojamiento"]; ?></td>
                            <td><?php echo $servicio["servicio"]; ?></td>
                            <td>
                                <button class="btn btn-outline-info btn-sm btn-edit" data-id="<?php echo $servicio["idServicioHotel"]; ?>">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm btn-delete" data-id="<?php echo $servicio["idServicioHotel"]; ?>">
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
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: mensaje,
            confirmButtonColor: '#3085d6'
        }).then(() => {
            const url = new URL(window.location);
            url.searchParams.delete('mensaje');
            window.history.replaceState({}, document.title, url.toString());
        });
    <?php endif; ?>

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el servicio del hotel.",
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

    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Deseas editar este servicio?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, editar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `editar.php?txtID=${id}`;
                }
            });
        });
    });
</script>

<style>
    body {
        background-color: #f5f9fc;
        font-family: 'Poppins', sans-serif;
    }
    h2 {
        font-weight: 600;
        color: #0984e3;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
    }
    .btn-success {
        background-color: #00cec9;
        border: none;
        font-weight: 500;
    }
    .btn-success:hover {
        background-color: #00b894;
    }
    .btn-sm {
        border-radius: 6px;
        padding: 0.35rem 0.8rem;
        font-size: 0.85rem;
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
    }
    tbody tr:hover {
        background-color: #f0fbfc;
    }
</style>
