<?php
require('componentes/componentesHtml.php');
require_once('data/obtenerDatos.php');
require_once('models/Productos.php');
require_once('models/ComprasCarrito.php');
session_start();

if (isset($_SESSION['comprasCarrito'])) {
    $productosCarrito = $_SESSION['comprasCarrito'];
}
$productos = new Productos();

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
        date_default_timezone_set('America/La_Paz');
        $fecha_actual = date("Y-m-d");
        $intervalo = new DateInterval("P3D");
        $fecha_exp = new DateTime();
        date_add($fecha_exp, $intervalo);
        $fecha_exp = $fecha_exp->format('Y-m-d');
        foreach ($productosCarrito->compras as $pedido) {
            // Datos del body
            $datosUsuario = array(
                "idUsuario" => $pedido->ciUsuario,
                "idProducto" => intval($pedido->idProducto),
                "estado" => 3,
                "cantidad" => $pedido->cantidad,
                "fecha" => $fecha_actual,
                "fechaExpiracion" => $fecha_exp
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
            } else {
                alertAviso("Mensaje", "El pedido se ha realizado con éxito", "Aceptar");
            }
        }
        $productosCarrito->quitarCompras();
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title><!-- Estilos slider -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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
</head>

<body>

    <!-- Carrito -->
    <div style="position: sticky; bottom: 800px;" class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document" style=" width:1000px">
            <div class="modal-content" style=" width:800px">
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
                                    <input type="number" class="form-control cantidad-input" min="1" max="' . $producto->stock . '" data-producto-id="' . $producto->idProducto . '" value="' . $producto->cantidad . '">
                                    <span class="text-danger cantidad-error">Redusca la Cantidad Porfis 😜</span>
                                </form>
                            </td>';


                                $totalProducto = $producto->precio * $producto->cantidad; // Calcular el total por producto
                                echo '<td class="total-producto" data-producto-id="' . $producto->idProducto . '">Bs. ' . $totalProducto . '</td>';

                                echo '<td>
                                    <form method="post">
                                        <button name="btnQuitar" type="submit" class="btn btn-danger">
                                            Quitar <i class="bi bi-trash"></i>
                                        </button>
                                        <input type="hidden" name="productoId" value="' . $producto->idProducto . '">
                                    </form>
                                    
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
                <div style="overflow-y: auto; max-height: 300px;" class="modal-footer">
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
        // Actualizar totales y validar cantidad cuando se cambia la cantidad
        $('.cantidad-input').on('input', function() {
            const cantidadInput = $(this);
            const cantidad = parseInt(cantidadInput.val());
            const productoId = cantidadInput.data('producto-id');
            const precio = parseFloat($(`#modalId .precio-producto[data-producto-id="${productoId}"]`).text().replace('Bs. ', ''));
            const stock = parseInt(cantidadInput.attr('max'));

            if (cantidad > stock) {
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


</body>

</html>