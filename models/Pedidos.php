<?php
    class Pedido {

        public $idPedido;
        public $idUsuario;
        public $idProducto;
        public $estado;
        public $cantidadProducto;
        public $fecha;


        public function __construct($idPedido,$idUsuario,$idProducto,$estado,$cantidadProducto,$fecha)
        {
            $this->idPedido = $idPedido;
            $this->idUsuario = $idUsuario;
            $this->idProducto = $idProducto;
            $this->estado = $estado;
            $this->cantidadProducto = $cantidadProducto;
            $this->fecha = $fecha;
        }
    }

    class Pedidos {
        
        public $pedidos;
    
        public function __construct() {
            $this->pedidos = array();
        }
    
        public function agregarPedido($pedido) {
            $this->pedidos[] = $pedido;
        }
    }
?>