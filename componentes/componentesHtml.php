<?php 
    
    function ir_a_inicio(){
        header('Location:index.php');
    }
    //componentes html
    function espacio_br($nroLineas){
        for($i=0;$i<$nroLineas;$i++) {
            echo '<br>';
        }
    }
    function margen($margen)
    {
        $margen .= 'px';
        echo "<p style=\"margin: $margen;border: 1px solid black;\">Hola</p>";
    }
    //componentes de bootstrap
    function alert($titulo,$texto,$txtAceptar) {
        $html = "
        <!-- Modal Body -->
        <div class='modal fade' id='modalId' tabindex='-1' role='dialog' aria-labelledby='modalTitleId' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalTitleId'>$titulo</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        $texto
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>$txtAceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Script para activar el alert -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalId'));
                myModal.show();
            });
        </script>";
    
        echo $html;
    }
    //alert de aviso
    function alertAviso($titulo,$texto,$txtAceptar) {
        $html = "
        <!-- Modal Body -->
        <div class='modal fade' id='modalId' tabindex='-1' role='dialog' aria-labelledby='modalTitleId' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-scrollable modal-dialog-top modal-sm' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalTitleId'>$titulo</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        $texto
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>$txtAceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Script para activar el alert -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalId'));
                myModal.show();

                // Agregar evento de clic al botón 'Aceptar'
                var btnAceptar = document.querySelectorAll('#modalId .modal-footer button')[0];
                btnAceptar.addEventListener('click', function() {
                    window.location.href = 'index.php';
                });
            });
        </script>";
    
        echo $html;
    }
    //alert personalizado con mas opciones
    function alertOp($titulo,$texto,$txtAceptar) {
        $html = "
        <!-- Modal Body -->
        <div class='modal fade' id='modalId' tabindex='-1' role='dialog' aria-labelledby='modalTitleId' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-scrollable modal-dialog modal-sm' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalTitleId'>$titulo</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        $texto
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>$txtAceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Script para activar el alert -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalId'));
                myModal.show();
            });
        </script>";
    
        echo $html;
    }
?>