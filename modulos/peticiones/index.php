<?php include('../../plantillas/header.php');?>

<?php
    require_once('../../data/obtenerDatos.php');
    require_once('../../models/Usuarios.php');
    require_once('../../models/Peticiones.php');
    require_once('../../componentes/componentesHtml.php');
     //Agregar a todos los usuarios desde la API
     $usuarios = new Usuarios();
     foreach (construirEndpoint('Usuario', 'ObtenerUsuarios') as $usuario) {
         $usuarios->agregarUsuario(new Usuario(
             $usuario->ci,
             $usuario->clave,
             $usuario->correo,
             $usuario->tipo,
             $usuario->estado,
             $usuario->nombreCompleto
         ));
     }
    //Agregar a todos los productos desde la API
    $peticiones = new Peticiones();
    //agregar todos los productos al objeto Productos (arreglo de productos)
    foreach (construirEndpoint('UsuarioPeticion', 'ObtenerPeticiones') as $peticion) {
        $peticiones->agregarPeticion(new Peticion(
            $peticion->idPeticion,
            $peticion->idUsuario,
            $peticion->productoNombre,
            $peticion->imagen,
            $peticion->cantidad,
            $peticion->especificaciones,
            $peticion->estado
        ));
    }
     
?>
<h4>Listado de todas las peticiones</h4>
    <!-- Filtrar peticiones -->
    <form style="margin:10px;" method="post">
        <div class="mb-3">
            <select class="form-select form-select-lg" name="tipoPublicacion">
                <option value="1" selected>Mostrar peticiones recientes</option>
                <option value="2" >Mostrar peticiones antiguas</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Listar</button>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">nroPeticion</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Producto</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Especificaciones</th>
                            <th scope="col">Estado</th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Obtener todos los pedidos -->
                        <?php foreach ($peticiones->peticiones as $peticion) { ?>
                        <tr class="">
                            <td scope="row"><?php echo $peticion->idPeticion?></td>
                            <td><?php foreach ($usuarios->usuarios as $usuario) {
                                    if($usuario->ci == $peticion->idUsuario)
                                    {
                                        echo $usuario->nombreCompleto;
                                    }
                                }
                                ?></td>
                            <td><?php echo $peticion->productoNombre?></td>
                            <td><?php echo $peticion->cantidad?></td>
                            <td><a name="" id="" class="btn btn-primary" href="#" role="button">Especificaciones <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                            </svg></a></td>
                            <td><?php echo ($peticion->estado == 0 ? 
                            "<div class='alert alert-warning' role='alert'>PENDIENTE</div>" : 
                            ($pedido->estado == 1 ? 
                            "<div class='alert alert-success' role='alert'>APROBADO</div>" : 
                            ($pedido->estado == 2 ? 
                            "<div class='alert alert-danger' role='alert'>DENEGADO</div>": 
                            "<div class='alert alert-secondary' role='alert'>ANULADO</div>"))) ?></td>
                            <td class="text-center">
                                    <a name="" id="" class="btn btn-primary" href="#"  role="button">Aprobar <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
                                    <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
                                    </svg></a> 
                                    <?php espacio_br(2) ?>
                                    <a name="" id="" class="btn btn-danger" href="index.php?ciAnu=<?php echo $usuario->ci;?>"  role="button">Denegar <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                                    <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
                                    </svg> </a>
                            </td>
                        </tr>
                        <?php 
                    }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php include('../../plantillas/footer.php');?>