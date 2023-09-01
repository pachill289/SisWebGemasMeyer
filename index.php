<?php
    require('componentes/componentesHtml.php');
    //obtener los prodcutos en stock
    require_once('data/obtenerDatos.php');
    require_once('models/Productos.php');
    require_once('models/ComprasCarrito.php');
    session_start();
    //recuperar la sesión del usuario si este ha iniciado sesión
   
    //Agregar el modelo para almacenar las compras de un usuario
    //Verificar si el usuario ha iniciado sesión
    //Agregar a todos los productos con stock desde la API
    if(isset($_SESSION['comprasCarrito']))
    {
        $productosCarrito = $_SESSION['comprasCarrito'];
    }
    $productos = new Productos();
    //agregar todos los productos al objeto Productos (arreglo de productos)
    foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
        $productos->agregarProducto(new Producto(
            $producto->idProducto,
            $producto->nombre,
            $producto->precio,
            $producto->cantidad,
            $producto->estado,
            $producto->imagen
        ));
    }
    //Validación compra
    if($_POST)
    {
        if(isset($_POST['habilitadoCompra'])) {
            if($_POST['habilitadoCompra'] == 1)
            {
            // Método para agregar productos al carrito
            $productoExistente = false;
            if (count($productosCarrito->compras) > 0) {
                // Verificar si el producto ya existe en el carrito
                foreach ($productosCarrito->compras as $compra) {
                    if ($compra->nombreProducto == $_POST['nombreProducto']) {
                        if($compra->cantidad<$compra->stock)
                            $compra->cantidad++;
                        $productoExistente = true;
                        break;
                    }
                }
            }
            //enviar al carrito de compras
            if (!$productoExistente) {
                // Agregar nueva compra al carrito
                $productosCarrito->agregarCompraCarrito(new CompraCarrito(
                    $_POST['ciUsuarioCompra'],
                    $_POST['nombreProducto'],
                    1,
                    $_POST['precio'],
                    $_POST['stock']
                ));
            }
            } 
        }
        else if(isset($_POST['inhabilitadoCompra']))
        {
            if($_POST['inhabilitadoCompra'] == 1 )
            {
                $_POST['inhabilitadoCompra'] = 0;
                alertOp('Mensaje','Primero debe iniciar sesión.','Aceptar');
            }
        }
        if(isset($_POST['btnCompra'])){
            setcookie("compraPaypal", json_encode($productosCarrito), time() + 3600, "/");
            header('Location:pago_paypal.php');
        }
    }
?>

<?php include('plantillas/header.php');?>
<br />
<!-- Tarea 1 hecha -->
<div class="row">
<?php foreach ($productos->productos as $producto) {?>
  <div class="col-4">
    <div class="card">
      <div class="card-body">
        <img style="width:300px;" src=<?php echo $producto->imagen?>>
        <h3 class="card-title"><?php echo $producto->nombre ?></h3>
        <h4 class="card-text"><b>Precio:</b> Bs.<?php echo $producto->precio ?></h4>
        <h4 class="card-text"><b>Cantidad:</b> <?php echo $producto->cantidad ?></h4>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Bienvenido a la página oficial gemas meyer Bolivia.</h1>
        <h4>Misión</h4>
        <p class="col-md-8 fs-4">Brindar a los clientes la alternativa de adquirir joyas al "precio justo", piedras preciosas naturales talladas en Bolivia, engarzadas en metales nobles de origen boliviano, labrados por artesanos orfebres locales, brindando empleos y generando impacto social, y a su vez logrando que llege sus manos una pieza de joyería de alta calidad.</p>
        <p class="col-md-8 fs-4"><b>Conoce mas de nosotros</b></p>
        <img width="450" height="350" src="resources/img_demostracion_2.jpg">
        <br><br>
        <a class="btn btn-primary btn-lg" href="https://www.labolivianitameyergems.com/nosotros" target="_blank">About us</a>
    </div>
    <h4>Productos disponibles para pedir:</h4>
    <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
        <!-- Asignar índices para cada imagen -->
            <?php $i=0; foreach ($productos->productos as $producto) { ?>
            <li data-bs-target="#carouselId" data-bs-slide-to="<?php echo $i?>" class="<?php 
            if($i == 0){
                echo "active";
            } ?>" aria-current="true" aria-label="Slide: <?php echo $producto->id-1?>"></li>
            <?php $i++;} $i=0;?>
        </ol>
        <div class="carousel-inner" role="listbox">
        <!-- Cargar las imagenes al carousel -->
        <?php foreach ($productos->productos as $producto) { ?>
            <div class="<?php if($i == 0){
                echo "carousel-item active";
            }
            else
            {
                echo "carousel-item";
            }?>">
            <?php echo '<img style="max-height: 400px; border-radius:20%;" class="w-50 d-block" alt="slide'.$i.'" style="border-radius: 20%;"src="'.$producto->imagen.'">'?>
            <div style="background-color: purple; width:400px;padding: 10px; height: 200px;" class="p-6 mb-4 rounded-3 carousel-caption position-absolute top-0 start-50">
                <p style="color: white;font-family:TipografiaElegante;font-size: 22px;"><b><?php echo $producto->nombre?></b>
                <br><b>Precio:</b> Bs. <?php echo $producto->precio?>
                <br><b>Actualmente quedan:</b> <?php echo $producto->cantidad?> ejemplares</p>
                <form method="post">
                    <?php if(isset($usuarioSesion) && $usuarioSesion->tipo == 3) { ?>
                    <input name="habilitadoCompra" type="number" hidden value="1">
                    <input name="ciUsuarioCompra" type="text" hidden value="<?php echo $usuarioSesion->ci ?>">
                    <input name="nombreProducto" type="text" hidden value="<?php echo $producto->nombre ?>">
                    <input name="precio" type="number" hidden value="<?php echo $producto->precio ?>">
                    <input name="stock" type="number" hidden value="<?php echo $producto->cantidad ?>">
                    <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                        Añadir al carrito
                    </button>
                    <?php 
                    }
                    else if(isset($usuarioSesion) && $usuarioSesion->tipo != 3) {  ?>
                    <input name="inhabilitadoCompra" type="number" hidden value="1">
                    <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-secondary" title="Primero inicia sesión con tu cuenta.">Añadir al carrito</button>
                    <?php }?>
                </form>
            </div>
            </div>
        <?php $i++;}?>
        </div>
        <button style="background-color: purple; width: 30px;" class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button style="background-color: purple; width: 30px;" class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!-- Modal carrito -->
    <div style="position: sticky; bottom: 500px;" class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">Carrito de pedidos <i class="bi bi-cart"></i></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                <div style="overflow-y: auto;  max-height: 500px;" class="modal-body">
                    <div>
                        <h5>Productos agregados: <?php echo (count($productosCarrito->compras));?></h5>
                        <?php
                        // Obtener el carrito de compras de la sesión
                        $total = 0;
                        $numCant = 1;
                        if (isset($_SESSION['comprasCarrito'])) {
                            // Mostrar los productos agregados al carrito
                            foreach ($productosCarrito->compras as $producto) {
                                echo "<hr/>";
                                echo "<h5>Nombre producto: </h5>";
                                echo "<h6>$producto->nombreProducto</h6>";
                                echo "<h5>Precio: </h5>";
                                echo "<h6>Bs. $producto->precio</h6>";
                                echo "
                                <form name='formCantidad' method='post'>
                                <label for='cantidad' class='form-label'>Cantidad</label>
                                <input type='number'class='form-control'min=1 max='".$producto->stock."' name='cantidad".$numCant."' value='$producto->cantidad'>";
                                $total += $producto->precio*$producto->cantidad;
                                $numCant++;
                            }
                            echo "
                            <hr/>
                            <label for='cantidad' class='form-label'>Total:</label>
                            Bs.<input type='number' readonly class='form-control' name='cantidad' value='$total'>
                            <button style='margin-top: 20px;' type='submit' class='btn btn-success'>
                            Guardar cambios <i class='bi bi-cart-check'></i>
                            </button>
                            </form>";
                        }
                    ?>
                    </div>
                </div>
                <div style="overflow-y: auto;  max-height: 300px;" class="modal-footer">
                    <a name="" id="" class="btn btn-danger" href="borrar_carrito.php" role="button">Vaciar carrito <i class="bi bi-trash"></i></a>
                    <form method="post">
                        <button name="btnCompra" type="submit" class="btn btn-primary">
                            Realizar pedido <i class="bi bi-cart-check-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        var modalId = document.getElementById('modalId');
        modalId.addEventListener('show.bs.modal', function (event) {
              // Button that triggered the modal
              let button = event.relatedTarget;
              // Extract info from data-bs-* attributes
              let recipient = button.getAttribute('data-bs-whatever');
    
            // Use above variables to manipulate the DOM
        });
    </script>
</div>
<?php
        //SIN USO
        //función para devolver un json a partir de un array
        // Acceder a los datos y trabajar con ellos, JSON_UNESCAPED_UNICODE permite ver los datos con   la tílde
        /*function decodificar_json($arrayObjetos)
        {
            return json_encode($arrayObjetos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        echo (decodificar_json(construirEndpoint('Usuario', 'ObtenerUsuarios')));
        echo '<br>';
        //devolver un objeto especifico
        echo (decodificar_json(construirEndpoint('Usuario', 'ObtenerUsuarios')[0]));
        echo '<br>';
        //devolver una propiedad de un objeto especifico
        echo (decodificar_json(construirEndpoint('Usuario', 'ObtenerUsuarios')[0]->nombreCompleto));
        //Agregar a todos los usuarios desde el endpoint
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
        echo "<br>";
        session_start();
        $_SESSION['usuarios'] = $usuarios;
        // Acceder al array de usuarios, ver a todos los usuarios
        foreach ($usuarios->usuarios as $usuario) {
            echo "CI: $usuario->ci | Clave: $usuario->clave | Correo: $usuario->correo | Tipo: " . ($usuario->tipo == 1 ? "Administrador" : ($usuario->tipo == 2 ? "Vendedor" : "Cliente")) . " | Estado: " . ($usuario->estado == 0 ? "Inactivo" : ($usuario->estado == 1 ? "Activo" : "Eliminado")) . " | Nombre completo: $usuario->nombreCompleto";
            echo "<br>";
        }
        //echo(construirEndpoint('Producto','ObtenerProductos'));
        */
?>
<?php include('plantillas/footer.php');?>