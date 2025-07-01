<?php
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
</head>
<body>
    <h2>Error</h2>
    <p><?php echo isset($errorMessage) ? htmlspecialchars($errorMessage) : 'Ocurrió un error inesperado.'; ?></p>
    <a href="index.php">Volver al inicio</a>
</body>
</html>
<?php
$content = ob_get_clean();
echo $content;
?>