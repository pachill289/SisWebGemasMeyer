<?php include('../../plantillas/header.php');?>
<h4>Listado de todas las compras pendientes</h4>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">idCompra</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Producto</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Saldo ingresado</th>
                            <th scope="col">Cantidad que desea el cliente</th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="">
                            <td scope="row">idCompra1</td>
                            <td>Cliente1</td>
                            <td>Producto1</td>
                            <td>Estado1</td>
                            <td>Saldo</td>
                            <td>Estado1</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalId">
                                Concretar venta <i class="bi bi-bag-check-fill"></i>
                                </button> | <a name="" id="" class="btn btn-danger" href="anular.php" role="button">Anular venta <i class="bi bi-arrow-down-circle"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Body -->
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">¿Concretar venta?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Desea concretar la venta ahora?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary">Si</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Optional: Place to the bottom of scripts -->
    <script>
        const myModal = new bootstrap.Modal(document.getElementById('modalId'), options)
    </script>
<?php include('../../plantillas/footer.php');?>