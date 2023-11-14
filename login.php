<?php 
//agregar componentes
require('componentes/componentesHtml.php');
//Obtener usuarios desde la API
require_once('data/obtenerDatos.php');
require_once('models/Usuarios.php');
require_once('models/ComprasCarrito.php');
session_start();
//url desarrollo
$url_base = "http://localhost:80/SisWebGemasMeyer/modulos/productos/";
//url producción
//$url_base = "http://pachill289-001-site1.htempurl.com/modulos/productos/";
//$url_base = "http://localhost:80/SisWebGemasMeyer/modulos/productos/";
//url producción 2
//$url_base = "https://gemas-meyer-demo.great-site.net/";
 //Agregar a todos los usuarios desde la API
 $usuarios = new Usuarios();
 //agregar todos los usuarios al objeto Usuarios
 foreach (construirEndpoint('Usuario', 'ObtenerUsuarios') as $usuario) {
     $usuarios->agregarUsuario(new Usuario(
         $usuario->ci,
         $usuario->clave,
         $usuario->correo,
         $usuario->celular,
         $usuario->tipo,
         $usuario->estado,
         $usuario->nombreCompleto
     ));
 }
 /*foreach($usuarios->usuarios as $usuario){
    echo "$usuario->ci | $usuario->clave";
 }*/
 if($_POST)
 {
    foreach($usuarios->usuarios as $usuario){
        if($usuario->ci == $_POST['ci'] && $usuario->clave == $_POST['clave'])
        {
            if($usuario->estado == 1)
            {
                setcookie("usuario", json_encode($usuario), time() + 86400, "/");
                //Inicializar el cookie de carrito para almacenar de manera temporal
                //Las compras que desea realizar un cliente
                if($usuario->tipo == 3)
                {
                    $comprasCarrito = new ComprasCarrito();
                    $_SESSION['comprasCarrito'] = $comprasCarrito;
                    header('Location:index.php');
                }
                else if($usuario->tipo == 1)
                {
                    header("Location:".$url_base);
                }
            }
            else
            {
                alert('Mensaje','<b>Su usuario fue inhabilitado, por favor contáctese con el administrador del sistema</b>','Aceptar');
            }
        }
    }
    alert('Mensaje','La contraseña o el ci son incorrectos','Aceptar');
    /*function verificarIdentidad($us)
    {
        
    }
    if(isset($_POST['ci']) || isset($_POST['clave'])){
        echo(verificarIdentidad($usuarios));
    }
    else
    {
        echo 'Algún campo esta vacio';
    }*/
 }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <!-- Íconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Archivos javascript -->
    <script type="text/javascript" src="acciones/validacionUsuario.js"></script>
    <script type="text/javascript" src="acciones/efectos.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- Biblioteca sweet alert -->
    <title>Login</title>
</head>
<body style="   background-image: url(resources/Fondo.png);
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center center;
                backdrop-filter: blur(5px);">
    <div class="container">
    <?php espacio_br(3);?>
        <div class="row">
            <div class="card">
                <div class="card-header"> <b>Iniciar sesión</b> </div>
                <div class="card-body">
                    <form action="" method="post">
                    <div class="mb-3">
                      <label for="ci" class="form-label">Ci:</label>
                      <input type="number" required
                        class="form-control" name="ci" id="ci" aria-describedby="helpCi" placeholder="Ingrese su carnet de identidad">
                    </div class="mb-3">
                      <label for="clave" class="form-label">Contraseña:</label>
                      <div class="input-group">
                      <input type="password"
                        class="form-control" required name="clave" id="clave" aria-describedby="helpClave" placeholder="Ingrese su contraseña">
                        <div class="input-group-append">
                              <a id="iconoClave" class="input-group-text" onclick="mostrarClave(this);  "><i class="bi bi-eye"></i></a>
                              <a id="iconoClave2" class="input-group-text" onclick="mostrarClave(this); "><i class="bi bi-eye-slash-fill"></i></a>
                        </div>
                      </div>
                      <div class="text-center">
                        <br/>
                        <button type="submit" class="btn btn-primary">
                            Iniciar sesión
                        </button>
                        <button type="reset" class="btn btn-danger">
                            Borrar
                        </button>
                        <a class="btn btn-secondary" href="index.php">Volver</a>
                      </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>