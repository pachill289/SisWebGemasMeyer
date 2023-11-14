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
      try
      {
        $urlImagen = '';
        $service = new Google_Service_Drive($client);
        // Obtener información de la imagen subida
        $file = $_FILES['imagen'];

        // Verificar si se cargó correctamente
        if (isset($_FILES['imagen']) && $file['error'] === UPLOAD_ERR_OK) {
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
          if(isset($_FILES['imagen']))
          $urlImagen = str_replace('&export=download','',$file->webContentLink);
        }
        else
        {
            $urlImagen = $_GET['txtImagen'];
        }
          
          //Subir datos a la API
          // Datos del body
          $publicacionId = $_GET['txtId'];
          if($_POST['txtTipo'] == 2)
          {
            $datosPublicacion = array(
                "idPublicacion" => intval($publicacionId),
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
            $datosPublicacion = array(
                "idPublicacion" => intval($publicacionId),
                "titulo" => $_POST['titulo'],
                "descripcion" => $_POST['descripcion'],
                "imagen" => $urlImagen,
                "estado" => 1,
                "tipo" => intval($_POST['tipo'])
              );
          }
    
          // Convertir el body a formato JSON
          $jsonData = json_encode($datosPublicacion,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
          //print_r($jsonData);
          // URL de la API
          $url = "https://apijoyeriav2.somee.com/api/UsuarioPublicacion/ActualizarPublicacion";
          // Inicializar cURL
        $ch = curl_init($url);
        
        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosPublicacion));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Establecer el encabezado "Content-Type"
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
            alertAviso("Mensaje","Publicación actualizada con éxito","Aceptar");
            //header('Location:index.php');
        } else {
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
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
                class="form-control" name="titulo" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ .\n,%]{5,100}$"
                minlength="5"
                maxlength="100" aria-describedby="helpNombre" placeholder="Ingrese un título para la nueva publicación"><?php echo $_GET['txtTitulo']; ?></textarea>
                <small id="helpNombre" class="form-text">El título debe tener mínimamente 5 caracteres, no puede exceder los 100 caracteres y solo puede utilizar la coma, letras/espacio.</small>
                <br/>
                <label for="descripcion" class="form-label">Descripcion (opcional):</label>
                <textarea type="text"
                class="form-control" name="descripcion" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ .\n,%][0-9]{5,500}$"
                minlength="5"
                maxlength="500" id="descripcion" aria-describedby="helpDescripcion" placeholder="Ingrese una descripción" onchange="validarDescripcionPublicacion(this.value)"><?php echo $_GET['txtDescripcion']; ?></textarea>
                <small id="helpDescripcion" class="form-text">La descripción debe tener mínimamente 5 caracteres, no puede exceder los 100 caracteres y solo puede utilizar letras/espacio.</small>
                <br/>
                <label class="form-label">Seleccione un tipo de publicación</label>
                <select id="tipoSelect" onchange="mostrarCamposPromocion(this.value)" required class="form-select form-select-lg" name="tipo">
                    <option value="1" <?php if ($_GET['txtTipo'] == 1) echo 'selected'; ?>>Publicación</option>
                    <option value="2" <?php if ($_GET['txtTipo'] == 2) echo 'selected'; ?>>Promoción</option>
                </select>
                <div id="promocionInputs">
                <label for="inputName">Seleccionar producto con promoción</label>
                <select required class="form-select form-select-lg" name="productoId">
                    <?php foreach ($productos->productos as $producto) {
                      ?>
                      <?php if($producto->id == $_GET['txtIdProducto']) {?>
                        <option selected value=<?php echo intval($_GET['txtIdProducto'])?>><?php echo $producto->nombre?></option>
                      <?php } 
                      else {?>
                      <option value=<?php echo $producto->id?>><?php echo $producto->nombre?></option>
                      <?php }
                      }?>
                </select>
                  <label for="" class="form-label">Descuento:</label>
                  <input required type="number" min="10" max="50" value=<?php echo $_GET['txtDescuento']?>
                    class="form-control" name="descuento" aria-describedby="helpId" placeholder="porcentaje">
                  <small id="helpId" class="form-text text-muted">Debe insertar una cantidad entre 10 y 50</small>
                </div>
                <label for="imagen" class="form-label">Escoger una imagen para la publicación (Se recomienda una imagen horizontal de 480x250 píxeles)</i>
                <input <?php echo (isset($_GET['txtImagen']))? "" : "required"?> type="file" accept=".jpg, .png" class="form-control" name="imagen" id="imagen" placeholder="Seleccione una imagen válida de tipo .jpg o .png" aria-describedby="ImagenHelpId">
                <div id="ImagenHelpId" class="form-text">Seleccione una imagen válida de tipo .jpg o .png y que no sea muy grande (máximo de 1024x1024 píxeles).</div>
                <!-- el estado por defecto será 1-->
            </div>
            <button type="submit" class="btn btn-success">Editar publicación</button>
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