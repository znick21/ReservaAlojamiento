<?php
ob_start();
?>
<h2>Listado de Reservas</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Inicio</th>
            <th>Estado</th>
            <th>Precio Total</th>
            <th>Número Huespedes</th>
            <th>Alojamiento</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($reservas as $res): ?>
        <tr>
            <td><?php echo $res['idReserva']; ?></td>
            <td><?php echo $res['fechaInicio']; ?></td>
            <td><?php echo $res['estado']; ?></td>
            <td><?php echo number_format($res['precioTotal'], 2); ?></td>
            <td><?php echo $res['numeroHuespedes']; ?></td>
            <td><?php echo $res['nombre_alojamiento']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include_once 'vistas/plantillas/principal.php';
?>