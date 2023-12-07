<?php
  include('plantillas/header.php'); 
  require('componentes/componentesHtml.php');
  require_once('data/obtenerDatos.php');
  require_once('models/Usuarios.php');
  require_once('data/registrarDatos.php');

  $usuarioExistente = false;
    //Verificar registro/añadir nuevo usuario mediante a la API
    
    if($_POST && isset($_POST['ci']))
    {
      //Verificar si el producto ya existe
       foreach (construirEndpoint('Usuario', 'ObtenerUsuarios') as $usuario) {
        if ($usuario->ci == $_POST['ci']) {
            $usuarioExistente = true;
        }
      }
      if(!$usuarioExistente)
      {
        // Datos del body
        $datosUsuario = array(
          "ci" => $_POST['ci'],
          "clave" => $_POST['clave'],
          "correo" => $_POST['correo'],
          "celular" => $_POST['celular'],
          "tipo" => $_POST['tipo'],
          "estado" => $_POST['estado'],
          "nombreCompleto" => $_POST['nombreCompleto']
        );
        
        //Lógica verificación email
        function generarCodigoVerificacion() {
            // Longitud del código de verificación
            $longitud = 4;
        
            // Caracteres permitidos en el código
            $caracteresPermitidos = '0123456789';
        
            // Generar el código aleatorio
            $codigo = '';
            for ($i = 0; $i < $longitud; $i++) {
                $codigo .= $caracteresPermitidos[rand(0, strlen($caracteresPermitidos) - 1)];
            }
        
            return $codigo;
        }
        function generarLinkConfirmacion($codigoVerificacion) {
            // URL de confirmación
            $urlConfirmacion = "http://localhost:80/confirmar.php";
        
            // Agregar el código de verificación como parámetro en la URL
            $linkConfirmacion = $urlConfirmacion . "?codigo=" . $codigoVerificacion;
        
            return $linkConfirmacion;
        }
        $destinatario = $_POST['correo'];
        $asunto = "Verificación de Correo";
        
        // Generar un código de verificación (puedes implementar tu propia lógica)
        $codigoVerificacion = generarCodigoVerificacion();
        $linkConfirmacion = generarLinkConfirmacion($codigoVerificacion);

        // Mensaje de correo electrónico
        $mensaje = "¡Gracias por registrarte! Tu código de verificación es: $codigoVerificacion, ingresa al siguiente enlace: $linkConfirmacion para verificar tu correo electrónico ingresando el código $codigoVerificacion";

        // Cabeceras del correo electrónico
        $headers = "From: $destinatario\r\n";
        $headers .= "Reply-To: $destinatario\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        // Configuración de seguridad para STARTTLS
        $opciones = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ];
        
        //stream_context_set_option(stream_context_get_default(), $opciones);
        // Enviar el correo electrónico
        //mail($destinatario, $asunto, $mensaje, $headers);
        registrarDatosLogin($datosUsuario,'Usuario','RegistrarUsuario','Se ha registrado correctamente',$_POST['ci'],$_POST['clave']);
      }
      else
      {
        alert("Aviso ⚠","El usuario ya existe,vuelva a intentarlo","Aceptar");
      }
    }
    
?>
    <h4>Formulario de registro</h4>
    <div class="card">
        <div class="card-header">
            Ingrese sus datos
        </div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="ci" class="form-label">Ci:</label>
                  <input type="number"
                    class="form-control" name="ci" required pattern="^[0-9]{7,10}$" maxlength="10" id="ci" aria-describedby="helpCi" placeholder="Ingrese un ci válido" onchange="validarCI(this.value)">
                  <small id="helpCi" class="form-text">El CI debe contener al menos 7 dígitos y no más de 10.</small>
                  <br/>
                  <label for="nombreCompleto" class="form-label">Nombre completo:</label>
                  <input type="text"
                    class="form-control" name="nombreCompleto" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{3,50}$"
                    minlength="3"
                    maxlength="50" id="nombreCompleto" aria-describedby="helpNombre" placeholder="Ingrese su nombre" onchange="validarNombre(this.value)">
                  <small id="helpNombre" class="form-text">El nombre debe tener mínimamente 3 caracteres, no puede exceder los 50 caracteres y solo puede utilizar letras/espacio.</small>
                  <br/>
                  <div class="mb-3">
                    <label for="clave" class="form-label">Contraseña:</label>
                    <div class="input-group">
                      <input type="password" class="form-control" name="clave" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$" id="clave" placeholder="Ingrese  una contraseña" onchange="validarClave(this.value)">
                      <div class="input-group-append">
                              <a id="iconoClave" class="input-group-text" onclick="mostrarClave(this);  "><i class="bi bi-eye-slash-fill"></i></a>
                              <a id="iconoClave2" class="input-group-text" onclick="mostrarClave(this); "><i class="bi bi-eye"></i></a>
                      </div>
                    </div>
                    <small id="helpClave" class="form-text">La contraseña debe tener 8 caracteres y contener por lo menos un letra mayúscula,una letra minúscula, un número y un caracter especial.</small>
                  </div>
                  <label for="correo" class="form-label">Correo:</label>
                  <input type="text"
                    class="form-control" name="correo" required pattern="^[\w\-]+(\.[\w\-]+)*@([\w\-]+\.)+[a-zA-Z]{2,3}$" id="correo" aria-describedby="helpCorreo" placeholder="Ingrese su correo electrónico" onchange="validarCorreo(this.value)">
                  <small id="helpCorreo" class="form-text">El correo electrónico debe contener almenos una @ y un dominio .com .es,etc..</small>
                  <br/>
                  <label for="correo" class="form-label">Celular:</label>
                  <input type="number"
                    class="form-control" name="celular" required pattern="^[67]\d{7}$" id="celular" aria-describedby="helpCelular" placeholder="Ingrese su celular" onchange="validarCelular(this.value)">
                  <small id="helpCorreo" class="form-text">El celular debe empezar por 6 o 7 y debe contener 8 dígitos.</small>
                  <br/>
                  <div class="mb-3">
                    <select hidden required class="form-select form-select-lg" name="tipo" id="tipo">
                        <option selected value="3">Cliente</option>
                    </select>
                  </div>
                  <small id="helpEstado" class="form-text">Si usted se registra, su estado por defecto será de tipo activo.</small>
                  <!-- el usuario por defecto ya esta activo si se registra -->
                  <input type="hidden"
                    class="form-control" name="estado" value=1>
                </div>
                <button type="submit" class="btn btn-success">Registrarse</button>
                <a class="btn btn-danger" href="index.php" role="button">Cancelar</a>
            </form>
        </div>
</div>
<?php include('plantillas/footer.php'); ?>