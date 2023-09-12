<?php include('../../plantillas/header.php');?>
<?php
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Productos.php');
    //API Google drive uso de composer
    require_once '../../vendor/autoload.php';
    //Es necesario actualizar la cuenta de servicio de google si esta ha caducado,la misma caduca el 31 de diciembre de 2023

    putenv('GOOGLE_APPLICATION_CREDENTIALS=../../data/webgemasmeyer-2670159b89b9.json');
    //Definir el servicio de google
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    //$client->setScopes(['https://www.googleapis.com/auth/drive.file']);
    $client->addScope(Google_Service_Drive::DRIVE);
    //API key (opcional si se usa AuthO): AIzaSyBfPk0hwW5WmPEtkOTdJlIN7XEb283BgIM
    
    //Uso de google drive
    //usa este id para determinar la carpeta de google drive
    $folderId = '1ibwNXkd6YS-YIj7n45Jxd3wvl8AFjhb1';
    //Listar imagenes
    try{
        
        $service = new Google_Service_Drive($client);

        $resultado = $service->files->listFiles();
        /*Mostrar las imagenes
        foreach ($resultado->getFiles() as $elemento) {
            // Obtener la URL pública de la imagen
            $imageUrl = 'https://drive.google.com/uc?id=' . $elemento->getId();
            
            // Mostrar la imagen en una etiqueta img
            echo '<img width=150 height=150 src="' . $imageUrl . '>" <br/>';
            echo "<p>$elemento->name</p>";
        }*/

    }catch(Google_Service_Exception $gs){
        $mensaje = json_decode($gs->getMessage());
        echo $mensaje->error->message;
    }catch(Exception $e)
    {
        echo $e->getMessage();
    }
    /*foreach ($imageFiles as $imageId) {
        $imageUrl = "mostrar_imagen.php?id=" . $imageId;
        echo '<img src="' . $imageUrl . '" alt="Imagen">';
    }   */ 
    $subCategoria = 'ObtenerProductos';
    if (isset($_POST['botonListar'])) {
        $subCategoria = 'ObtenerProductos';
    } 
    else if (isset($_POST['botonListar2'])) {
        $subCategoria = 'ObtenerProductosEnStock';
    }
    //Agregar a todos los productos desde la API
    $productos = new Productos();

    if (isset($_POST['botonListar3']) && isset($_POST['nombreBusqueda'])) {
        foreach (construirEndpoint('Producto', $subCategoria) as $producto) {
            if (stripos($producto->nombre, $_POST['nombreBusqueda']) !== false) {
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
    }
    else
    {
        //agregar todos los productos al objeto Productos (arreglo de productos)
        foreach (construirEndpoint('Producto', $subCategoria) as $producto) {
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
    
    //opciones para listar

?>

<br/>
    <h4>Lista de todos los productos</h4>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                <nav class="navbar navbar-expand navbar-light bg-light">
                    <ul class="nav navbar-nav">
                    <li class="nav-item">
                    <a name="" id="" class="btn btn-primary" style="margin: 5px;" href="crear.php"    role="button">Registrar nuevo producto <i class="bi bi-gem"></i></a>
                    </li>
                    <!-- Opciones para listar -->
                    
                    <form method="post" action="index.php">
                            <button class="btn btn-primary" style="margin: 5px;" type="submit"  name="botonListar" value="1">
                            Listar productos
                            </button>
                            <button class="btn btn-primary" style="margin: 5px;" type="submit"  name="botonListar2" value="2">
                            Listar productos en stock
                            </button>
                            <div class="mb-3" style="display: flex; height:45px; margin: 5px;">
                                <label style="width: 230px;" for="" class="form-label">
                                <b>Buscar producto:</b></label>
                                <input type="search" autocomplete="off" size="50" title="(también puede filtrar productos por  palabras clave escribiendo: collares,anillos, etc..)"
                                class="form-control" name="nombreBusqueda" placeholder="Ingrese el nombre del producto">
                                <button style="width: 300px; margin-left: 15px;" class="btn btn-primary" type="submit" name="botonListar3">
                                    Buscar producto <i class="bi bi-search"></i>
                                </button>
                            </div>
                    </form>
                    
                    </ul>
                </nav>
                    <thead>
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Imagen</th>
                            <th scope="col">Estado</th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                   
                        <!-- Obtener a todos los productos mediante el objeto $productos -->
                        <?php foreach ($productos->productos as $producto) { ?>
                            <tr class="">
                                <td scope="row"><?php echo $producto->id?></td>
                                <td><?php echo $producto->nombre?></td>
                                <td><?php echo $producto->precio?> Bs.</td>
                                <td class="text-center"><?php echo $producto->cantidad?></td>
                                <td><?php echo '<img style="border-radius: 20%;" width=200 height=200 src="'.$producto->imagen.'">'?></td>
                                <td class="text-center">
                                <?php echo ($producto->estado == 0 ? "Inactivo" : 
                                    ($producto->estado == 1 ? "Activo" : "Eliminado"))?>
                                </td>
                                <td class="text-center">
                                <a class="btn btn-success" href="editar.php?txtId=<?php echo $producto->id;?>&txtNombre=<?php echo $producto->nombre;?>&txtCategoria=<?php echo $producto->categoria;?>&txtPrecio=<?php echo $producto->precio;?>&txtCantidad=<?php echo $producto->cantidad;?>&txtImagen=<?php echo $producto->imagen;?>" role="button">Editar <i class="bi bi-pencil-square"></i></a> 
                                </td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include('../../plantillas/footer.php');?>