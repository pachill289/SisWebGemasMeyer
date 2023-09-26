<?php include('../../plantillas/header.php');?>
  <?php
    //API Google drive uso de composer
    require_once '../../vendor/autoload.php';
    //Es necesario actualizar la cuenta de servicio de google si esta ha caducado,la misma caduca el 31   de diciembre de 2023
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
          $datosProducto = array(
            "nombre" => $_POST['nombre'],
            "precio" => intval($_POST['precio']),
            "cantidad" => intval($_POST['cantidad']),
            "imagen" => $urlImagen,
          );
    
          // Convertir el body a formato JSON
          $jsonData = json_encode($datosProducto,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
          print_r($jsonData);
          // URL de la API
          $url = "http://apijoyeriav2.somee.com/api/Producto/RegistrarProducto";
    
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
            alertAviso("Mensaje","Producto registrado con éxito ✅","Aceptar");
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
    <h4>Registrar nuevo producto</h4>
    <div class="card">
        <div class="card-header">
            Datos producto
        </div>
        <div class="card-body">
           <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                <label for="nombre" class="form-label">Nombre producto:</label>
                  <textarea type="text"
                    class="form-control" name="nombre" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ .()]{5,500}$"
                    minlength="5"
                    maxlength="500" id="nombre" aria-describedby="helpNombre" placeholder="Ingrese un nombre para el nuevo producto" onchange="validarNombreProducto(this.value)"></textarea>
                  <small id="helpNombre" class="form-text">El nombre debe tener mínimamente 5 caracteres, no puede exceder los 500 caracteres y solo puede utilizar letras/espacio.</small>
                  <br/>
                  <label for="descripcion" class="form-label">Descripcion (opcional):</label>
                  <textarea type="text"
                    class="form-control" name="descripcion" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ .\n][0-9]{5,100}$"
                    minlength="5"
                    maxlength="100" id="descripcion" aria-describedby="helpDescripcion" placeholder="Ingrese una descripción para el nuevo producto" onchange="validarDescripcion(this.value)"></textarea>
                  <small id="helpDescripcion" class="form-text">La descripción debe tener mínimamente 5 caracteres, no puede exceder los 100 caracteres y solo puede utilizar letras/espacio.</small>
                  <br/>
                  <label for="precio" class="form-label">Precio:</label>
                  <input type="number"
                    class="form-control" name="precio" required min=1 value=500 id="precio" aria-describedby="helpPrecio" placeholder="Ingrese un precio" onchange="validarPrecio(this.value)">
                  <small id="helpPrecio" class="form-text">El precio debe ser mayor a 0.</small>
                  <br/>
                  <label for="cantidad" class="form-label">Cantidad:</label>
                  <input type="number"
                    class="form-control" name="cantidad" required min=0 value=1 id="cantidad" aria-describedby="helpCantidad" placeholder="Ingrese una cantidad inicial para el producto." onchange="validarCantidad(this.value)">
                  <small id="helpCantidad" class="form-text">La cantidad debe ser mayor o igual a 0.</small>
                  <br/>
                  <label for="imagen" class="form-label">Escoger una imagen para subirla a google drive</label> <i class="bi bi-google"></i>
                  <input required type="file" accept=".jpg, .png" class="form-control" name="imagen" id="imagen" placeholder="Seleccione una imagen válida de tipo .jpg o .png" aria-describedby="ImagenHelpId">
                  <div id="ImagenHelpId" class="form-text">Seleccione una imagen válida de tipo .jpg o .png y que no sea muy grande (máximo de 1024x1024 píxeles).</div>
                  <!-- el estado se calcula automáticamente con un trigger si la cantidad es mayor a 0 el estado es activo de lo contrario es inactivo -->
                  <small id="helpEstado" class="form-text">Si la cantidad es mayor a 0 el estado del producto es de tipo activo de lo contrario es inactivo.</small>
                </div>
                <button type="submit" class="btn btn-success">Registrar producto</button>
                <a name="" id="" class="btn btn-danger" href="index.php" role="button">Cancelar</a>
           </form>
        </div>
    </div>
<?php include('../../plantillas/footer.php');?>