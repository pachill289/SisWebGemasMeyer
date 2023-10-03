<?php
    if($_POST)
    {
        // Datos del body
      $datosUsuario = array(
        "ci" => $_POST['txtCi'],
        "clave" => $_POST['clave'],
        "correo" => $_POST['correo'],
        "tipo" => intval($_POST['tipo']),
        "estado" => intval($_POST['estado']),
        "nombreCompleto" => $_POST['nombreCompleto']
      );
      //Uso del método PUT personalizado en php con curl
      $url = "https://apijoyeriav2.somee.com/api/Usuario/ActualizarUsuario";

      // Inicializar cURL
      $ch = curl_init($url);

      // Configurar la solicitud PUT y otros ajustes necesarios
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosUsuario));
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
          header('Location:index.php');
      } else {
          var_dump($datosUsuario);
          echo http_build_query($datosUsuario);
          echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
      }
    }
?>
<?php include('../../plantillas/header.php');?>
<h4>Editar usuario</h4>
    <div class="card">
        <div class="card-header">
            Datos del usuario
        </div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="txtCi" class="form-label">Ci:</label>
                  <input type="number" readonly
                    class="form-control text-muted" name="txtCi" required pattern="^[0-9]{7,10}$" maxlength="10" id="txtCi" value="<?php echo $_GET['txtCi'];?>" title="No puede editar el ci" aria-describedby="helpCi">
                    <small id="helpNombre" class="form-text"></small>
                  <br/>
                  <label for="nombreCompleto" class="form-label">Nombre completo:</label>
                  <input type="text"
                    class="form-control" name="nombreCompleto" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{15,50}$"
                    value="<?php echo $_GET['txtNombre'];?>"
                    minlength="15"
                    maxlength="50" id="nombreCompleto" aria-describedby="helpNombre" placeholder="Ingrese su nombre" onchange="validarNombre(this.value)">
                  <small id="helpNombre" class="form-text">El nombre debe tener mínimamente 30 caracteres, no puede exceder los 50 caracteres y solo puede utilizar letras/espacio.</small>
                  <br/>
                  <div class="mb-3">
                    <label for="clave" class="form-label">Contraseña:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="clave" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$" value="<?php echo $_GET['txtClave'];?>" id="clave" placeholder="Ingrese una contraseña" onchange="validarClave(this.value)">
                        <div class="input-group-append">
                            <a id="iconoClave" class="input-group-text" onclick="mostrarClave(this);"><i class="bi bi-eye"></i></a>
                            <a id="iconoClave2" class="input-group-text" onclick="mostrarClave(this);"><i class="bi bi-eye-slash-fill"></i></a>
                        </div>
                    </div>
                    <small id="helpClave" class="form-text">La contraseña debe tener 8 caracteres y contener al menos una letra mayúscula, una letra minúscula, un número y un caracter especial.</small>
                  </div>
                  <label for="correo" class="form-label">Correo:</label>
                  <input type="text"
                    class="form-control" name="correo" required pattern="^[\w\-]+(\.[\w\-]+)*@([\w\-]+\.)+[a-zA-Z]{2,3}$" value="<?php echo $_GET['txtCorreo'];?>" id="correo" aria-describedby="helpCorreo" placeholder="Ingrese su correo electrónico" onchange="validarCorreo(this.value)">
                  <small id="helpCorreo" class="form-text">El correo electrónico debe contener almenos una @ y un dominio .com .es,etc..</small>
                  <br/>
                  <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo:</label>
                    <select required class="form-select form-select-lg" name="tipo" id="tipo">
                    <option value="1" <?php if ($_GET['txtTipo'] == 1) echo 'selected'; ?>>
                    Administrador
                    </option>
                    <option value="2" <?php if ($_GET['txtTipo'] == 2) echo 'selected'; ?>>
                    Vendedor
                    </option>
                    <option value="3" <?php if ($_GET['txtTipo'] == 3) echo 'selected'; ?>>
                        Cliente
                    </option>
                    </select>
                  </div>
                  <input type="hidden"
                    class="form-control" name="estado" value=1>
                </div>
                <button type="submit" class="btn btn-success">Guardar cambios</button>
                <a name="" id="" class="btn btn-danger" href="index.php" role="button">Cancelar</a>
            </form>
        </div>
        <div class="card-footer text-muted">
            
        </div>
    </div>
<?php include('../../plantillas/footer.php');?>