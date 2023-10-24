<?php
    class Peticion {

        public $idPeticion;
        public $idUsuario;
        public $productoNombre;
        public $imagen;
        public $cantidad;
        public $especificaciones;
        public $estado;


        public function __construct($idPeticion,$idUsuario,$productoNombre,$imagen,$cantidad,$especificaciones,$estado)
        {
            $this->idPeticion = $idPeticion;
            $this->idUsuario = $idUsuario;
            $this->productoNombre = $productoNombre;
            $this->imagen = $imagen;
            $this->cantidad = $cantidad;
            $this->especificaciones = $especificaciones;
            $this->estado = $estado;
        }
    }

    class Peticiones {
        
        public $peticiones;
    
        public function __construct() {
            $this->peticiones = array();
        }
    
        public function agregarPeticion($peticion) {
            $this->peticiones[] = $peticion;
        }
    }
?>