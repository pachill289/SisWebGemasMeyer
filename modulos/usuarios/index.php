<?php include('../../plantillas/header.php');?>
<?php
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Usuarios.php');
    require_once('../../componentes/componentesHtml.php');
    //Agregar a todos los usuarios desde la API
    $usuarios = null;
    function agregarUsuarios($subCat)
    {
        $us = new Usuarios();
        //agregar a todos los usuarios al objeto Usuarios
        foreach (construirEndpoint('Usuario', $subCat) as $usuario) {
            $us->agregarUsuario(new Usuario(
                $usuario->ci,
                $usuario->clave,
                $usuario->correo,
                $usuario->celular,
                $usuario->tipo,
                $usuario->estado,
                $usuario->nombreCompleto
            ));
        }
        return $us;
    }
    function buscarUsuarioPorNombre($subCat,$nombre)
    {
        $us = new Usuarios();
        //agregar a todos los usuarios al objeto Usuarios
        foreach (construirEndpointParametro('Usuario',$subCat,$nombre) as $usuario) {
            $us->agregarUsuario(new Usuario(
                $usuario->ci,
                $usuario->clave,
                $usuario->correo,
                $usuario->celular,
                $usuario->tipo,
                $usuario->estado,
                $usuario->nombreCompleto
            ));
        }
        return $us;
        //echo (construirEndpointParametro('Usuario',$subCat,$nombre)->clave);
    }
    $subCategoria = 'ObtenerUsuarios';
    $usuarios = agregarUsuarios($subCategoria);
    //Opciones para buscar y
    if (isset($_POST['botonListar'])) {
        $subCategoria = 'ObtenerUsuarios';
        $usuarios = agregarUsuarios($subCategoria);
    } 
    else if (isset($_POST['botonListar2'])) {
        //Buscar el usuario por su nombre
        $nombreUs = $_POST['nombreBusqueda']; 
        $subCategoria = 'ObtenerUsuarioPorNombre';
        if($nombreUs != "")
        {
            $usuarios = buscarUsuarioPorNombre($subCategoria,$nombreUs);
        }
    }
    //Si el usuario presiona el botón "si" en el modal para anular a un usuario...
    //Anular a un usaurio por el ci por el método PUT personalizado
    if (isset($_GET['ciAnu'])) {

        $url = "https://apijoyeriav2.somee.com/api/Usuario/AnularUsuarioWeb/".$_GET['ciAnu'];
        echo $url;
        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
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
            alertAviso("Mensaje","Usuario anulado con éxito ✅","Aceptar");
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
            echo $response;
        }
    }
    if (isset($_GET['ciAct'])) {

        $url = "https://apijoyeriav2.somee.com/api/Usuario/ActivarUsuarioWeb/".$_GET['ciAct'];

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
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
            alertAviso("Mensaje","Usuario activado con éxito ✅","Aceptar");
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
        }
    }
?>
<br/>
<?php if(isset($_COOKIE['usuario']))
    { $usuarioSesion = json_decode($_COOKIE['usuario']); if($usuarioSesion->tipo == 1) {?>
    <h4>Lista de todos los usuarios</h4>
    <div class="card">
        <div class="card-body">
        <nav class="navbar navbar-expand navbar-light bg-light">
        <ul class="nav navbar-nav">
            <li class="nav-item">
            <a name="" id="" class="btn btn-primary btn-lg" href="crear.php" role="button">
                Registrar un nuevo usuario <i class="bi bi-file-earmark-person"></i>
            </a>
            </li>
            <form method="post">
                <li class="nav-item" style="margin-left: 20px;">
                    <div class="mb-3">
                        <label for="" class="form-label">Buscar usuario:</label>
                        <input type="search" size="50"
                            class="form-control" name="nombreBusqueda" placeholder="Ingrese el nombre del usuario">
                    </div>
                </li>
                <li class="nav-item" style="margin: 10px;">
                    <button class="btn btn-primary" type="submit" name="botonListar2">
                    Buscar usuario <i class="bi bi-search"></i>
                    </button>
                </li>
                <li class="nav-item" style="margin: 10px;">
                    <button class="btn btn-primary" type="submit" name="botonListar">
                    Listar usuarios <i class="bi bi-people-fill"></i>
                    </button>
                </li>
            </form>
        </ul>
        </nav>
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">CI</th>
                            <th scope="col">Nombre completo</th>
                            <th scope="col">Clave</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Celular</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Estado</th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Obtener a todos los usuarios mediante el objeto $usuarios -->
                        <?php
                        if(count($usuarios->usuarios)>0)
                        {
                        foreach ($usuarios->usuarios as $usuario) { ?>
                            <tr class="">
                                <td scope="row"><?php echo $usuario->ci?></td>
                                <td><?php echo $usuario->nombreCompleto?></td>
                                <td><?php echo $usuario->clave?></td>
                                <td><?php echo $usuario->correo?></td>
                                <td><?php echo ($usuario->celular != '' ? $usuario->celular : 'N/A')?></td>
                                <td>
                                    <?php echo ($usuario->tipo == 1 ? "Administrador" : ($usuario->tipo == 2 ? "Vendedor" : "Cliente"))?>
                                </td>
                                <td>
                                    <?php echo ($usuario->estado == 0 ? "<div class='alert alert-secondary' role='alert'>INACTIVO</div>" : 
                                    ($usuario->estado == 1 ? "<div class='alert alert-primary' role='alert'>ACTIVO</div>" : "Eliminado"))?>
                                </td>
                                <td class="text-center">
                                    <a name="" id="" class="btn btn-success" href="editar.php?txtCi=<?php echo $usuario->ci;?>&txtNombre=<?php echo $usuario->nombreCompleto;?>&txtClave=<?php echo $usuario->clave?>&txtCorreo=<?php echo $usuario->correo?>&txtCelular=<?php echo $usuario->celular?>&txtTipo=<?php echo $usuario->tipo?>"  role="button">Editar <i class="bi bi-pencil-square"></i></a> 
                                    <?php espacio_br(2) ?>
                                    <a name="" id="" class="btn btn-danger" href="index.php?ciAnu=<?php echo $usuario->ci;?>"  role="button">Anular <i class="bi bi-arrow-down-circle"></i> </a>
                                    <?php espacio_br(2) ?> <a name="" id="" class="btn btn-primary" href="index.php?ciAct=<?php echo $usuario->ci;?>"  role="button">Activar <i class="bi bi-arrow-up-circle"></i> </a>
                                </td>
                            </tr>
                        <?php }
                        }
                        else
                        {
                            echo "<tr>
                            <div class='alert alert-danger' role='alert'>La búsqueda no coincide con ningún usuario</div>
                            </tr>";
                        }?>
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