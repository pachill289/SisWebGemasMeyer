<?php include('../../plantillas/header.php');?>
<?php
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Publicaciones.php');
    require_once('../../models/Productos.php');
    require_once('../../componentes/componentesHtml.php');
    $productos = new Productos();
    foreach (construirEndpoint('Producto', 'ObtenerProductos') as $producto) {
        $productos->agregarProducto(new Producto(
            $producto->idProducto,
            $producto->nombre,
            $producto->precio,
            $producto->cantidad,
            $producto->estado,
            $producto->imagen,
            $producto->categoria
        ));
    }
    //Agregar a todas las publicaciones desde la API
    $publicaciones = new Publicaciones();
    foreach (construirEndpoint('UsuarioPublicacion', 'ObtenerPublicaciones') as $publicacion) {
        $publicaciones->agregarPublicacion(new Publicacion(
            $publicacion->idPublicacion,
            $publicacion->titulo,
            $publicacion->descripcion,
            $publicacion->imagen,
            $publicacion->estado,
            $publicacion->tipo,
            $publicacion->idProducto,
            $publicacion->descuento
        ));
    }
    if($_POST)
    {
        if(isset($_POST['tipoPublicacion']))
        {
            if($_POST['tipoPublicacion'] == 3)
            {
                $publicaciones = new Publicaciones();
                foreach (construirEndpoint('UsuarioPublicacion', 'ObtenerPublicaciones') as $publicacion) {
                    if($publicacion->tipo == 2)
                    {
                        $publicaciones->agregarPublicacion(new Publicacion(
                            $publicacion->idPublicacion,
                            $publicacion->titulo,
                            $publicacion->descripcion,
                            $publicacion->imagen,
                            $publicacion->estado,
                            $publicacion->tipo,
                            $publicacion->idProducto,
                            $publicacion->descuento
                        ));
                    }
                }
            }
            else if($_POST['tipoPublicacion'] == 2)
            {
                $publicaciones = new Publicaciones();
                foreach (construirEndpoint('UsuarioPublicacion', 'ObtenerPublicaciones') as $publicacion) {
                    if($publicacion->tipo == 1)
                    {
                        $publicaciones->agregarPublicacion(new Publicacion(
                            $publicacion->idPublicacion,
                            $publicacion->titulo,
                            $publicacion->descripcion,
                            $publicacion->imagen,
                            $publicacion->estado,
                            $publicacion->tipo,
                            $publicacion->idProducto,
                            $publicacion->descuento
                        ));
                    }
                }
            }
            else
            {
                $publicaciones = new Publicaciones();
                foreach (construirEndpoint('UsuarioPublicacion', 'ObtenerPublicaciones') as $publicacion) {
                    $publicaciones->agregarPublicacion(new Publicacion(
                        $publicacion->idPublicacion,
                        $publicacion->titulo,
                        $publicacion->descripcion,
                        $publicacion->imagen,
                        $publicacion->estado,
                        $publicacion->tipo,
                        $publicacion->idProducto,
                        $publicacion->descuento
                    ));
                }
            }
        }
    }
    //Si el usuario presiona el botón "si" en el modal para anular a un usuario...
    //Anular a un usaurio por el ci por el método PUT personalizado
    if (isset($_GET['idAnu'])) {

        $url = "https://apijoyeriav2.somee.com/api/UsuarioPublicacion/AnularPublicacion/".$_GET['idAnu'];
    
        // Inicializar cURL
        $ch = curl_init($url);
    
        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);
    
        // Verificar si hubo algún error
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
        }
    
        // Obtener el código de respuesta HTTP
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // Cerrar la conexión cURL
        curl_close($ch);
    
        // Procesar la respuesta
        if ($httpCode == 200) {
           
            alertAviso("Mensaje","Publicación anulada con éxito ✅","Aceptar");
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
            echo $response;
        }
    }
    if (isset($_GET['idAct'])) {

        $url = "https://apijoyeriav2.somee.com/api/UsuarioPublicacion/ActivarPublicacion/".$_GET['idAct'];

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo algún error
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
        }

        // Obtener el código de respuesta HTTP
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Cerrar la conexión cURL
        curl_close($ch);

        // Procesar la respuesta
        if ($httpCode == 200) {
            //header('Location:index.php');
            alertAviso("Mensaje","Publicación activada con éxito ✅","Aceptar");
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
        }
    }
?>
<br/>
<?php if(isset($_COOKIE['usuario']))
    { $usuarioSesion = json_decode($_COOKIE['usuario']); if($usuarioSesion->tipo == 1) {?>
    <h4>Lista de todas las publicaciones</h4>
    <div class="card">
        <div class="card-body">
        <nav class="navbar navbar-expand navbar-light bg-light">
        <ul class="nav navbar-nav">
            <li class="nav-item">
            <a name="" id="" class="btn btn-primary btn-lg" href="crear.php" role="button">
                Registrar una nueva publicacion <i class="bi bi-send-plus"></i>
            </a>
            </li>
            <form style="margin:10px;" method="post">
                <div class="mb-3">
                    <label for="" class="form-label">Listar:</label>
                    <select class="form-select form-select-lg" name="tipoPublicacion">
                        <option value="1" selected>Todo</option>
                        <option value="2" >Publicaciones</option>
                        <option value="3">Promociones</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Listar</button>
            </form>
        </ul>
        </nav>
        <h2>Publicaciones:</h2>
            <div class="table-responsive-sm">
                <table class="table">
                    <tbody>
                        <!-- Obtener a todos los usuarios mediante el objeto $usuarios -->
                        <?php
                        $nroPublicacion = 1;
                        foreach ($publicaciones->publicaciones as $publicacion) { ?>
                        <tr style="border-bottom: solid black 3px;"></tr>
                        <tr>
                            <td><h3>Publicación número: <?php echo $nroPublicacion?></h3><p style="font-size: 22px;"><b>Título:</b> <?php echo $publicacion->titulo?></p><?php echo '<img class="img-fluid" width=480 height=200 src="'.$publicacion->imagen.'">'?></td>
                            <td class="text-center">
                            <h4>Acciones:</h4>
                                    <a class="btn btn-success" href="editar.php?txtId=<?php echo $publicacion->idPublicacion;?>&txtTitulo=<?php echo $publicacion->titulo;?>&txtDescripcion=<?php echo $publicacion->descripcion?>&txtImagen=<?php echo $publicacion->tipo?>&txtTipo=<?php echo $publicacion->tipo?>&txtEstado=<?php echo $publicacion->estado?>&txtIdProducto=<?php echo($publicacion->tipo == 2 ? $publicacion->idProducto : null)?>&txtDescuento=<?php echo($publicacion->tipo == 2 ? $publicacion->descuento : 0)?>"  role="button">Editar <i class="bi bi-pencil-square"></i> </a> 
                                    <?php espacio_br(2) ?>
                                    <a class="btn btn-danger" href="index.php?idAnu=<?php echo $publicacion->idPublicacion;?>" role="button">Anular<i class="bi bi-arrow-down-circle"></i></a>
                                    <?php espacio_br(2) ?> <a class="btn btn-primary" href="index.php?idAct=<?php echo $publicacion->idPublicacion;?>"  role="button">Activar <i class="bi bi-arrow-up-circle"></i> </a>
                            </td>
                            <td>
                            <h4>Estado:</h4>
                                <?php echo ($publicacion->estado == 0 ? "<div class='alert  alert-secondary' role='alert'>INACTIVO</div>" : 
                                ($publicacion->estado == 1 ? "<div class='alert alert-primary'  role='alert'>ACTIVO</div>" : "Eliminado"))?>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row"><h4>Descripción: </h4><?php echo $publicacion->descripcion?></td>
                        </tr>
                        <tr>
                            <td>
                                <h4>Tipo:</h4><h5><?php echo ($publicacion->tipo == 1? "Publicacion" : "Promocion") ?></h5>
                            </td>
                        </tr>
                        <?php if($publicacion->tipo == 2) { ?>
                        <tr>
                            <td>
                                <?php
                                $promocion = false;
                                foreach($productos->productos as $producto)
                                {
                                    if($publicacion->idProducto == $producto->id)
                                    {
                                        echo "<h4>Producto en promoción:</h4> $producto->nombre<tr> <td>Descuento: $publicacion->descuento%</td></tr> ";
                                        $promocion = true;
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <tr style="border-bottom: solid black 3px;"></tr>
                    <?php $nroPublicacion++; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } else {
      echo "<h1 style='color: red;'><b><center>Acceso denegado</center></b></h1>";
    }
    } else {
      echo "<h1 style='color: #b59410;'><b><center>Debe autenticarse primero</center></b></h1>";
    } ?>
<?php include('../../plantillas/footer.php');?>