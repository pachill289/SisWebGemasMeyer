<!-- Estilos slider -->
<style>
    .containerSli input[type="number"] {
        width: 55px;
        /* Reducido el ancho */
        height: 20px;
        /* Reducido la altura */
        background: #fff;
        border: 1px solid #ddd;
        font-size: 10px;
        /* Reducido el tamaño de fuente */
        font-weight: 600;
        text-align: center;
        border-radius: 5px;
    }

    .containerSli .range-slider {
        position: relative;
        width: 250px;
        /* Reducido el ancho */
        height: 3px;
        /* Reducido la altura */
        background: #ddd;
        outline: none;
        top: 1px;
        margin: 5px;
        /* Reducido el margen */
    }

    .containerSli .range-slider .progress {
        left: auto;
        right: auto;
        height: 100%;
        background-image: linear-gradient(10deg, #cb00a0, #d72fb1, #e248c3, #ed5dd4, #f871e6);
        border-radius: 50px;
        position: absolute;
    }

    .containerSli .range-slider input[type="range"] {
        position: absolute;
        top: -8px;
        left: -2px;
        width: 101%;
        -webkit-appearance: none;
        pointer-events: none;
        background: none;
        outline: none;
    }

    .containerSli .range-slider input::-webkit-slider-thumb {
        pointer-events: auto;
        -webkit-appearance: none;
        width: 20px;
        /* Reducido el ancho */
        height: 20px;
        /* Reducido la altura */
        background: #CB00A0;
        border-radius: 50px;
    }

    /* Oculta las flechas de incremento y decremento en los inputs de tipo number */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        appearance: none;
        margin: 0;
    }
</style>

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
if (isset($_SESSION['comprasCarrito'])) {
    $productosCarrito = $_SESSION['comprasCarrito'];
}
$productos = new Productos();

//Validación compra
if ($_POST) {
    if (isset($_POST['habilitadoCompra'])) {
        if ($_POST['habilitadoCompra'] == 1) {
            // Método para agregar productos al carrito
            $productoExistente = false;
            if (count($productosCarrito->compras) > 0) {
                // Verificar si el producto ya existe en el carrito
                foreach ($productosCarrito->compras as $compra) {
                    if ($compra->nombreProducto == $_POST['nombreProducto']) {
                        if ($compra->cantidad < $compra->stock)
                            $compra->cantidad++;
                        $productoExistente = true;
                        break;
                    }
                }
            }
            //enviar al carrito de pedidos
            if (!$productoExistente) {
                // Agregar nuevo pedido al carrito
                $productosCarrito->agregarCompraCarrito(new CompraCarrito(
                    $_POST['ciUsuarioCompra'],
                    $_POST['idProducto'],
                    $_POST['nombreProducto'],
                    1,
                    $_POST['precio'],
                    $_POST['stock']
                ));
            }
        }
    } else if (isset($_POST['inhabilitadoCompra'])) {
        if ($_POST['inhabilitadoCompra'] == 1) {
            $_POST['inhabilitadoCompra'] = 0;
            alertAviso('Mensaje', 'Primero debe iniciar sesión.', 'Aceptar');
        }
    }
    if (isset($_POST['btnCompra'])) {
        //setcookie("pedido", json_encode($productosCarrito), time() + 3600, "/");
        date_default_timezone_set('America/La_Paz');
        $fecha_hora_actual = date("Y-m-d");
        foreach ($productosCarrito->compras as $pedido) {
            // Datos del body
            $datosUsuario = array(
                "idUsuario" => $pedido->ciUsuario,
                "idProducto" => intval($pedido->idProducto),
                "estado" => 3,
                "cantidad" => $pedido->cantidad,
                "fecha" => $fecha_hora_actual
            );

            // Convertir el body a formato JSON
            $jsonData = json_encode($datosUsuario);

            // URL de la API
            $url = "http://apijoyeriav2.somee.com/api/UsuarioPedido/RegistrarPedido";

            // Configurar el flujo de contexto
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => $jsonData
                )
            ));

            // Realizar la solicitud POST
            $response = file_get_contents($url, false, $context);

            // Verificar si la solicitud fue exitosa
            if ($response === false) {
                $httpCode = http_response_code();
                alertAviso("Error", "El pedido no se ha realizado debido a un error", "Aceptar");
                echo "Error en la solicitud, el pedido no se pudo registrar por un error: $httpCode";
                // Manejar el error de la API aquí
            }
        }
        $productosCarrito->quitarCompras();
    }
    //filtrado
    if (isset($_POST['precioMin']) && isset($_POST['precioMax'])) {
        //echo "precio mínimo: ".$_POST['precioMin']."precio máximo: ".$_POST['precioMax'];
        foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
            if ($producto->precio >= $_POST['precioMin'] && $producto->precio <= $_POST['precioMax'])
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
        if (count($productos->productos) == 0) {
            alertAviso("Mensaje ⚠", "Lo sentimos no pudimos encontrar ningún producto que este en ese rango de precios, por favor vuelva a filtrar por el precio", "Aceptar");
        }
    }
    //filtrado por categorías
    else if (isset($_POST['cat'])) {
        foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
            if ($producto->categoria == $_POST['cat'])
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
        if (count($productos->productos) == 0) {
            alertAviso("Mensaje ⚠", "Lo sentimos no pudimos encontrar ningún producto con esa categoría en este momento.", "Aceptar");
        }
    } else {
        foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
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
    }
} else {
    foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
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
}
?>

<div class="mb-3">
    <label class="visually-hidden" for="inputName">Hidden input label</label>

</div>
<?php include('plantillas/header.php'); ?>
<nav style="width:100%;z-index:9999;" class="navbar navbar-expand navbar-light bg-light sticky-top">
    <div class="container">
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="#" aria-current="page">
                        <h3>Inicio</h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#joyas">
                        <h3>Catálogo de joyas</h3>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<br />
<div class="p-5 mb-4 bg-light rounded-3">
    <h1 class="display-5 fw-bold">Bienvenido a la página oficial gemas meyer Bolivia.</h1>
    <div class="container-fluid py-5">
        <h4>Misión</h4>
        <p class="col-md-8 fs-4">Brindar a los clientes la alternativa de adquirir joyas al "precio justo", piedras preciosas naturales talladas en Bolivia, engarzadas en metales nobles de origen boliviano, labrados por artesanos orfebres locales, brindando empleos y generando impacto social, y a su vez logrando que llege a sus manos una pieza de joyería de alta calidad.</p>
        <p class="col-md-8 fs-4"><b>Conoce mas de nosotros</b></p>
        <img width="450" height="350" src="resources/img_demostracion_2.jpg">
        <br><br>
        <a class="btn btn-primary btn-lg" href="https://www.facebook.com/profile.php?id=100089640294548" target="_blank">Visitar Museo Gemológico <i class="bi bi-facebook"></i></a>
    </div>
</div>
<div id="joyas" style="margin-bottom: 70px;" class="card">
    <div class="card-body">
        <h3 class="card-title">Catálogo</h3>
    </div>
</div>
<!-- Tarea 2 barra lateral -->
<aside style="float: left;margin-left:-100px;margin-right:10px;" class="col-sm-auto bg-light sticky-top">
    <div class="container-fluid">
        <div class="row">
            <div style="overflow-y:auto;max-height:500px;" class="d-flex flex-sm-column flex-row flex-nowrap bg-light align-items-center sticky-top">
                <!-- En esta parte se define un formulario para registrar los valores mínimos
                y máximos, además se actualiza mediante el evento onchange cada valor. nota:
                los valores que se obtienen no son dinámicos es decir que son valores aproximados, en el campo value se actualiza de acuerdo a la última selección que hizo el usuario -->
                <div class="range text-center">
                    <form method="post" id="filtroForm">
                            <?php espacio_br(1) ?>
                            <h2>Buscar joyas</h2>
                            <p> Filtrado por nombre</p>
                            <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" placeholder="Ingrese el nombre del producto" style="width: 300px; padding: 10px; border: 2px solid #ed5dd4; border-radius: 5px; font-size: 16px; box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);">
                            <?php espacio_br(1) ?>
                            <h4>Filtrar por precio en Bs.</h4>
                            <?php espacio_br(1) ?>
                        <div class="containerSli">
                            <div class="min-value numberVal" style="display: inline-flex; margin-right: 25px; margin-left: 5px;">
                                <label>Mínimo </label>
                                <input name="precioMin" type="number" onchange="valorInput(this.value)" value=<?php echo (isset($_POST['precioMin']) ? $_POST['precioMin'] : "304") ?> min="304" max="14000" id="inputPrecioMin" aria-describedby="helpId" style="text-align: center;font-size:medium;">
                            </div>
                            <div class="max-value numberVal" style="text-align: right; display: inline-flex;">
                                <label for="">Máximo </label>
                                <input name="precioMax" type="number" onchange="valorInput2(this.value)" max="14000" min="577" value=<?php echo (isset($_POST['precioMax']) ? $_POST['precioMax'] : "14000") ?> id="inputPrecioMax" aria-describedby="helpId" style="text-align: center;font-size:10px;font-size:medium;">
                            </div>
                            <br>
                            &nbsp;
                            <div class="range-slider">
                                <div class="progress"></div>
                                <input name="sliderPrecios" onchange="valorRange(this.value)" type="range" min="304" max="14000" step="10" value=<?php echo (isset($_POST['precioMin']) ? $_POST['precioMin'] : "304") ?> class="range-min" id="customRange1" />

                                <input name="sliderPrecios2" onchange="valorRange2(this.value)" type="range" min="577" max="14000" value=<?php echo (isset($_POST['precioMax']) ? $_POST['precioMax'] : "14000") ?> class="range-max" id="customRange2" />
                            </div>
                        </div>
                        <?php espacio_br(1) ?>
                        <button type="submit" class="btn btn-primary">Filtrar 🔍</button>
                        <button type="button" onclick="limpiarFiltros()" class="btn btn-danger">Limpiar Filtros 🧹</button>


                        <script>
                            const range = document.querySelectorAll('.range-slider input');
                            const progress = document.querySelector('.range-slider .progress');
                            let gap = 1000;
                            const inputValue = document.querySelectorAll('.numberVal input');

                            range.forEach(input => {
                                input.addEventListener('input', e => {
                                    let minrange = parseInt(range[0].value),
                                        maxrange = parseInt(range[1].value);

                                    if (maxrange - minrange < gap) {
                                        if (e.target.className === "range-min") {
                                            range[0].value = maxrange - gap;
                                            minrange = maxrange - gap; // Actualiza minrange cuando ajustas maxrange
                                        } else {
                                            range[1].value = minrange + gap;
                                            maxrange = minrange + gap; // Actualiza maxrange cuando ajustas minrange
                                        }
                                    } else {
                                        progress.style.left = (minrange / range[0].max) * 100 + '%';
                                        progress.style.right = 100 - (maxrange / range[1].max) * 100 + '%';
                                    }

                                    inputValue[0].value = minrange;
                                    inputValue[1].value = maxrange;
                                });
                            });
                        </script>
                        <script>
                            function limpiarFiltros() {
                                // Restablecer los campos de búsqueda y filtros
                                document.getElementById('nombreProducto').value = '';
                                document.getElementById('inputPrecioMin').value = '304'; // Valor mínimo
                                document.getElementById('inputPrecioMax').value = '14000'; // Valor máximo
                                document.getElementById('customRange1').value = '304'; // Valor mínimo del rango
                                document.getElementById('customRange2').value = '14000'; // Valor máximo del rango

                                // Enviar el formulario para actualizar la página
                                document.getElementById('filtroForm').submit();
                            }
                        </script>



                    </form>
                </div>
                <h4>Categorías:</h4>

                <ul class="nav nav-pills nav-flush flex-sm-column flex-row flex-nowrap mb-auto mx-auto text-center align-items-left">
                    <li class="nav-item">
                        <form method="post">
                            <input name="cat" type="text" hidden value="Anillo">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                <i class="bi bi-gem"></i> Anillos
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Cadena">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                <i class="bi bi-gem"></i> Cadenas
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Collar">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                <i class="bi bi-gem"></i> Collares
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Juego">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                <i class="bi bi-gem"></i> Juegos
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Dije">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                <i class="bi bi-gem"></i> Dijes
                            </button>
                        </form>
                    </li>
                    <li>
                        <a href="#" class="nav-link py-3 px-2" title="Ir arriba" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Customers">
                            <i class="bi-arrow-bar-up fs-1"></i>
                        </a>
                    </li>
                </ul>
                </form>
            </div>
            <div class="col-sm p-3 min-vh-100">
                <!-- content -->
            </div>
        </div>
    </div>
</aside>
<!-- Tarea 1 hecha -->
<div class="row">
    <?php
    $productosMostrados = $productos->productos; // Por defecto, mostrar todos los productos

    // Verificar si se ha enviado el formulario de búsqueda por nombre
    if ($_POST && isset($_POST['nombreProducto'])) {
        $nombreBuscado = $_POST['nombreProducto'];
        $productosFiltrados = array();

        // Recorrer todos los productos y filtrar los que coincidan con el nombre buscado
        foreach ($productos->productos as $producto) {
            if (stripos($producto->nombre, $nombreBuscado) !== false) {
                $productosFiltrados[] = $producto;
            }
        }

        // Usar los productos filtrados en lugar de todos los productos
        $productosMostrados = $productosFiltrados;
    }

    // Mostrar los productos (ya sean todos o los filtrados)
    if (count($productosMostrados) == 0) {
        echo '<h1>No se encontró ningún producto,vuelva a intentarlo</h1>';
        echo '<script>
            document.getElementById("search-message").style.display = "block";
            setTimeout(function(){
                document.getElementById("search-message").style.display = "none";
            }, 2000); // Mostrar durante 2 segundos
        </script>';
    } else {
        foreach ($productosMostrados as $producto) {
    ?>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <img style="width:220px;" src="<?php echo $producto->imagen ?>">
                        <h3 style="font-family:TipografiaElegante-bold;font-size: 33px;" class="card-title"><?php echo $producto->nombre ?></h3>
                        <h4 style="font-family:TipografiaElegante;font-size: 22px;" class="card-text"><b>Precio:</b> Bs.<?php echo $producto->precio ?></h4>
                        <h4 style="font-family:TipografiaElegante;font-size: 22px;" class="card-text"><b>Cantidad:</b> <?php echo $producto->cantidad ?></h4>
                        <form method="post">
                            <?php if (isset($usuarioSesion) && $usuarioSesion->tipo == 3) { ?>
                                <input name="habilitadoCompra" type="number" hidden value="1">
                                <input name="ciUsuarioCompra" type="text" hidden value="<?php echo $usuarioSesion->ci ?>">
                                <input name="idProducto" type="text" hidden value="<?php echo $producto->id ?>">
                                <input name="nombreProducto" type="text" hidden value="<?php echo $producto->nombre ?>">
                                <input name="precio" type="number" hidden value="<?php echo $producto->precio ?>">
                                <input name="stock" type="number" hidden value="<?php echo $producto->cantidad ?>">
                                <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                    Añadir al carrito <i class="bi bi-cart-plus-fill"></i>
                                </button>
                            <?php
                            } else if (isset($usuarioSesion) && $usuarioSesion->tipo != 3) {  ?>
                                <input name="inhabilitadoCompra" type="number" hidden value="1">
                                <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-secondary" title="Debe ser un usuario de tipo cliente">Añadir al carrito <i class="bi bi-cart-plus-fill"></i></button>
                            <?php } else if (!isset($usuarioSesion)) { ?>
                                <input name="inhabilitadoCompra" type="number" hidden value="1">
                                <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-secondary" title="Primero inicia sesión con tu cuenta.">Añadir al carrito <i class="bi bi-cart-plus-fill"></i></button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</div>

<!-- Carrito -->
<div style="position: sticky; bottom: 500px;" class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Carrito de pedidos <i class="bi bi-cart"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div style="overflow-y: auto;  max-height: 500px;" class="modal-body">
                <div>
                    <h5>Productos agregados: <?php echo (count($productosCarrito->compras)); ?></h5>
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
                                <input type='number' readonly class='form-control' min='1' max='" . $producto->stock . "' name='cantidad" . $numCant . "' value='$producto->cantidad'>";
                            $total += $producto->precio * $producto->cantidad;
                            $numCant++;
                        }
                        echo "
                            <hr/>
                            <label for='cantidad' class='form-label'>Total:</label>
                            Bs.<input type='number' readonly class='form-control' name='cantidad' value='$total'>
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
    modalId.addEventListener('show.bs.modal', function(event) {
        // Button that triggered the modal
        let button = event.relatedTarget;
        // Extract info from data-bs-* attributes
        let recipient = button.getAttribute('data-bs-whatever');

        // Use above variables to manipulate the DOM
    });
</script>
<?php include('plantillas/footer.php'); ?>