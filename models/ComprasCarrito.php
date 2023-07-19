<?php
// Definición de la clase
class CompraCarrito {
    
    public $ciUsuario;
    public $nombreProducto;
    public $cantidad;
    public $precio;
    public $stock;

    public function __construct($ciUsuario,$nombreProducto,$cantidad,$precio,$stock) {
        $this->ciUsuario = $ciUsuario;
        $this->nombreProducto = $nombreProducto;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
        $this->stock = $stock;
    }
    public function actualizarCantidad($cantidadNueva)
    {
        $this->cantidad = $cantidadNueva;
    }
}

class ComprasCarrito {
    public $compras;

    public function __construct() {
        $this->compras = array();
    }

    public function agregarCompraCarrito($compra) {
        $this->compras[] = $compra;
    }
    public function quitarCompras() {
        $this->compras = array();
    }
} 
?>