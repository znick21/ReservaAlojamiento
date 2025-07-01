<?php 
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../db.php");

// Obtener lista de hoteles con detalles del alojamiento si es necesario
$sentencia = $conexion->prepare("
    SELECT h.idAlojamiento, h.estrellas, h.telefonoContacto
    FROM Hotel h
    ORDER BY h.idAlojamiento ASC
");
$sentencia->execute();
$lista_hoteles = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Eliminar hotel si se pasa un ID por GET
if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];

    $eliminarStmt = $conexion->prepare("DELETE FROM Hotel WHERE idAlojamiento = :id");
    $eliminarStmt->bindParam(":id", $txtID);

    if ($eliminarStmt->execute()) {
        header("Location: index.php?mensaje=" . urlencode("Hotel eliminado correctamente"));
        exit();
    } else {
        header("Location: index.php?mensaje=" . urlencode("Error al eliminar el hotel"));
        exit();
    }
}
?>

<?php include("../../templates/header.php"); ?>

<br>
<h2 class="text-center">Hoteles Registrados</h2>

<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-success" href="crear.php" role="button">
        <i class="bi bi-building-add"></i> Nuevo Hotel
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4>Lista de Hoteles</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabla_id">
                <thead class="table-dark">
                    <tr>
                        <th>ID Alojamiento</th>
                        <th>Estrellas</th>
                        <th>Teléfono de Contacto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_hoteles as $hotel): ?>
                        <tr>
                            <td><?php echo $hotel["idAlojamiento"]; ?></td>
                            <td><?php echo $hotel["estrellas"]; ?> ★</td>
                            <td><?php echo $hotel["telefonoContacto"]; ?></td>
                            <td>
                                <button class="btn btn-outline-info btn-sm btn-edit" data-id="<?php echo $hotel["idAlojamiento"]; ?>">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm btn-delete" data-id="<?php echo $hotel["idAlojamiento"]; ?>">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Mostrar mensaje de éxito o error -->
<?php if (isset($_GET["mensaje"])): ?>
<script>
    const mensaje = '<?php echo htmlspecialchars($_GET["mensaje"]); ?>';
    const esError = mensaje.toLowerCase().includes('error');

    Swal.fire({
        icon: esError ? 'error' : 'success',
        title: esError ? 'Error' : 'Éxito',
        text: mensaje,
        confirmButtonColor: '#3085d6'
    }).then(() => {
        const url = new URL(window.location);
        url.searchParams.delete('mensaje');
        window.history.replaceState({}, document.title, url.toString());
    });
</script>
<?php endif; ?>

<!-- Confirmaciones con SweetAlert2 -->
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
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
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            Swal.fire({
                title: 'Editar hotel',
                icon: 'question',
                showCancelButton: true,
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
    }

    .card {
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
        background-color: #ffffff;
    }

    thead.table-dark th {
        background-color: #2d3436;
        color: white;
        vertical-align: middle;
    }

    tbody tr:hover {
        background-color: #f0fbfc;
    }
</style>
