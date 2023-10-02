<?php include('../../plantillas/header.php');?>
<?php
//modal
    require_once('../../componentes/componentesHtml.php');
    require_once('../../data/constantes.php');
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Usuarios.php');
    require_once('../../models/Pedidos.php');
    require_once('../../models/Productos.php');
    //Pedidos
    //Agregar a todos los pedidos desde la API
    $pedidos = new Pedidos();
     
     foreach (construirEndpoint('UsuarioPedido', 'ObtenerPedidos') as $pedido) {
         $pedidos->agregarPedido(new Pedido(
            $pedido->idPedido,
            $pedido->idUsuario,
            $pedido->idProducto,
            $pedido->estado,
            $pedido->cantidad,
            $pedido->fecha,
            $pedido->fecha_expiracion
         ));
     }
    //Limpieza manual
    //Verificar si no existe ningún pedido para deshabilitar el botón para borrar todos los pedidos
    $pedidosTotales = 0;
    //Verificar si existe algún pedido
    foreach ($pedidos->pedidos as $pedido) { 
        $pedidosTotales++;
    }
    //Verificar si existe algún pedido pendiente
    $pedidosPendientes = 0;
    foreach ($pedidos->pedidos as $pedido) { 
        if($pedido->estado == 1 || $pedido->estado == 3) {
            $pedidosPendientes++;
        }
    }
    if($_POST)
    {
        if(isset($_POST['btnBorrar']))
        {
            $url = URL_API.'/api/'.'UsuarioPedido/'.'EliminarPedidos'; // url
            //Borrar pedidos de la base de datos
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                alertAviso('Error ','Error al eliminar los pedidos: ' . curl_error($ch),'Aceptar');
            }
            curl_close($ch);
            alertAviso("Mensaje","Pedidos eliminados con éxito","Aceptar");
        }
        if(isset($_POST['btnBorrar2']))
        {
            alertAviso("Mensaje ⚠","No puede borrar todos los pedidos, porque tiene algunos pedidos pendientes o no es necesario realizar una limpieza","Aceptar");
        }
    }
    //POR HACER PARA EL SPRINT 4
    //definir una cookie que dure 1 día
    // Duración de la cookie en segundos (1 día)
    /*
    //Verificar si hoy es domingo para realizar una limpieza de pedidos
    $diaDeLaSemana = date('w');
    $tiempoExpiracion = time() + 86400;
    $limpiezaRealizada = false;
    setcookie(COOKIE_LIMPIEZA,$limpiezaRealizada ? '1' : '0', $tiempoExpiracion, '/');
    if ($diaDeLaSemana == 1 && $_COOKIE[COOKIE_LIMPIEZA] == '0') {
        // Tu código para la acción que se ejecutará los domingos
        $url = URL_API.'/api/'.'UsuarioPedido/'.'EliminarPedidos'; // url
        //Borrar pedidos de la base de datos
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            alertAviso('Error ','Error al eliminar los pedidos: ' . curl_error($ch),'Aceptar');
        }
        curl_close($ch);
        $limpiezaRealizada = true;
        setcookie(COOKIE_LIMPIEZA,$limpiezaRealizada ? '1' : '0', $tiempoExpiracion, '/');
        alertAviso("Aviso ⚠","Hoy es domingo, el sistema realizó una limpieza de pedidos".$response,"Aceptar");
    }*/
    if($_GET)
    {
        //Concretar venta
        if(isset($_GET['idPedido']) && isset($_GET['idProducto']) && isset($_GET['cantidadPedido']))
        {
            $productoAgotado = false;
            //Verificar si el producto se ha agotado
            foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
                if($producto->idProducto == $_GET['idProducto'])
                {
                    if($producto->cantidad == 0)
                    {
                        $productoAgotado = true;
                    }
                }
            }
            if(!$productoAgotado)
            {
                // Datos de la solicitud
                $pedidoId = $_GET['idPedido'];
                $productoId = $_GET['idProducto'];
                $cantidadPedido = $_GET['cantidadPedido'];

                // URL de la API con los parámetros de ruta y consulta
                $url = "https://apijoyeriav2.somee.com/api/UsuarioPedido/ConcretarPedido/{$pedidoId}/   {$productoId}?cantidadPedido={$cantidadPedido}";

                // Datos del cuerpo del PUT como un arreglo
                $datosPUT = array(
                    'idPedido' => $_GET['idPedido'],
                    'idProducto' => $_GET['idProducto'],
                    'cantidadPedido' => $_GET['cantidadPedido']
                );

                // Convertir el arreglo a formato JSON
                $datosJSON = json_encode($datosPUT);

                // Obtener la longitud del cuerpo en bytes
                $contentLength = strlen($datosJSON);

                // Inicializar cURL
                $ch = curl_init();

                // Establecer opciones de cURL
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Método PUT
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Establecer el encabezado "Content-Type"
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . $contentLength
                ));
                // Si es necesario, incluir los datos del body en el PUT
                /*if ($datosPUT) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $datosPUT);
                }*/

                // Ejecutar la solicitud y obtener la respuesta
                $response = curl_exec($ch);

                // Verificar si hubo algún error
                if ($response === false) {
                    echo 'Error: ' . curl_error($ch);
                }
                else
                {
                    alertAviso("Mensaje","Pedido concretado con éxito ✅","Aceptar");
                }
            }
            else
            {
                alertAviso("Mensaje ⚠","El producto se ha agotado","Aceptar");
            }
            
        
            // Obtener el código de respuesta HTTP (No funciona)
            /*$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            // Cerrar la conexión cURL
            curl_close($ch);
        
            // Procesar la respuesta
            if ($httpCode == 200) {
                
            } else {
                echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
            }*/
        }
        //Anular pedido
        if (isset($_GET['idAnular'])) {

            $url = "https://apijoyeriav2.somee.com/api/UsuarioPedido/AnularPedido/".$_GET['idAnular'];
    
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
                alertAviso("Mensaje","Pedido anulado con éxito ✅","Aceptar");
            } else {
                echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
            }
        }
    }
?>
<?php

     //Agregar a todos los usuarios desde la API
     $usuarios = new Usuarios();
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
    //Agregar a todos los productos desde la API
    $productos = new Productos();
    //agregar todos los productos al objeto Productos (arreglo de productos)
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
     
?>
<h4>Listado de todos los pedidos</h4>
    <!-- Limpieza de pedidos -->
    <form method="post">
        <?php if($pedidosPendientes == 0 && $pedidosTotales != 0) {?>
        <button name="btnBorrar" title="Se recomienda hacerlo cada domingo o cuando no tiene ningún pedido pendiente" type="submit" class="btn btn-danger">
            Realizar limpieza manual de pedidos <i class="bi bi-trash-fill"></i>
        </button>
        <?php } else {?>
        <button name="btnBorrar2" title="⚠ Tiene algunos pedidos pendientes o ya realizó una limpieza" type="submit" class="btn btn-secondary">
            Realizar limpieza manual de pedidos <i class="bi bi-trash-fill"></i>
        </button>
        <?php }?>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">nroPedido</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Producto</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col" title="Fecha en la cual el usuario pidió el producto">Fecha pedido</th>
                            <th scope="col" title="Fecha de expiración del pedido, esto indica que el pedido va a expirar dentro de tres días">Fecha expiración</th>
                            <th scope="col"><b>Estado</b></th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Obtener todos los pedidos -->
                        <?php foreach ($pedidos->pedidos as $pedido) { 
                            if($pedido->estado == 1 || $pedido->estado == 3) { ?>
                        <tr class="">
                            <td scope="row"><?php echo $pedido->idPedido?></td>
                            <td><?php foreach ($usuarios->usuarios as $usuario) {
                                    if($usuario->ci == $pedido->idUsuario)
                                    {
                                        echo $usuario->nombreCompleto;
                                    }
                                }
                                ?></td>
                            <td><?php foreach ($productos->productos as $producto) {
                                    if($producto->id == $pedido->idProducto)
                                    {
                                        if($producto->cantidad == 0)
                                        {
                                            echo "<div class='alert alert-danger' role='alert'>¡Producto agotado!</div>";
                                        }
                                        else
                                        {
                                            echo $producto->nombre;
                                        }
                                    }
                                }
                                ?></td>
                            <td><?php echo $pedido->cantidadProducto?></td>
                            <td><?php echo ((new DateTime($pedido->fecha))->format('d-m-y'))?></td>
                            <td><?php echo ((new DateTime($pedido->fecha_expiracion))->format('d-m-y'))?></td>
                            <td><?php echo ($pedido->estado == 1 ? 
                            "<div class='alert alert-success' role='alert'>ENTREGADO</div>" : 
                            ($pedido->estado == 2 ? 
                            "<div class='alert alert-danger' role='alert'>NO ENTREGADO</div>" : 
                            ($pedido->estado == 3 ? 
                            "<div class='alert alert-warning' role='alert'>PENDIENTE</div>": 
                            "<div class='alert alert-secondary' role='alert'>ANULADO</div>"))) ?></td>
                            <td class="text-center">
                                <?php
                                foreach ($productos->productos as $producto) {
                                if($producto->id == $pedido->idProducto)
                                { 
                                if($pedido->estado != 1 && $producto->cantidad != 0) { ?>
                                <a class="btn btn-primary" href="index.php?idPedido=<?php echo $pedido->idPedido;?>&idProducto=<?php echo $pedido->idProducto;?>&cantidadPedido=<?php echo $pedido->cantidadProducto;?>">
                                Concretar pedido <i class="bi bi-bag-check-fill"></i>
                                </a>
                                <hr/>
                                <?php }
                                }
                                }?>
                                <a name="" id="" class="btn btn-danger" href="index.php?idAnular=<?php echo $pedido->idPedido;?>" role="button">Anular pedido <i class="bi bi-arrow-down-circle"></i></a>
                            </td>
                        </tr>
                        <?php } 
                    }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include('../../plantillas/footer.php');?>