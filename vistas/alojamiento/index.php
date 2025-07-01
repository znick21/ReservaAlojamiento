<?php
ob_start();
?>
<h2>Listado de Alojamientos</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Precio/Noche</th>
            <th>Capacidad</th>
            <th>Detalles</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($alojamientos as $alo): ?>
        <tr>
            <td><?php echo $alo['idAlojamiento']; ?></td>
            <td><?php echo $alo['nombre']; ?></td>
            <td><?php echo $alo['tipo']; ?></td>
            <td><?php echo number_format($alo['precioPorNoche'], 2); ?></td>
            <td><?php echo $alo['capacidad']; ?></td>
            <td>
                <?php if($alo['tipo'] == 'Hotel' && $alo['estrellas']): ?>
                    Estrellas: <?php echo $alo['estrellas']; ?><br>
                    Teléfono: <?php echo $alo['telefonoContacto']; ?>
                <?php elseif($alo['tipo'] == 'Hostal' && $alo['horaCheckIn']): ?>
                    Check-in: <?php echo $alo['horaCheckIn']; ?>
                <?php elseif($alo['tipo'] == 'DepartamentoTuristico'): ?>
                    Habitaciones: <?php echo $alo['numeroHabitaciones']; ?><br>
                    Cap. Máx: <?php echo $alo['capacidadMaxima']; ?><br>
                    Amueblado: <?php echo $alo['amueblado'] ? 'Sí' : 'No'; ?><br>
                    Cocina: <?php echo $alo['cocinaEquipada'] ? 'Sí' : 'No'; ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include_once 'vistas/plantillas/principal.php';
?>