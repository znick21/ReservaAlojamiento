<?php
$editar = isset($editar) && $editar;
$reserva = isset($reserva) ? $reserva : [];
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SELECT idAlojamiento, nombre FROM alojamiento WHERE estado = 'Activo'");
$hoteles = $stmt->fetchAll(PDO::FETCH_ASSOC);
$action = $editar ? "index.php?controller=reserva&action=editar&id=" . $reserva['idReserva'] : "index.php?controller=reserva&action=crear";
?>

<h2><?php echo $editar ? 'Editar Reserva' : 'Crear Reserva'; ?></h2>
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <form action="<?php echo $action; ?>" method="POST" id="reservaForm">
        <label>Hotel:
            <select name="idAlojamiento" id="hotelSelect" required onchange="cargarHabitaciones()">
                <option value="">Seleccione un hotel</option>
                <?php foreach ($hoteles as $hotel): ?>
                    <option value="<?php echo htmlspecialchars($hotel['idAlojamiento']); ?>" <?php echo ($reserva['idAlojamiento'] ?? '') == $hotel['idAlojamiento'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($hotel['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Tipo de Habitación:
            <select name="tipoHabitacion" id="tipoHabitacionSelect" required onchange="mostrarHabitaciones()">
                <option value="">Seleccione tipo</option>
                <option value="Lujo" <?php echo ($reserva['tipoHabitacion'] ?? '') === 'Lujo' ? 'selected' : ''; ?>>Lujo</option>
                <option value="Estándar" <?php echo ($reserva['tipoHabitacion'] ?? '') === 'Estándar' ? 'selected' : ''; ?>>Estándar</option>
                <option value="Suite" <?php echo ($reserva['tipoHabitacion'] ?? '') === 'Suite' ? 'selected' : ''; ?>>Suite</option>
            </select>
        </label><br>
        <label>Fecha Inicio: <input type="date" name="fechaInicio" value="<?php echo htmlspecialchars($reserva['fechaInicio'] ?? ''); ?>" required onchange="calcularPrecio()"></label><br>
        <label>Fecha Fin: <input type="date" name="fechaFin" value="<?php echo htmlspecialchars($reserva['fechaFin'] ?? ''); ?>" required onchange="calcularPrecio()"></label><br>
        <label>Estado:
            <select name="estado" required>
                <option value="Pendiente" <?php echo ($reserva['estado'] ?? '') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="Confirmada" <?php echo ($reserva['estado'] ?? '') === 'Confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                <option value="Cancelada" <?php echo ($reserva['estado'] ?? '') === 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
            </select>
        </label><br>
        <label>Notas: <textarea name="notas"><?php echo htmlspecialchars($reserva['notas'] ?? ''); ?></textarea></label><br>
        <label>Precio Total: <input type="text" name="precioTotal" id="precioTotal" value="<?php echo htmlspecialchars($reserva['precioTotal'] ?? '0.00'); ?>" readonly></label><br>
        <label>Habitación:
            <select name="idHabitacion" id="habitacionSelect" required>
                <option value="">Seleccione una habitación</option>
            </select>
        </label><br>
        <button type="submit">Crear</button>
    </form>
    <div id="estadoHabitaciones">
        <table id="habitacionesTable" style="border-collapse: collapse; width: 100%; margin-top: 0;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ddd; padding: 8px;">Número</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Estado</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Precio</th>
                </tr>
            </thead>
            <tbody id="habitacionesBody"></tbody>
        </table>
    </div>
</div>

<script>
function cargarHabitaciones() {
    const hotelId = document.getElementById('hotelSelect').value;
    if (hotelId) {
        fetch(`index.php?controller=habitacion&action=getByAlojamiento&id=${hotelId}`)
            .then(response => response.json())
            .then(data => {
                const tipoSelect = document.getElementById('tipoHabitacionSelect');
                tipoSelect.innerHTML = '<option value="">Seleccione tipo</option>';
                ['Lujo', 'Estándar', 'Suite'].forEach(tipo => {
                    if (data.some(h => h.tipoHabitacion === tipo)) {
                        const option = document.createElement('option');
                        option.value = tipo;
                        option.textContent = tipo;
                        tipoSelect.appendChild(option);
                    }
                });
                actualizarHabitaciones(data);
                calcularPrecio();
            });
    } else {
        actualizarHabitaciones([]);
    }
}

function actualizarHabitaciones(data) {
    const habitacionSelect = document.getElementById('habitacionSelect');
    const table = document.getElementById('habitacionesTable');
    const tbody = document.getElementById('habitacionesBody');
    habitacionSelect.innerHTML = '<option value="">Seleccione una habitación</option>';
    if (data && data.length > 0) {
        tbody.innerHTML = '';
        data.forEach(h => {
            const option = document.createElement('option');
            option.value = h.idHabitacion;
            option.textContent = `${h.tipoHabitacion} - ${h.numeroHabitacion} ($${h.precio})`;
            habitacionSelect.appendChild(option);

            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="border: 1px solid #ddd; padding: 8px;">${h.numeroHabitacion}</td>
                <td style="border: 1px solid #ddd; padding: 8px; color: ${h.estado === 'Disponible' ? 'green' : 'red'}">${h.estado}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">$${h.precio}</td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="3" style="padding: 8px;">No hay habitaciones disponibles</td></tr>';
    }
}

function mostrarHabitaciones() {
    const hotelId = document.getElementById('hotelSelect').value;
    const tipo = document.getElementById('tipoHabitacionSelect').value;
    if (hotelId && tipo) {
        fetch(`index.php?controller=habitacion&action=getByAlojamiento&id=${hotelId}`)
            .then(response => response.json())
            .then(data => {
                const filteredData = data.filter(h => h.tipoHabitacion === tipo);
                actualizarHabitaciones(filteredData);
            });
    } else {
        actualizarHabitaciones([]);
    }
}

function calcularPrecio() {
    const fechaInicio = new Date(document.querySelector('input[name="fechaInicio"]').value);
    const fechaFin = new Date(document.querySelector('input[name="fechaFin"]').value);
    const precioTotal = document.getElementById('precioTotal');
    const habitacionSelect = document.getElementById('habitacionSelect');

    if (!fechaInicio || !fechaFin || isNaN(fechaInicio.getTime()) || isNaN(fechaFin.getTime()) || fechaFin < fechaInicio || !habitacionSelect.value) {
        precioTotal.value = '0.00';
        return;
    }

    const dias = (fechaFin - fechaInicio) / (1000 * 60 * 60 * 24) + 1;
    const habitacionId = habitacionSelect.value;

    if (habitacionId) {
        fetch(`index.php?controller=habitacion&action=getPrecio&id=${habitacionId}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.status === 'success') {
                    const precioBase = parseFloat(data.precio) || 0;
                    const total = precioBase * dias;
                    precioTotal.value = total.toFixed(2);
                } else {
                    console.error('Error al obtener precio:', data);
                    precioTotal.value = '0.00';
                }
            })
            .catch(error => {
                console.error('Error al obtener precio:', error);
                precioTotal.value = '0.00';
            });
    } else {
        precioTotal.value = '0.00';
    }
}

document.getElementById('hotelSelect').addEventListener('change', cargarHabitaciones);
document.getElementById('tipoHabitacionSelect').addEventListener('change', mostrarHabitaciones);
document.querySelector('input[name="fechaInicio"]').addEventListener('change', calcularPrecio);
document.querySelector('input[name="fechaFin"]').addEventListener('change', calcularPrecio);
document.getElementById('habitacionSelect').addEventListener('change', calcularPrecio);
cargarHabitaciones();
</script>

<?php if (isset($response) && $response['status'] === 'error') { ?>
    <p class="error"><?php echo htmlspecialchars($response['message']); ?></p>
<?php } ?>