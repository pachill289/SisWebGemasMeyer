<?php
    require_once('models/ComprasCarrito.php');
    session_start();
    $compras = $_SESSION['comprasCarrito'];
    $compras->quitarCompras();
    header('Location:index.php');
?>