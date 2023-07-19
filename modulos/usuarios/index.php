<?php include('../../plantillas/header.php');?>
<?php
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Usuarios.php');
     //Agregar a todos los usuarios desde la API
     $usuarios = new Usuarios();
     //agregar todos los usuarios al objeto Usuarios
     foreach (construirEndpoint('Usuario', 'ObtenerUsuarios') as $usuario) {
         $usuarios->agregarUsuario(new Usuario(
             $usuario->ci,
             $usuario->clave,
             $usuario->correo,
             $usuario->tipo,
             $usuario->estado,
             $usuario->nombreCompleto
         ));
     }
    //Si el usuario presiona el botón "si" en el modal para anular a un usuario...
    //Anular a un usaurio por el ci por el método PUT personalizado
    if (isset($_GET['ciAnu'])) {

        $url = "http://apijoyeriav2.somee.com/api/Usuario/AnularUsuarioWeb/".$_GET['ciAnu'];

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Establecer el encabezado "Content-Length" si se trata de un solo dato
        $contentLength = strlen($_GET['ciAnu']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Length: ' . $contentLength
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
            header('Location:index.php');
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
        }
    }
    if (isset($_GET['ciAct'])) {

        $url = "http://apijoyeriav2.somee.com/api/Usuario/ActivarUsuarioWeb/".$_GET['ciAct'];

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Establecer el encabezado "Content-Length"
        $contentLength = strlen($_GET['ciAct']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Length: ' . $contentLength
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
            header('Location:index.php');
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
        }
    }
?>
<br/>
    <h4>Lista de todos los usuarios</h4>
    <div class="card">
        <div class="card-body">
        <a name="" id="" class="btn btn-primary btn-lg" href="crear.php"   role="button">Registrar un nuevo usuario <i class="bi bi-file-earmark-person"></i></a>
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">CI</th>
                            <th scope="col">Nombre completo</th>
                            <th scope="col">Clave</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Estado</th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Obtener a todos los usuarios mediante el objeto $usuarios -->
                        <?php foreach ($usuarios->usuarios as $usuario) { ?>
                            <tr class="">
                                <td scope="row"><?php echo $usuario->ci?></td>
                                <td><?php echo $usuario->nombreCompleto?></td>
                                <td><?php echo $usuario->clave?></td>
                                <td><?php echo $usuario->correo?></td>
                                <td>
                                    <?php echo ($usuario->tipo == 1 ? "Administrador" : ($usuario->tipo == 2 ? "Vendedor" : "Cliente"))?>
                                </td>
                                <td>
                                    <?php echo ($usuario->estado == 0 ? "Inactivo" : 
                                    ($usuario->estado == 1 ? "Activo" : "Eliminado"))?>
                                </td>
                                <td class="text-center">
                                    <a name="" id="" class="btn btn-success" href="editar.php?txtCi=<?php echo $usuario->ci;?>&txtNombre=<?php echo $usuario->nombreCompleto;?>&txtClave=<?php echo $usuario->clave?>&txtCorreo=<?php echo $usuario->correo?>&txtTipo=<?php echo $usuario->tipo?>"  role="button">Editar <i class="bi bi-pencil-square"></i> </a> |
                                    <a name="" id="" class="btn btn-danger" href="index.php?ciAnu=<?php echo $usuario->ci;?>"  role="button">Anular <i class="bi bi-arrow-down-circle"></i> </a>
                                    <br/><br/> <a name="" id="" class="btn btn-primary" href="index.php?ciAct=<?php echo $usuario->ci;?>"  role="button">Activar <i class="bi bi-arrow-up-circle"></i> </a>
                                </td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Opcional -->
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">Anular usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Desea anular a este usuario?</p>
                    <b><span id="usuarioNombre"></span></b>
                </div>
                <div class="modal-footer">
                    <form method="post" action="index.php">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                        <button name="btnAnular" type="submit" class="btn btn-primary">Si</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Optional: Place to the bottom of scripts -->
    <script>
        const modal = document.getElementById('modalId');
        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Botón que activó el modal
            var nombre = button.getAttribute('data-nombre'); // Obtener el atributo data-nombre
            var usuarioNombre = document.getElementById('usuarioNombre');
            usuarioNombre.textContent = nombre; // Establecer el nombre del usuario en el modal
        });
    </script>
<?php include('../../plantillas/footer.php');?>