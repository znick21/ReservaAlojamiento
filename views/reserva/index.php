<h2>Lista de Reservas</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Habitación</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Precio Total</th>
            <th>Estado</th>
            <th>Fecha Reserva</th>
            <th>Notas</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $db = new Database();
        $conn = $db->getConnection();
        foreach ($reservas as $reserva) {
            $stmt = $conn->prepare("SELECT CONCAT(tipoHabitacion, ' - ', numeroHabitacion) as nombre 
                                    FROM habitacion WHERE idHabitacion = ?");
            $stmt->execute([$reserva['idHabitacion']]);
            $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
            <tr>
                <td><?php echo htmlspecialchars($reserva['idReserva']); ?></td>
                <td><?php echo htmlspecialchars($habitacion['nombre'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($reserva['fechaInicio']); ?></td>
                <td><?php echo htmlspecialchars($reserva['fechaFin']); ?></td>
                <td><?php echo htmlspecialchars(number_format($reserva['precioTotal'], 2)); ?></td>
                <td><?php echo htmlspecialchars($reserva['estado']); ?></td>
                <td><?php echo htmlspecialchars($reserva['fechaReserva']); ?></td>
                <td><?php echo htmlspecialchars($reserva['notas'] ?? 'N/A'); ?></td>
                <td>
                    <a href="index.php?controller=reserva&action=editar&id=<?php echo $reserva['idReserva']; ?>">Editar</a>
                    <a href="index.php?controller=reserva&action=eliminar&id=<?php echo $reserva['idReserva']; ?>" onclick="return confirm('¿Seguro que desea eliminar?');">Eliminar</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if (empty($reservas)) { ?>
    <p>No hay reservas registradas.</p>
<?php } ?>