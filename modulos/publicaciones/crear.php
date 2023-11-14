<?php include('../../plantillas/header.php');?>
  <?php
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Productos.php');
    require('../../componentes/componentesHtml.php');
    //API Google drive uso de composer
    require_once '../../vendor/autoload.php';
    //Es necesario actualizar la cuenta de servicio de google si esta ha caducado,la misma caduca el 31   de diciembre de 2023
    
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
    
    if($_POST)
    {
      //Recuperar la ruta de la imagen
      /*
      if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        // Ruta temporal del archivo subido
        $tempFilePath = json_encode($_FILES['imagen']);
        echo $tempFilePath;
      }*/
      //Subir y cargar imagen desde GOOGLE DRIVE
      try
      {
        $urlImagen = '';
        $service = new Google_Service_Drive($client);
        // Obtener información de la imagen subida
        $file = $_FILES['imagen'];

        // Verificar si se cargó correctamente
        if ($file['error'] === UPLOAD_ERR_OK) {
          // Obtener el nombre y la ruta temporal del archivo
          $fileName = $file['name'];
          $filePath = $file['tmp_name'];
        
          // Crear un archivo en Google Drive
          $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $fileName,
            'parents' => array($folderId), // ID de la carpeta de destino en Google Drive
          ));
        
          $fileContent = file_get_contents($filePath);
        
          $file = $service->files->create($fileMetadata, array(
            'data' => $fileContent,
            'uploadType' => 'multipart',
            'fields' => 'id, webContentLink',
          ));
          $urlImagen = str_replace('&export=download','',$file->webContentLink);

          //Subir datos a la API
          // Datos del body
          if($_POST['tipo'] == 2)
          {
            $datosProducto = array(
                "titulo" => $_POST['titulo'],
                "descripcion" => $_POST['descripcion'],
                "imagen" => $urlImagen,
                "estado" => 1,
                "tipo" => intval($_POST['tipo']),
                "idProducto" => intval($_POST['productoId']),
                "descuento" => $_POST['descuento']
              );
          }
          else
          {
            $datosProducto = array(
                "titulo" => $_POST['titulo'],
                "descripcion" => $_POST['descripcion'],
                "imagen" => $urlImagen,
                "estado" => 1,
                "tipo" => intval($_POST['tipo'])
              );
          }
          
    
          // Convertir el body a formato JSON
          $jsonData = json_encode($datosProducto,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
          //print_r($jsonData);
          // URL de la API
          $url = "https://apijoyeriav2.somee.com/api/UsuarioPublicacion/RegistrarPublicacion";
    
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
          if ($response !== false) {
            alertAviso("Mensaje","Publicación creada con éxito ✅","Aceptar");
            // Procesar la respuesta de la API aquí
          } else {
            $httpCode = http_response_code();
            echo "Error en la solicitud, el producto no se pudo registrar por un error: $httpCode";
            // Manejar el error de la API aquí
          }
        } else {
          // Mostrar un mensaje de error en caso de fallo en la carga
          echo 'Error al subir el archivo.';
          
        }
      }catch(Google_Service_Exception $gs){
          $mensaje = json_decode($gs->getMessage());
          echo $mensaje->error->message;
      }catch(Exception $e)
      {
          echo $e->getMessage();
      }
    }
  ?>
<?php if(isset($_COOKIE['usuario']))
    { $usuarioSesion = json_decode($_COOKIE['usuario']); if($usuarioSesion->tipo == 1) {?>
<h4>Registrar una nueva publicación</h4>
<div class="card">
    <div class="card-header">
        Datos publicación
    </div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
            <label for="nombre" class="form-label">Título publicación:</label>
                <textarea type="text"
                class="form-control" name="titulo" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ .()]{5,500}$"
                minlength="5"
                maxlength="500" id="nombre" aria-describedby="helpNombre" placeholder="Ingrese un título para la nueva publicación" onchange="validarTituloPublicacion(this.value)"></textarea>
                <small id="helpNombre" class="form-text">El título debe tener mínimamente 5 caracteres, no puede exceder los 500 caracteres y solo puede utilizar letras/espacio.</small>
                <br/>
                <label for="descripcion" class="form-label">Descripcion (opcional):</label>
                <textarea type="text"
                class="form-control" name="descripcion" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ .\n][0-9]{5,100}$"
                minlength="5"
                maxlength="100" id="descripcion" aria-describedby="helpDescripcion" placeholder="Ingrese una descripción" onchange="validarDescripcionPublicacion(this.value)"></textarea>
                <small id="helpDescripcion" class="form-text">La descripción debe tener mínimamente 5 caracteres, no puede exceder los 500 caracteres y solo puede utilizar letras/espacio.</small>
                <br/>
                <label class="form-label">Seleccione un tipo de publicación</label>
                <select id="tipoSelect" onchange="mostrarCamposPromocion(this.value)" required class="form-select form-select-lg" name="tipo">
                    <option selected value="1">Publicación</option>
                    <option value="2">Promoción</option>
                </select>
                <div id="productoSelect">
                <label for="inputName">Seleccionar producto con promoción</label>
                <select required class="form-select form-select-lg" name="productoId">
                    <?php foreach ($productos->productos as $producto) {?>
                        <option selected value=<?php echo intval($producto->id)?>><?php echo $producto->nombre?></option>
                    <?php }?>
                </select>
                  <label for="" class="form-label">Descuento:</label>
                  <input required type="number" min="10" max="50" value="10"
                    class="form-control" name="descuento" aria-describedby="helpId" placeholder="porcentaje">
                  <small id="helpId" class="form-text text-muted">Debe insertar una cantidad entre 10 y 50</small>
                </div>
                <label for="imagen" class="form-label">Escoger una imagen para la publicación (Se recomienda una imagen horizontal de 480x250 píxeles)</i>
                <input required type="file" accept=".jpg, .png" class="form-control" name="imagen" id="imagen" placeholder="Seleccione una imagen válida de tipo .jpg o .png" aria-describedby="ImagenHelpId">
                <div id="ImagenHelpId" class="form-text">Seleccione una imagen válida de tipo .jpg o .png y que no sea muy grande (máximo de 1024x1024 píxeles).</div>
                <!-- el estado por defecto será 1-->
            </div>
            <button type="submit" class="btn btn-success">Crear publicación</button>
            <a name="" id="" class="btn btn-danger" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php } else {
      echo "<h1 style='color: red;'><b><center>Acceso denegado</center></b></h1>";
    }
    } else {
      echo "<h1 style='color: #b59410;'><b><center>Debe autenticarse primero</center></b></h1>";
    } ?>
<?php include('../../plantillas/footer.php');?>