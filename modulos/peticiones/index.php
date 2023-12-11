<?php include('../../plantillas/header.php');?>

<?php
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Usuarios.php');
    require_once('../../models/Peticiones.php');
    require_once('../../componentes/componentesHtml.php');
     //Agregar a todos los usuarios desde la API
     $usuarios = new Usuarios();
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
    //Agregar a todos los productos desde la API
    $peticiones = new Peticiones();
    //agregar todos los productos al objeto Productos (arreglo de productos)
    foreach (construirEndpoint('UsuarioPeticion', 'ObtenerPeticiones') as $peticion) {
        $peticiones->agregarPeticion(new Peticion(
            $peticion->idPeticion,
            $peticion->idUsuario,
            $peticion->productoNombre,
            $peticion->imagen,
            $peticion->cantidad,
            $peticion->especificaciones,
            $peticion->estado
        ));
    }
    //Especificaciones
    if(isset($_GET['btnEsp'])){
        alert("Especificaciones","<h5>Nombre del producto: </h5>".$_GET['txtNom']."<br>"."<h5> Características: </h5>".$_GET['txtEsp']."<br>"."<h5> Imagen: </h5> <br>".
        "<img class='img-fluid' src='".$_GET['txtImagen']."' width='250' height='250'>","Aceptar");
        //$_GET['btnEsp'] = null;
    }
    //Notificacion via whatsapp con la API de ultramsg
    if(isset($_GET['notificacion']) && isset($_GET['celular']))
    {
        if($_GET['celular'] == '')
        {
            alertAviso("Advertencia ⚠","El cliente no tiene un numero de celular registrado","Aceptar");
        }
        else
        {
            if ($_GET['estado'] == 1) {
                //API de ultramsg
                $params=array(
                'token' => 'njcren6e408opzcy',
                'to' => '591'.$_GET['celular'],
                'body' => 'Enhorabuena su petición de joya con IA ha sido aprobada y estará disponible dentro de 3 a 5 días | Detalle producto: '.$_GET['producto']." |   especificaciones: ".$_GET['txtEsp']
                );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.ultramsg.com/instance66961/messages/chat",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_SSL_VERIFYHOST => 0,
                  CURLOPT_SSL_VERIFYPEER => 0,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => http_build_query($params),
                  CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                  ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    alertAviso("Mensaje","Error no se pudo enviar la notificacion, codigo de error".$err,"Aceptar");
                } else {
                    alertAviso("Mensaje","Notificacion enviada con exito ✔","Aceptar");
                }
            }
            if ($_GET['estado'] == 2)
            {
                //API de ultramsg
                $params=array(
                'token' => 'njcren6e408opzcy',
                'to' => '591'.$_GET['celular'],
                'body' => 'Su petición de joya con IA ha sido denegada vuelva a intentarlo | Detalle: producto: '.$_GET['producto']." | especificaciones: ".$_GET['txtEsp']
                );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.ultramsg.com/instance66961/messages/chat",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_SSL_VERIFYHOST => 0,
                  CURLOPT_SSL_VERIFYPEER => 0,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => http_build_query($params),
                  CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                  ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    alertAviso("Mensaje","Error no se pudo enviar la notificacion, codigo de error".$err,"Aceptar");
                } else {
                    alertAviso("Mensaje","Notificacion enviada con exito ✔","Aceptar");
                }
            }
        }
    }

    if (isset($_GET['idPeticionAprobacion'])) {

        $url = "https://apijoyeriav2.somee.com/api/UsuarioPeticion/AprobarPeticion/".$_GET['idPeticionAprobacion'];
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
            alertAviso("Mensaje","Peticion aprobada con exito ✅","Aceptar");
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
            echo $response;
        }
    }
    if (isset($_GET['idPeticionDenegar'])) {

        $url = "https://apijoyeriav2.somee.com/api/UsuarioPeticion/DenegarPeticion/".$_GET['idPeticionDenegar'];
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
            alertAviso("Mensaje","Peticion denegada con exito ✅","Aceptar");
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
            echo $response;
        }
    }
?>
<?php if(isset($_COOKIE['usuario']))
    { $usuarioSesion = json_decode($_COOKIE['usuario']); if($usuarioSesion->tipo == 1) {?>
<h4>Listado de todas las peticiones</h4>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">nroPeticion</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Producto</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Especificaciones</th>
                            <th scope="col">Estado</th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Obtener todos los pedidos -->
                        <?php foreach ($peticiones->peticiones as $peticion) { ?>
                        <tr class="">
                            <td scope="row"><?php echo $peticion->idPeticion?></td>
                            <td><?php foreach ($usuarios->usuarios as $usuario) {
                                    if($usuario->ci == $peticion->idUsuario)
                                    {
                                        echo $usuario->nombreCompleto;
                                    }
                                }
                                ?></td>
                            <td><?php echo $peticion->productoNombre?></td>
                            <td><?php echo $peticion->cantidad?></td>
                            <td><a name="" id="" class="btn btn-primary" href="index.php?btnEsp=1&txtNom=<?php echo($peticion->productoNombre)?>&txtEsp=<?php echo($peticion->especificaciones)?>&txtImagen=<?php echo ($peticion->imagen);?>" role="button">Especificaciones <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                            </svg></a></td>
                            <td><?php echo ($peticion->estado == 0 ? 
                            "<div class='alert alert-warning' role='alert'>PENDIENTE</div>" : 
                            ($peticion->estado == 1 ? 
                            "<div class='alert alert-success' role='alert'>APROBADO</div>" : 
                            ($peticion->estado == 2 ? 
                            "<div class='alert alert-danger' role='alert'>DENEGADO</div>": 
                            "<div class='alert alert-secondary' role='alert'>ANULADO</div>"))) ?></td>
                            <?php if($peticion->estado == 0) { ?>
                            <td class="text-center">
                                    <a name="" id="" class="btn btn-primary" href="index.php?idPeticionAprobacion=<?php echo $peticion->idPeticion;?>"  role="button">Aprobar <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
                                    <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
                                    </svg></a> 
                                    <?php espacio_br(2) ?>
                                    <a name="" id="" class="btn btn-danger" href="index.php?idPeticionDenegar=<?php echo $peticion->idPeticion;?>"  role="button">Denegar <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                                    <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
                                    </svg> </a>
                            </td>
                            <?php }?>
                            <?php if($peticion->estado == 1 || $peticion->estado == 2) { ?>
                                <td class="text-center">
                                    <a name="" id="" class="btn btn-success" href="index.php?notificacion=1&celular=<?php foreach ($usuarios->usuarios as $usuario) {
                                         if($usuario->ci == $peticion->idUsuario) echo $usuario->celular;
                                         } ?>&estado=<?php echo $peticion->estado; ?>&producto=<?php echo $peticion->productoNombre; ?>" role="button">Notificar al cliente por Whatsapp <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                          </svg></a>
                                </td>
                            <?php }?>
                        </tr>
                        <?php 
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