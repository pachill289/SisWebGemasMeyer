<?php include('../../plantillas/header.php');?>
  <?php
    //API Google drive uso de composer
    require_once '../../componentes/componentesHtml.php';
    require_once '../../models/Productos.php';
    require_once '../../data/obtenerDatos.php';
    require_once '../../data/googleDriveAPI.php';
    require_once '../../data/registrarDatos.php';
    require_once '../../data/constantes.php';
    require_once '../../vendor/autoload.php';
    //Obtiene el servicio de google drive listo para ser usado
    $googleDriveSerive = GetDriveService(GetDriveClient());
    $urlImagen = '';
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
        $productoExistente = false;
        // Obtener información de la imagen subida
        $file = $_FILES['imagen'];
        // Verificar si se cargó correctamente
        if($_POST['imagen_seleccionada']=="ninguno")
        {
          if ($file['error'] === UPLOAD_ERR_OK) {
            // Obtener el nombre y la ruta temporal del archivo
            $fileName = $file['name'];
            $filePath = $file['tmp_name'];
            if(!verifyFileInFolder($fileName,GOOGLE_DRIVE_FOLDER_ID,$googleDriveSerive)) {
              // Crear un archivo en Google Drive
              $urlImagen = CreateFileInFolderGetImgUrl($fileName,GOOGLE_DRIVE_FOLDER_ID,$filePath,$googleDriveSerive);
              //Verificar si el producto ya existe
              foreach (construirEndpoint('Producto', 'ObtenerProductos') as $producto) {
                if ($producto->nombre == $_POST['nombre']) {
                    $productoExistente = true;
                }
              }
              if($productoExistente == false)
              {
                //Subir datos a la API
                // Datos del body
                $datosProducto = array(
                  "idProducto" => 0,
                  "nombre" => $_POST['nombre'],
                  "descripcion" => "",
                  "precio" => $_POST['precio'],
                  "cantidad" => $_POST['cantidad'],
                  "categoria" => "",
                  "imagen" => $_POST['imagen_seleccionada'],
                  "estado" => 0
                );
                //var_dump($datosProducto);
                registrarDatos($datosProducto,'Producto','RegistrarProducto');
              }
              else
              {
                alert("Aviso","El nombre del producto ya existe, vuelva a intentarlo.","Aceptar");
              }
              
            }
          } else {
            // Mostrar un mensaje de error en caso de fallo en la carga
            alert("Aviso","Suba o seleccione una imagen","Aceptar");
          }
        }
        else
        {
          //Verificar si el producto ya existe
          foreach (construirEndpoint('Producto', 'ObtenerProductos') as $producto) {
            if ($producto->nombre == $_POST['nombre']) {
                $productoExistente = true;
            }
          }
          if($productoExistente == false)
          {
          $datosProducto = array(
                "idProducto" => 0,
                "nombre" => $_POST['nombre'],
                "descripcion" => "",
                "precio" => $_POST['precio'],
                "cantidad" => $_POST['cantidad'],
                "categoria" => "",
                "imagen" => $_POST['imagen_seleccionada'],
                "estado" => 0
              );
          registrarDatos($datosProducto,'Producto','RegistrarProducto');
          }
          else
          {
            alert("Aviso","El nombre del producto ya existe,vuelva a intentarlo","Aceptar");
          }
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
                  <div id="imagenSelection">
                  <label for="imagen" class="form-label">Escoger una imagen y subirla a google drive</label> <i class="bi bi-google"></i>
                  <input id="seleccionNuevaImagen" type="file" accept=".jpg, .png" class="form-control" name="imagen" placeholder="Seleccione una imagen válida de tipo .jpg o .png" aria-describedby="ImagenHelpId">
                  <div id="ImagenHelpId" class="form-text">Seleccione una imagen válida de tipo .jpg o .png y que no sea muy grande (máximo de 1024x1024 píxeles).</div>
                  </div>
                  <!-- el estado se calcula automáticamente con un trigger si la cantidad es mayor a 0 el estado es activo de lo contrario es inactivo -->
                  <small id="helpEstado" class="form-text">Si la cantidad es mayor a 0 el estado del producto es de tipo activo de lo contrario es inactivo.</small>
                  <br/>
                  <label>Seleccionar imagen desde google drive (En caso de que la imagen ya exista):</label>
                  <?php espacio_br(1);?>
                  <?php
                    try {
                      $folderId = GOOGLE_DRIVE_FOLDER_ID;
                      // Realiza una consulta para obtener la lista de archivos en la carpeta
                      $results = $googleDriveSerive->files->listFiles([
                          'q' => "'$folderId' in parents and mimeType contains 'image/'",
                      ]);
                      
                      // Crea el elemento select
                      echo '<select class="form-select form-select-lg" name="imagen_seleccionada" id="imagen_seleccionada">';
                      echo '<option selected value="ninguno">Seleccione una imagen</option>';
                      $ur_base_img = "https://drive.google.com/uc?id=";
                      foreach ($results->getFiles() as $file) {
                          echo '<option value="' . $ur_base_img.$file->getId() . '">'. 
                          $file->getName().'</option>';
                      }
                      
                      echo '</select>';
                      // Crea un elemento div para mostrar la imagen seleccionada
                      echo '<div id="imagen_mostrada"></div>';

                      // JavaScript para actualizar la imagen cuando se seleccione una opción
                      echo '<script>
                      document.getElementById("seleccionNuevaImagen").addEventListener("change", function() {
                        console.log(this.value);
                        var inputElement = document.getElementById("imagen_seleccionada");
                        inputElement.setAttribute("hidden", "true");
                      });
                          document.getElementById("imagen_seleccionada").addEventListener("change", function() {
                              var selectedOption = this.options[this.selectedIndex];
                              var inputElement = document.getElementById("imagenSelection");
                              if(this.selectedIndex == 0)
                              {
                                inputElement.removeAttribute("hidden");
                              }
                              else
                              {
                                inputElement.setAttribute("hidden", "true");
                              }
                              var fileId = selectedOption.value;
                              var imageUrl = fileId;
                              document.getElementById("imagen_mostrada").innerHTML = "<br><img width=200 height=200 src=\"" + imageUrl + "\" alt=\"sin imagen\">";
                          });
                      </script>';
                  } catch (Exception $e) {
                      echo 'Error: ' . $e->getMessage();
                  }
                  ?>
                </div>
                <button type="submit" class="btn btn-success">Registrar producto</button>
                <a name="" id="" class="btn btn-danger" href="index.php" role="button">Cancelar</a>
           </form>
        </div>
    </div>
<?php include('../../plantillas/footer.php');?>