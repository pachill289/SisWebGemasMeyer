<?php
    //url base para desarrollo
    $url_base = "http://localhost:80/SisWebGemasMeyer/";
    //url producción 1:
    //$url_base = "https://gemas-meyer-demo.great-site.net/";
    //url producción 2:
    //$url_base = "http://pachill289-001-site1.htempurl.com/";
    if(isset($_COOKIE['usuario']))
    {
        $usuarioSesion = json_decode($_COOKIE['usuario']);
    }
?>
<!doctype html>
<html lang="es">
<head>
    <title><?php 
    if(isset($usuarioSesion)) { 
        if($usuarioSesion->tipo == 3)
        {
            echo "Página Web Gemas Meyer";
        }
        else {
            echo "Sistema Web Gemas Meyer";
        }
    }
    else {
        echo "Página Web Gemas Meyer";
    }?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="<?php echo $url_base;?>resources/favicon.png" type="image/png">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Íconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Archivos javascript -->
    <script type="text/javascript" src="<?php echo $url_base;?>acciones/validacionUsuario.js"></script>
    <script type="text/javascript" src="<?php echo $url_base;?>acciones/validacionProducto.js"></script>
    <script type="text/javascript" src="<?php echo $url_base;?>acciones/validacionPublicacion.js"></script>
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
                if(isset($_SESSION['comprasCarrito']))
                {
                    $productosCarrito = $_SESSION['comprasCarrito'];
                    echo "<span><b>(".count($productosCarrito->compras).")</b></span>";
                }
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
                <a style="font-size: xx-large;" class="nav-link active" href="<?php if(isset($usuarioSesion) && $usuarioSesion->tipo == 1) {
                    echo ($url_base."modulos/productos/index.php");
                    } else {
                        echo ($url_base);
                    }?>" aria-current="page"><?php if(isset($usuarioSesion) && $usuarioSesion->tipo == 1){ echo "Sistema Web Gemas Meyer";} else { echo "Página web gemas meyer";}?><span class="visually-hidden">(current)</span></a>
            </li>
            <li class="nav-item">
                <?php if(isset($usuarioSesion)) {
                        if($usuarioSesion->tipo == 1 || $usuarioSesion->tipo == 2 && $usuarioSesion->estado == 1) {
                    ?>
                <a style="font-size: 22px;" class="nav-link" href="<?php echo $url_base;?>modulos/productos/">Productos <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-box-seam-fill" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184l.01-.003.268-.108a.75.75 0 0 1 .558 0l.269.108.01.003 6.97 2.789ZM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461L10.404 2Z"/>
</svg></a>
                <?php 
                        }
                    }?>
            </li>
            <li class="nav-item">
                <?php if(isset($usuarioSesion)){
                        if($usuarioSesion->tipo == 1) {
                    ?>
                <a style="font-size: 22px;" class="nav-link" href="<?php echo $url_base;?>modulos/usuarios/">Usuarios <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-standing" viewBox="0 0 16 16">
  <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM6 6.75v8.5a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v2.75a.75.75 0 0 0 1.5 0v-2.5a.25.25 0 0 1 .5 0Z"/>
</svg></a>
                <?php }
                }?>
            </li>
            <li class="nav-item">
                <?php if(isset($usuarioSesion)) {
                    if($usuarioSesion->tipo == 1 || $usuarioSesion->tipo == 2) {
                    ?>
                <a style="font-size: 22px;" class="nav-link" href="<?php echo $url_base;?>modulos/publicaciones/">Publicaciones <i class="bi bi-send"></i></a>
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
                <?php if(isset($usuarioSesion)) {
                    if($usuarioSesion->tipo == 1 || $usuarioSesion->tipo == 2) {
                    ?>
                <a style="font-size: 22px;" class="nav-link" href="<?php echo $url_base;?>modulos/peticiones/">Peticiones 
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-raised-hand" viewBox="0 0 16 16">
                <path d="M6 6.207v9.043a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H6.236a.998.998 0 0 1-.447-.106l-.33-.165A.83.83 0 0 1 5 2.488V.75a.75.75 0 0 0-1.5 0v2.083c0 .715.404 1.37 1.044 1.689L5.5 5c.32.32.5.754.5 1.207Z"/>
                <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/>
                </svg></a>
                <?php }
                }?>
            </li>
            <li class="nav-item">
                <?php if(!isset($usuarioSesion)) {?>
                <a style="margin-left: 30px;margin-top: 30px;" class="btn btn-primary" href="<?php echo $url_base;?>login.php"><b>Iniciar sesión</b><i class="bi bi-door-open-fill"></i></a>
                <a style="margin-top: 30px;" class="btn btn-success" href="<?php echo $url_base;?>registro.php" role="button">Registrarse 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-add" viewBox="0 0 16 16">
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Zm-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path d="M2 13c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Z"/>
                </svg></a>
                <?php }
                else
                {
                    echo "<h6><b>Bienvenido: $usuarioSesion->nombreCompleto</b></h6>";
                    echo "<a style='margin-left: 30px;' class='btn btn-danger' href='".$url_base."/logout.php'>Cerrar sesión <i class='bi bi-door-closed-fill'></i></a>";
                }?>
                <?php if(isset($usuarioSesion) && $usuarioSesion->tipo == 3) {?>
                <div style="z-index: 99999;margin-left: 30px;" class="dropdown open">
                <br/>
                    <button class="btn btn-primary dropdown-toggle" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                               Perfil
                            </button>
                    <div class="dropdown-menu" aria-labelledby="triggerId">
                        <a class="dropdown-item" href="#">Ver carrito</a>
                    </div>
                </div>
                <?php }?>
            </li>
            <li style="margin-left: 50px;" class="nav-item"><img height="100px" src="<?php echo $url_base;?>resources/logo2.jpg"></li>
        </ul>
    </nav>
    </header>
    <main class="container">