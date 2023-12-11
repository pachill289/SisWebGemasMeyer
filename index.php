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


    /* CSS para ocultar el mensaje de error por defecto */
    .cantidad-error {
        display: none;
    }

    /* CSS para mostrar el mensaje de error cuando se supere el stock */
    .cantidad-input.is-invalid+.cantidad-error {
        display: inline-block;
    }
</style>

<?php

require('componentes/componentesHtml.php');
require('componentes/functionUtilities.php');
//obtener los prodcutos en stock
require_once('data/obtenerDatos.php');
require_once('models/Productos.php');
require_once('models/Publicaciones.php');
require_once('models/ComprasCarrito.php');
session_start();
//recuperar la sesión del usuario si este ha iniciado sesión
//getVersionApiGoogleDrive();
//Agregar el modelo para almacenar las compras de un usuario
//Verificar si el usuario ha iniciado sesión
//Agregar a todos los productos con stock desde la API
if (isset($_SESSION['comprasCarrito'])) {
    $productosCarrito = $_SESSION['comprasCarrito'];
}
$productos = new Productos();
//Añadir las publicaciones desde la API
$publicaciones = new Publicaciones();
foreach (construirEndpoint('UsuarioPublicacion', 'ObtenerPublicaciones') as $publicacion) {
    $publicaciones->agregarPublicacion(new Publicacion(
        $publicacion->idPublicacion,
        $publicacion->titulo,
        $publicacion->descripcion,
        $publicacion->imagen,
        $publicacion->estado,
        $publicacion->tipo
    ));
}
//Obtener un pre
$precioMaximo = 0; $count = 1;
foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
    if($count == 1)
    {
        $precioMinimo = $producto->precio;
    }
}
foreach (construirEndpoint('Producto', 'ObtenerProductosEnStock') as $producto) {
    if($producto->precio < $precioMinimo)
    {
        $precioMinimo = $producto->precio; 
    }
    if($producto->precio > $precioMaximo)
    {
        $precioMaximo = $producto->precio; 
    }
}
//Validación compra
if ($_POST) {
    if(isset($_POST['cantidadNueva']))
        echo $_POST['cantidadNueva'];
    if (isset($_POST['btnActualizar'])) {
        $productoId = $_POST['productoId'];
        //Actualizar la cantidad
        foreach ($productosCarrito->compras as $pedido) {
            if($pedido->idProducto == $productoId)
            {
                if(isset($_POST['cantidadNueva']) && $_POST['cantidadNueva'] != 1)
                {
                    $pedido->cantidad = $_POST['cantidadNueva'];
                    echo "La cantidad si cambió";
                }
                else
                {
                    echo "La cantidad no cambió";
                }
            }
        }
        //var_dump($productosCarrito->compras);
    }
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
                $cantidadNueva = 1;
                if(isset($_POST['cantidadNueva']))
                {
                    $cantidadNueva = $_POST['cantidadNueva'];
                }
                // Agregar nuevo pedido al carrito
                $productosCarrito->agregarCompraCarrito(new CompraCarrito(
                    $_POST['ciUsuarioCompra'],
                    $_POST['idProducto'],
                    $_POST['nombreProducto'],
                    $cantidadNueva,
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
        //Realizar pedido
        //Determinar la fecha y hora de La Paz Bolivia
        date_default_timezone_set('America/La_Paz');
        $fecha_actual = date("Y-m-d\TH:i:s.u\Z");
        // Convertir la fecha actual en un objeto DateTime
        $fechaObjeto = new DateTime($fecha_actual);
        // Aumentar 3 días
        $fechaObjeto->modify('+3 days');
        // Obtener la nueva fecha en formato de cadena
        $fecha_exp = $fechaObjeto->format('Y-m-d\TH:i:s.u\Z');
        foreach ($productosCarrito->compras as $pedido) {
            // Datos del body
            $datosUsuario = array(
                "idUsuario" => $pedido->ciUsuario,
                "idProducto" => intval($pedido->idProducto),
                "estado" => 3,
                "cantidad" => $pedido->cantidad,
                "fecha" => $fecha_actual,
                "fecha_expiracion" => $fecha_exp
            );

            // Convertir el body a formato JSON
            $jsonData = json_encode($datosUsuario);

            // URL de la API
            $url = "https://apijoyeriav2.somee.com/api/UsuarioPedido/RegistrarPedido";

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
            } else {
                alertAviso("Mensaje", "El pedido se ha realizado con éxito", "Aceptar");
            }
        }
        $productosCarrito->quitarCompras();
    }
    //Borrar productos del carrito
    // Verificar si se envió el formulario para quitar un producto
    if (isset($_POST['btnQuitar'])) {
        $productoId = $_POST['productoId'];
    
        // Obtener el carrito de compras de la sesión
        $productosCarrito = isset($_SESSION['comprasCarrito']) ? $_SESSION['comprasCarrito'] : new stdClass();
    
        // Buscar y eliminar el producto del carrito por su ID
        foreach ($productosCarrito->compras as $indice => $producto) {
            if ($producto->idProducto == $productoId) {
                unset($productosCarrito->compras[$indice]);
                break; // Detener el bucle una vez que se elimine el producto
            }
        }
    
        // Actualizar la sesión del carrito
        $_SESSION['comprasCarrito'] = $productosCarrito;
    
        // Redirigir de nuevo al carrito o a donde desees
        alertAviso("Mensaje", "Producto eliminado", "Aceptar");
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
<?php include('plantillas/header.php'); ?>
<div class="mb-3">
    <label class="visually-hidden" for="inputName">Hidden input label</label>
</div>
<nav style="width:100%;z-index:9999;" class="navbar navbar-expand navbar-light bg-light sticky-top">
    <div class="container">
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="#publicaciones" aria-current="page">
                        <h3>Publicaciones</h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#joyas">
                        <h3>Catálogo de joyas</h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#mision">
                        <h3>Misión y visión</h3>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<br />
<!-- publicaciones -->
<div id="publicaciones" style="margin-bottom: 20px;" class="card">
    <div class="card-body">
        <h3 class="card-title">Publicaciones <i class="bi bi-newspaper"></i></h3>
    </div>
</div>
<div style="overflow-y: auto;  max-height: 500px;" class="bg-light rounded-3">
    <?php foreach($publicaciones->publicaciones as $publicacion) { if($publicacion->estado == 1) {?>
    <div style="display:inline;">
        <img style="width: 1115px;max-width:max-content; height:300px;padding:10px;" src=<?php echo $publicacion->imagen ?> alt="Imagen no disponible">
        <h3 style="padding:10px;"><?php echo $publicacion->titulo ?></h3>
        <div class="mb-3" style="padding: 10px;">
            <textarea readonly class="form-control" rows="3"><?php echo $publicacion->descripcion ?></textarea>
        </div>
    </div>
    <?php }
    }?>
</div>
<!-- Catálogo -->
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
                                <label>Mínimo: </label>
                                <input name="precioMin" readonly type="number" onchange="valorInput(this.value,<?php echo($precioMaximo) ?>,<?php echo($precioMaximo) ?>)" value=<?php echo (isset($_POST['precioMin']) ? $_POST['precioMin'] : $precioMinimo) ?> id="inputPrecioMin" aria-describedby="helpId" style="text-align: center;font-size:medium;">
                            </div>
                            <div class="max-value numberVal" style="text-align: right; display: inline-flex;">
                                <label for="">Máximo: </label>
                                <input name="precioMax" readonly type="number" onchange="valorInput2(this.value,<?php echo($precioMaximo) ?>,<?php echo($precioMaximo) ?>)" value=<?php echo (isset($_POST['precioMax']) ? $_POST['precioMax'] : $precioMaximo) ?> id="inputPrecioMax" aria-describedby="helpId" style="text-align: center;font-size:10px;font-size:medium;">
                            </div>
                            <br>
                            &nbsp;
                            <div class="range-slider">
                                <div class="progress"></div>
                                <input name="sliderPrecios" onchange="valorRange(this.value)" type="range" max=<?php echo($precioMaximo) ?> min=<?php echo($precioMinimo) ?> step="10" value=<?php echo (isset($_POST['precioMin']) ? $_POST['precioMin'] : $precioMinimo) ?> class="range-min" id="customRange1" />
                                <input name="sliderPrecios2" onchange="valorRange2(this.value)" type="range" max=<?php echo($precioMaximo) ?> min=<?php echo($precioMinimo) ?> value=<?php echo (isset($_POST['precioMax']) ? $_POST['precioMax'] : $precioMaximo) ?> class="range-max" id="customRange2" />
                            </div>
                        </div>
                        <?php espacio_br(1) ?>
                        <button type="submit" class="btn btn-primary">Filtrar 🔍</button>
                        <button type="button" onclick="<?php sendJsArgs("limpiarFiltros",$precioMinimo,$precioMaximo)?>" class="btn btn-danger">Limpiar Filtros 🧹</button>

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
                            function limpiarFiltros(precioMin,precioMax) {
                                // Restablecer los campos de búsqueda y filtros
                                document.getElementById('nombreProducto').value = '';
                                document.getElementById('inputPrecioMin').value = precioMin; // Valor mínimo
                                document.getElementById('inputPrecioMax').value = precioMax; // Valor máximo
                                document.getElementById('customRange1').value = precioMin; // Valor mínimo del rango
                                document.getElementById('customRange2').value = precioMax; // Valor máximo del rango
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
                             Anillos <svg fill="#fff" height="20px" width="20px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	 viewBox="0 0 512 512"  xml:space="preserve">
<g>
	<path class="st0" d="M379.429,213.995c-14.739-14.776-32.171-26.934-51.474-35.669l81.691-105.941L331.762,0H180.238
		l-77.874,72.348l81.681,105.978c-19.341,8.735-36.754,20.892-51.511,35.669c-31.536,31.536-51.128,75.247-51.128,123.42
		c0,48.181,19.593,91.874,51.128,123.429C164.115,492.426,207.818,512,256,512c48.182,0,91.885-19.574,123.429-51.156
		c31.573-31.555,51.166-75.248,51.166-123.429C430.594,289.242,411.002,245.53,379.429,213.995z M300.722,168.628l-15.487,20.079
		l7.958-21.884l15.889-43.515l61.125-44.806L300.722,168.628z M291.35,126.206l-13.812,37.951l-9.68,26.588l-3.423,9.342H246.47
		l-3.376-9.258l-9.688-26.551l-13.86-38.073H291.35z M218.376,110.523l9.184-36.483h55.795l9.184,36.483H218.376z M332.706,38.371
		l28.954,26.906l-54.336,39.868l-9.072-36.249L332.706,38.371z M191.031,27.458h129.91l0.177,0.206l-34.64,30.675h-60.958
		l-34.64-30.722L191.031,27.458z M179.294,38.371l33.546,29.703v0.037l-9.296,36.988l-53.709-39.354L179.294,38.371z
		 M143.194,80.353l58.591,42.954l15.917,43.749l6.874,18.864l-13.346-17.292L143.194,80.353z M360.042,441.456
		c-26.672,26.635-63.36,43.076-104.042,43.076c-40.681,0-77.37-16.441-104.042-43.076c-26.654-26.672-43.076-63.36-43.076-104.042
		c0-40.691,16.423-77.37,43.076-104.043c14-13.981,30.759-25.157,49.388-32.62l14.608,18.947h80.092l14.616-18.947
		c18.621,7.463,35.38,18.639,49.38,32.62c26.654,26.673,43.085,63.352,43.085,104.043
		C403.127,378.096,386.696,414.784,360.042,441.456z"/>
	<path class="st0" d="M256,233.896c-28.552,0-54.504,11.606-73.198,30.319c-18.714,18.705-30.32,44.638-30.32,73.2
		c0,28.533,11.606,54.494,30.32,73.19c18.694,18.714,44.647,30.329,73.198,30.329c28.552,0,54.504-11.615,73.199-30.329
		c18.704-18.695,30.32-44.656,30.32-73.19c0-28.561-11.616-54.495-30.32-73.2C310.504,245.502,284.552,233.896,256,233.896z
		 M312.542,393.967c-14.495,14.486-34.415,23.417-56.543,23.417c-22.117,0-42.037-8.931-56.543-23.417
		c-14.505-14.524-23.427-34.434-23.427-56.552c0-22.136,8.922-42.038,23.427-56.553c14.506-14.496,34.426-23.417,56.543-23.427
		c22.128,0.01,42.048,8.931,56.543,23.427c14.496,14.514,23.427,34.416,23.427,56.553
		C335.97,359.533,327.038,379.452,312.542,393.967z"/>
</g>
</svg>
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Cadena">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                             Cadenas <svg fill="#fff" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	 viewBox="0 0 208.472 208.472" xml:space="preserve">
<g>
	<path d="M192.945,129.533c-2.569-8.313-8.416-15.206-14.115-21.575c-1.121-1.253-3.406,0.447-2.367,1.826
		c0.083,0.11,0.166,0.221,0.25,0.331c-0.4,0.558-0.549,1.313-0.181,2.054c4.707,9.482,0.757,19.179,4.104,28.708
		c1.568,4.466,5.957,7.613,10.346,4.404C195.669,141.852,194.393,134.223,192.945,129.533z"/>
	<path d="M201.15,113.316c-16.72-30.551-54.758-40.541-84.501-23.611c-0.39-1.61-1.358-3.209-2.255-4.337
		c-1.725-2.168-4.146-3.664-6.841-4.249c-0.575-6.869-3.623-14.095-7.567-17.916c-5.053-4.896-13.786-7.031-20.937-5.391
		c3.807-7.619-2.529-18.169-10.32-22.41c-3.786-2.061-9.012-3.073-14.015-2.72C56.811,11.091,28.821-0.726,11.14,7.384
		c-20.905,9.589-9.671,38.07,11.313,35.291c6.54,7.685,16.751,12.668,26.595,10.965c-0.535,0.995-0.97,1.937-1.257,2.682
		c-2.83,7.342-1.717,15.734,3.229,21.884c9.68,12.037,32.057,11.221,36.833-4.542c1.463,0.811,2.812,1.93,3.947,3.575
		c1.109,1.608,2.049,3.52,2.745,5.534c-5.796-1.425-11.804,6.187-12.772,11.511c-1.602,8.807,4.908,16.874,12.98,19.614
		c-21.64,44.03,26.253,110.982,81.064,83.225C205.99,181.843,217.217,142.671,201.15,113.316z M107.913,95.032
		c0.346,0.472,0.618,0.989,0.898,1.499c-3.135,2.545-5.809,5.319-8.14,8.239c-1.024-0.491-1.955-1.067-2.745-2.191
		c-1.056-1.502-1.328-3.183-1.254-4.937c4.142-0.484,6.971-2.382,8.698-5.146C106.313,93.239,107.197,94.054,107.913,95.032z
		 M38.228,26.581c3.324,2.045,5.68,5.263,6.421,9.32c-1.956,1.36-3.53,3.148-4.445,5.458c-2.604-3.562-3.545-8.389-1.871-13.595
		C38.473,27.327,38.405,26.926,38.228,26.581z M50.931,50.66c-6.618,2.012-13.167,0.07-18.976-3.808
		c-2.438-1.628-4.839-3.546-6.786-5.767c0.04-0.75-0.352-1.508-1.108-1.636c-2.101-3.807-4.637-12.063-0.579-14.679
		c3.535-0.807,7.821-0.685,9.848-0.214c1.027,0.239,1.993,0.594,2.926,1c-0.473,0.061-0.91,0.303-1.159,0.844
		c-3.322,7.245-0.89,15.017,4.22,19.669c-0.004,0.188-0.041,0.353-0.039,0.546c0.013,1.023,1.339,1.635,2.099,1.064
		c2.844,1.878,6.238,2.899,9.854,2.557C51.125,50.372,51.034,50.52,50.931,50.66z M56.081,46.64
		c-0.505,0.083-0.999,0.271-1.482,0.524c-4.401,0.523-8.367-0.58-11.375-2.797c3.989-7.003,10.537-10.7,19.507-7.81
		c8.763,2.824,19.902,14.876,11.702,23.027c-0.861,0.487-1.707,1.002-2.442,1.654c-1.355-7.321-6.752-13.525-14.228-15.144
		C57.032,45.937,56.462,46.208,56.081,46.64z M67.645,69.844c-1.288-0.814-2.477-1.664-3.484-2.861
		c-0.212-0.279-0.41-0.575-0.626-0.85c-0.238-0.358-0.48-0.706-0.686-1.132c-1.875-3.881-1.261-8.065,0.661-11.791
		c0.042-0.08,0.004-0.161,0.022-0.243c3.098,2.491,5.342,5.941,6.21,10.06c0.041,0.196,0.131,0.35,0.242,0.481
		C68.785,65.232,67.97,67.34,67.645,69.844z M94.635,74.639c-5.402-7.613-14.503-7.295-22.696-5.362
		c5.199-8.041,13.958-11.407,22.962-5.046c4.107,2.901,7.014,7.1,8.405,11.94c1.628,5.663,1.236,15.958-5.637,18.467
		C100.708,88.337,98.626,80.263,94.635,74.639z M170.988,193.899c-28.119,11.665-60.283-3.543-72.818-30.518
		c-12.513-26.928-0.565-56.598,23.654-71.94c26.643-10.582,57.998-4.778,73.783,21.713
		C212.241,141.068,202.077,181.003,170.988,193.899z"/>
</g>
</svg> 
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Collar">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                Collares <svg fill="#fff" height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	 viewBox="0 0 511.994 511.994" xml:space="preserve">
<g>
	<g>
		<path d="M508.25,48.545c-5.001-5.001-13.099-5.001-18.099,0L255.996,282.7L21.85,48.545c-5.001-5.001-13.099-5.001-18.099,0
			c-5,5.001-5,13.099,0,18.099l170.633,170.633c-18.884,2.398-33.587,18.389-33.587,37.914c0,21.171,17.229,38.4,38.4,38.4
			c19.524,0,35.516-14.703,37.914-33.587l22.383,22.383c-15.002,22.605-47.497,74.53-47.497,100.813c0,35.285,28.698,64,64,64
			c35.302,0,64-28.715,64-64c0-26.283-32.495-78.208-47.497-100.804l22.383-22.383c2.398,18.884,18.389,33.587,37.914,33.587
			c21.171,0,38.4-17.229,38.4-38.4c0-19.524-14.703-35.516-33.587-37.914L508.25,66.653
			C513.242,61.652,513.242,53.546,508.25,48.545z M179.196,287.999c-7.049,0-12.8-5.734-12.8-12.8s5.751-12.8,12.8-12.8
			s12.8,5.734,12.8,12.8S186.244,287.999,179.196,287.999z M294.396,403.199c0,21.171-17.229,38.4-38.4,38.4s-38.4-17.229-38.4-38.4
			c0-13.978,19.977-50.466,38.4-79.266C274.419,352.724,294.396,389.196,294.396,403.199z M345.596,275.199
			c0,7.066-5.751,12.8-12.8,12.8c-7.049,0-12.8-5.734-12.8-12.8s5.751-12.8,12.8-12.8
			C339.844,262.399,345.596,268.133,345.596,275.199z"/>
	</g>
</g>
</svg>
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Juego">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                Juegos <svg fill="#fff" height="30px" width="30px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	 viewBox="0 0 512 512" xml:space="preserve">
<g>
	<g>
		<g>
			<path d="M170.667,290.133c0-30.037-22.298-54.895-51.2-59.051v-44.928c9.916-3.533,17.067-12.911,17.067-24.021V128
				c0-11.11-7.151-20.489-17.067-24.021V59.733c0-4.719-3.823-8.533-8.533-8.533c-9.412,0-17.067-7.654-17.067-17.067
				c0-9.412,7.654-17.067,17.067-17.067c9.412,0,17.067,7.654,17.067,17.067c0,12.006,1.843,51.2,25.6,51.2
				c4.71,0,8.533-3.814,8.533-8.533s-3.823-8.533-8.533-8.533c-3.439,0-8.533-14.336-8.533-34.133
				C145.067,15.309,129.758,0,110.933,0C92.109,0,76.8,15.309,76.8,34.133c0,15.872,10.897,29.261,25.6,33.05v36.796
				C92.484,107.511,85.333,116.89,85.333,128v34.133c0,11.11,7.151,20.489,17.067,24.021v44.928
				c-28.902,4.156-51.2,29.013-51.2,59.051s22.298,54.895,51.2,59.051v35.686c-19.447,3.951-34.133,21.197-34.133,41.796
				c0,20.599,14.686,37.845,34.133,41.796v35.004c0,4.719,3.823,8.533,8.533,8.533c4.71,0,8.533-3.814,8.533-8.533v-35.004
				c19.447-3.951,34.133-21.197,34.133-41.796c0-20.599-14.686-37.845-34.133-41.796v-35.686
				C148.369,345.028,170.667,320.171,170.667,290.133z M131.704,253.09c12.996,7.33,21.896,21.094,21.896,37.043
				s-8.9,29.713-21.896,37.043c3.268-10.65,4.83-23.868,4.83-37.043S134.972,263.74,131.704,253.09z M90.163,327.177
				c-12.996-7.33-21.897-21.094-21.897-37.043s8.9-29.713,21.897-37.043c-3.268,10.65-4.83,23.868-4.83,37.043
				S86.895,316.527,90.163,327.177z"/>
			<path d="M409.6,384.87v-35.686c28.902-4.156,51.2-29.013,51.2-59.051s-22.298-54.895-51.2-59.051v-44.928
				c9.916-3.533,17.067-12.911,17.067-24.021V128c0-11.11-7.151-20.489-17.067-24.021V59.733c0-4.719-3.823-8.533-8.533-8.533
				C391.654,51.2,384,43.546,384,34.133c0-9.412,7.654-17.067,17.067-17.067c9.412,0,17.067,7.654,17.067,17.067
				c0,12.006,1.843,51.2,25.6,51.2c4.71,0,8.533-3.814,8.533-8.533s-3.823-8.533-8.533-8.533c-3.439,0-8.533-14.336-8.533-34.133
				C435.2,15.309,419.891,0,401.067,0c-18.825,0-34.133,15.309-34.133,34.133c0,15.872,10.897,29.261,25.6,33.05v36.796
				c-9.916,3.533-17.067,12.911-17.067,24.021v34.133c0,11.11,7.151,20.489,17.067,24.021v44.928
				c-28.902,4.156-51.2,29.013-51.2,59.051s22.298,54.895,51.2,59.051v35.686c-19.447,3.951-34.133,21.197-34.133,41.796
				c0,20.599,14.686,37.845,34.133,41.796v35.004c0,4.719,3.823,8.533,8.533,8.533c4.71,0,8.533-3.814,8.533-8.533v-35.004
				c19.448-3.951,34.133-21.197,34.133-41.796C443.733,406.067,429.047,388.821,409.6,384.87z M421.837,253.09
				c12.996,7.33,21.897,21.094,21.897,37.043s-8.9,29.713-21.897,37.043c3.268-10.65,4.83-23.868,4.83-37.043
				S425.105,263.74,421.837,253.09z M380.297,327.177c-12.996-7.33-21.897-21.094-21.897-37.043s8.9-29.713,21.897-37.043
				c-3.268,10.65-4.83,23.868-4.83,37.043S377.028,316.527,380.297,327.177z"/>
		</g>
	</g>
</g>
</svg>
                            </button>
                        </form>
                    </li>
                    <?php espacio_br(1) ?>
                    <li>
                        <form method="post">
                            <input name="cat" type="text" hidden value="Dije">
                            <button style="font-family:TipografiaElegante;font-size: 22px;" type="submit" class="btn btn-primary">
                                Dijes <svg fill="#fff" height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	 viewBox="0 0 512 512" xml:space="preserve">
<g>
	<g>
		<path d="M149.299,181.001c-2.202-3.311-5.163-5.931-8.499-7.868V74.445c14.865-5.299,25.6-19.379,25.6-36.045
			C166.4,17.229,149.171,0,128,0S89.6,17.229,89.6,38.4c0,16.666,10.735,30.737,25.6,36.045v98.697
			c-3.337,1.937-6.298,4.557-8.499,7.868C97.084,195.413,12.8,323.951,12.8,396.8C12.8,460.322,64.478,512,128,512
			s115.2-51.678,115.2-115.2C243.2,323.951,158.916,195.413,149.299,181.001z M128,25.6c7.066,0,12.8,5.726,12.8,12.8
			c0,7.066-5.734,12.8-12.8,12.8s-12.8-5.734-12.8-12.8C115.2,31.326,120.934,25.6,128,25.6z M128,198.076c0,0,64,96,64,144
			c0,35.354-28.655,64-64,64s-64-28.655-64-64C64,294.076,128,198.076,128,198.076z M128,486.4c-49.485,0-89.6-40.115-89.6-89.6
			c0-8.713,1.553-18.586,4.181-29.116c11.093,36.881,44.971,63.991,85.419,63.991s74.325-27.11,85.419-63.991
			c2.62,10.53,4.181,20.403,4.181,29.116C217.6,446.285,177.485,486.4,128,486.4z"/>
	</g>
</g>
<g>
	<g>
		<path d="M405.299,181.001c-2.21-3.311-5.163-5.931-8.499-7.868V74.445c14.865-5.299,25.6-19.379,25.6-36.045
			C422.4,17.229,405.171,0,384,0c-21.171,0-38.4,17.229-38.4,38.4c0,16.666,10.735,30.737,25.6,36.045v98.697
			c-3.336,1.937-6.298,4.557-8.499,7.868C353.084,195.413,268.8,323.951,268.8,396.8c0,63.522,51.678,115.2,115.2,115.2
			s115.2-51.678,115.2-115.2C499.2,323.951,414.916,195.413,405.299,181.001z M384,25.6c7.066,0,12.8,5.726,12.8,12.8
			c0,7.066-5.734,12.8-12.8,12.8c-7.066,0-12.8-5.734-12.8-12.8C371.2,31.326,376.934,25.6,384,25.6z M384,198.076c0,0,64,96,64,144
			c0,35.345-28.655,64-64,64c-35.345,0-64-28.655-64-64C320,294.076,384,198.076,384,198.076z M384,486.4
			c-49.485,0-89.6-40.115-89.6-89.6c0-8.713,1.553-18.586,4.181-29.116c11.093,36.881,44.971,63.991,85.419,63.991
			c40.448,0,74.325-27.119,85.419-63.991c2.62,10.53,4.181,20.403,4.181,29.116C473.6,446.285,433.485,486.4,384,486.4z"/>
	</g>
</g>
</svg>
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
    if ($_POST && isset($_POST['nombreProducto']) && $_POST['nombreProducto'] != "") {
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
    } else {
        foreach ($productosMostrados as $producto) {
    ?>
            <!-- Ahora el catálogo es responsive con las clases: col-lg-4 col-md-6 col-sm-12 e img-fluid para las imágenes -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <img class="img-fluid" src="<?php echo $producto->imagen ?>" alt="<?php echo $producto->nombre ?>">
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
<div id="mision" style="margin-left:270px;" class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h4>Misión</h4>
        <p class="col-md-8 fs-4">Brindar a los clientes la alternativa de adquirir joyas al "precio justo", piedras preciosas naturales talladas en Bolivia, engarzadas en metales nobles de origen boliviano, labrados por artesanos orfebres locales, brindando empleos y generando impacto social, y a su vez logrando que llege a sus manos una pieza de joyería de alta calidad.</p>
        <p class="col-md-8 fs-4"><b>Conoce mas de nosotros</b></p>
        <img class="img-fluid" width="450" height="350" src="resources/img_demostracion_2.jpg">
        <br><br>
        <a class="btn btn-primary btn-lg" href="https://www.facebook.com/profile.php?id=100089640294548" target="_blank">Visitar Museo Gemológico <i class="bi bi-facebook"></i></a>
    </div>
</div>
<!-- Carrito script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Carrito -->
<div style="position: sticky;" class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:1000px; margin-top: 100px;">
        <div class="modal-content" style="width:800px;">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Carrito de pedidos <i class="bi bi-cart"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div style="overflow-y: auto; max-height: 500px;" class="modal-body">
                <div>
                    <h5>Productos agregados: <?php echo (count($productosCarrito->compras)); ?></h5>
                    <?php
                    // Obtener el carrito de compras de la sesión
                    $total = 0;
                    $numCant = 1;
                    if (isset($_SESSION['comprasCarrito'])) {
                        // Mostrar los productos agregados al carrito en una tabla
                        echo '<table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nombre producto</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Cantidad</th>
                                        <th>Total por producto</th> <!-- Nueva columna para el total por producto -->
                                        <th>Quitar</th> <!-- Agregar esta columna para el botón "Quitar" -->
                                    </tr>
                                </thead>
                                <tbody>';

                        foreach ($productosCarrito->compras as $producto) {
                            echo '<tr>';
                            echo '<td>' . $producto->nombreProducto . '</td>';
                            echo '<td class="precio-producto" data-producto-id="' . $producto->idProducto . '">Bs. ' . $producto->precio . '</td>';
                            echo '<td>' . $producto->stock . '</td>';
                            echo '<td>
                                <form name="formCantidad" method="post">
                                    <input name="cantidadNueva" required onchange="actualizarCantidad(this)" type="number" class="form-control cantidad-input" min="1" max="' . $producto->stock . '" data-producto-id="' . $producto->idProducto . '" value="' .$producto->cantidad. '">
                                    <br/>
                                    <span class="text-danger cantidad-error">La cantidad no es válida</span>
                            </td>';


                            $totalProducto = $producto->precio * $producto->cantidad; // Calcular el total por producto
                            echo '<td class="total-producto" data-producto-id="' . $producto->idProducto . '">Bs. ' . $totalProducto . '</td>';

                            echo '<td>
                                        <button name="btnQuitar" type="submit" class="btn btn-danger">
                                            Quitar <i class="bi bi-trash"></i>
                                        </button>
                                        <input type="hidden" name="productoId" value="' . $producto->idProducto . '">
                                </td>';
                            echo '</tr>';

                            $total += $totalProducto; // Utilizar el total por producto en lugar de calcularlo nuevamente
                            $numCant++;
                        }

                        echo "</tbody></table>";

                        echo '<hr/>
                            <label for="cantidad" class="form-label">Total:</label>
                            Bs. <input type="number" readonly class="form-control" name="cantidad" value="' . $total . '">';
                    }
                    ?>
                </div>
            </div>
            <div style="overflow-y: auto; max-height: 300px;" class="modal-footer justify-content-start">
                    <button name="btnCompra" type="submit" class="btn btn-primary">
                        Realizar pedido <i class="bi bi-cart-check-fill"></i>
                    </button>
                    <button name="btnActualizar" type="submit" class="btn btn-success">
                        Guardar cambios <i class="bi bi-cart-check-fill"></i>
                    </button>
                </form>
                <a name="" id="" class="btn btn-danger" href="borrar_carrito.php" role="button">Vaciar carrito <i class="bi bi-trash"></i></a>
                <form method="post" for="formCantidad">
                    
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function actualizarCantidad(input) {
        document.getElementById('formCantidad').submit();
    }
    // Actualizar totales y validar cantidad cuando se cambia la cantidad
    $('.cantidad-input').on('input', function() {
        const cantidadInput = $(this);
        const cantidad = parseInt(cantidadInput.val());
        const productoId = cantidadInput.data('producto-id');
        const precio = parseFloat($(`#modalId .precio-producto[data-producto-id="${productoId}"]`).text().replace('Bs. ', ''));
        const stock = parseInt(cantidadInput.attr('max'));

        if (cantidad > stock || cantidad <= 0) {
            // Mostrar un mensaje de error si la cantidad supera el stock
            cantidadInput.addClass('is-invalid').text('Cantidad insuficiente');
        } else {
            // Quitar el mensaje de error si es válido
            cantidadInput.removeClass('is-invalid');

            const nuevoTotal = cantidad * precio;

            // Actualizar el total por producto toFixed(2)
            $(`#modalId .total-producto[data-producto-id="${productoId}"]`).text('Bs. ' + nuevoTotal);

            // Actualizar el total general
            let totalGeneral = 0;
            $('.total-producto').each(function() {
                totalGeneral += parseFloat($(this).text().replace('Bs. ', ''));
            });
            $('input[name="cantidad"]').val(totalGeneral.toFixed(2));
        }
    });
</script>
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
<!-- Misión, links y footer-->

<?php include('plantillas/footer.php'); ?>