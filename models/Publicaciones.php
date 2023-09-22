<?php
    // Definición de la clase
class Publicacion {
    public $idPublicacion;
    public $titulo;
    public $descripcion;
    public $imagen;
    public $estado;
    public $tipo;
    public $idProducto;
    public $descuento;

    public function __construct($idPublicacion, $titulo,$descripcion,$imagen,$estado,$tipo,$idProducto = null,$descuento = 0) {
        $this->idPublicacion = $idPublicacion;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
        $this->estado = $estado;
        $this->tipo = $tipo;
        //Si idProducto es diferente de nulo  y el descuento es mayor a 0 se genera una promoción
        if($idProducto != null && $descuento > 0)
        {
            $this->idProducto = $idProducto;
            $this->descuento = $descuento;
            $this->tipo = 2;
        }
        else
        {
            $this->idProducto = null;
            $this->descuento = 0;
            $this->tipo = 1;
        }
    }
}

class Publicaciones {
    public $publicaciones;

    public function __construct() {
        $this->publicaciones = array();
    }

    public function agregarPublicacion($publicacion) {
        $this->publicaciones[] = $publicacion;
    }
}
?>