<?php
    //url base para desarrollo
    $url_base = "http://localhost:8080/PaginaWebGM/";
    //url para prodcucción
    //$url_base = "http://gemas-meyer-demo.great-site.net/";
    if(isset($_COOKIE['usuario']))
    {
        $usuarioSesion = json_decode($_COOKIE['usuario']);
    }
?>
<!doctype html>
<html lang="es">

<head>
    <title>Sistema Web Gemas Meyer</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Íconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Archivos javascript -->
    <script type="text/javascript" src="<?php echo $url_base;?>acciones/validacionUsuario.js"></script>
    <script type="text/javascript" src="<?php echo $url_base;?>acciones/validacionProducto.js"></script>
    <script type="text/javascript" src="<?php echo $url_base;?>acciones/efectos.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        @font-face {
            font-family: 'TipografiaElegante';
            src: url('resources/fuentes/DancingScript-VariableFont_wght.ttf') format('truetype');
        }
        @font-face {
            font-family: 'TipografiaElegante-bold';
            src: url('resources/fuentes/DancingScript-Bold.ttf') format('truetype');
        }
    </style>
</head>
<?php if(isset($usuarioSesion)) {
    if($usuarioSesion->tipo == 3) {?>
            <button style="top: 570px;left:85%;position: sticky;z-index: 9999;" type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">
            Ver carrito <i class="bi bi-cart4"></i>
            <!--Conteo de productos para el carrito -->
            <?php 
                $productosCarrito = $_SESSION['comprasCarrito'];
                echo "<span><b>(".count($productosCarrito->compras).")</b></span>";
             ?>
            </button>
    <?php }
}?>
<body style="   background-image: url(resources/Fondo.png);
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center center;
                backdrop-filter: blur(5px);" >
    <header>
    <nav class="navbar navbar-expand navbar-light bg-light">
        <ul class="nav navbar-nav">
            <li class="nav-item">
                <a style="font-size: xx-large;" class="nav-link active" href="<?php echo $url_base;?>" aria-current="page">Sistema Web Gemas Meyer<span class="visually-hidden">(current)</span></a>
            </li>
            <li class="nav-item">
                <?php if(isset($usuarioSesion)) {
                        if($usuarioSesion->tipo == 1 || $usuarioSesion->tipo == 2 && $usuarioSesion->estado == 1) {
                    ?>
                <a style="font-size: 22px;" class="nav-link" href="<?php echo $url_base;?>modulos/productos/">Productos</a>
                <?php 
                        }
                    }?>
            </li>
            <li class="nav-item">
                <?php if(isset($usuarioSesion)){
                        if($usuarioSesion->tipo == 1) {
                    ?>
                <a style="font-size: 22px;" class="nav-link" href="<?php echo $url_base;?>modulos/usuarios/">Usuarios</a>
                <?php }
                }?>
            </li>
            <li class="nav-item">
                <?php if(isset($usuarioSesion)) {
                    if($usuarioSesion->tipo == 1 || $usuarioSesion->tipo == 2) {
                    ?>
                <a style="font-size: 22px;" class="nav-link" href="<?php echo $url_base;?>modulos/pedidos/">Pedidos <i class="bi bi-handbag-fill"></i></a>
                <?php }
                }?>
            </li>
            <li class="nav-item">
                <?php if(!isset($usuarioSesion)) {?>
                <a style="margin-left: 30px;margin-top: 30px;" class="btn btn-primary" href="<?php echo $url_base;?>login.php"><b>Iniciar sesión</b> <i class="bi bi-door-open-fill"></i></a>
                <?php }
                else
                {
                    echo "<h6><b>Bienvenido: $usuarioSesion->nombreCompleto</b></h6>";
                }?>
            </li>
            <li class="nav-item">
                <?php if(isset($usuarioSesion)) {?>
                <a style="margin-left: 30px;" class="btn btn-danger" href="<?php echo $url_base;?>/logout.php">Cerrar sesión <i class="bi bi-door-closed-fill"></i></a>
                <?php }?>
            </li>
            <!-- Carrito de compras -->
            <li class="nav-item">
                
            </li>
            <li style="margin-left: 50px;" class="nav-item"><img height="100px" src="<?php echo $url_base;?>resources/logo2.jpg"></li>
        </ul>
    </nav>
    </header>
    <main class="container">