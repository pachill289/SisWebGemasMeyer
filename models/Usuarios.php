<?php
// Definición de la clase
class Usuario {
    public $ci;
    public $clave;
    public $correo;
    public $tipo;
    public $estado;
    public $nombreCompleto;

    public function __construct($ci, $clave,$correo,$tipo,$estado,$nombreCompleto) {
        $this->ci = $ci;
        $this->clave = $clave;
        $this->correo = $correo;
        $this->tipo = $tipo;
        $this->estado = $estado;
        $this->nombreCompleto = $nombreCompleto;
    }

    public function miMetodo() {
        echo "Este es un método de la clase.";
    }
}

class Usuarios {
    public $usuarios;

    public function __construct() {
        $this->usuarios = array();
    }

    public function agregarUsuario($usuario) {
        $this->usuarios[] = $usuario;
    }
}

/* Crear un objeto de la clase
$objeto = new Usuario("valor1", "valor2");

// Acceder a las propiedades del objeto
echo $objeto->propiedad1;
echo $objeto->propiedad2;

// Llamar a un método del objeto
$objeto->miMetodo();
*/
