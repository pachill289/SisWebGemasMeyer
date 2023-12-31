<?php
    class Producto {
        public $id;
        public $nombre;
        public $descripcion;
        public $precio;
        public $cantidad;
        public $categoria;
        public $imagen;
        public $estado;

        public function __construct($id,$nombre,$precio,$cantidad,$estado,$imagen,$categoria)
        {
            $this->id = $id;
            $this->nombre = $nombre;
            $this->descripcion = null;
            $this->precio = $precio;
            $this->cantidad = $cantidad;
            $this->categoria = $categoria;
            $this->imagen = $imagen;
            $this->estado = $estado;
        }
    }

    class Productos {
        public $productos;
    
        public function __construct() {
            $this->productos = array();
        }
    
        public function agregarProducto($producto) {
            $this->productos[] = $producto;
        }
    }
?>