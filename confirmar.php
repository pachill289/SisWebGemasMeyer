<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación correo</title>
    <?php
    if($_GET)
    {
        $codigo = $_GET['codigo'];
    } 
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recuperar el código de verificación enviado por el formulario
        $codigoIngresado = $_POST["codigo"];
    
        if ($codigoIngresado == $codigo) {
            echo "¡Código de verificación correcto! Correo verificado,ahora puede iniciar sesión.";
            header("Location: login.php");
        } else {
            echo "Código de verificación incorrecto. Intenta de nuevo.";
        }
    } else {
        // Redirigir si se accede directamente a este script sin enviar datos por el formulario
        header("Location: confirmar.php");
        exit();
    }
    ?>
</head>
<body>
    <h2>Comprobar Código de Verificación</h2>
    <form action="confirmar.php" method="post">
        <label for="codigo">Código de Verificación:</label>
        <input type="text" id="codigo" name="codigo" required>
        <br>
        <input type="submit" value="Comprobar">
    </form>
</body>
</html>