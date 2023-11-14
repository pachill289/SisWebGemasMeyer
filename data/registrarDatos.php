<?php
require_once 'constantes.php';
/**
 * Registra datos en una API externa utilizando una solicitud POST.
 *
 * Esta función toma datos de producto, categoría y subcategoría como entrada, los convierte a formato JSON
 * y realiza una solicitud POST a una API externa. Luego, muestra una alerta de éxito si la solicitud fue exitosa
 * o maneja el error si la solicitud falla.
 *
 * @param array $datos son los datos que se van a registrar.
 * @param string $categoria La categoría a la que pertenece el producto en la API.
 * @param string $subcategoria La subcategoría a la que pertenece el producto en la API.
 */
function registrarDatos($datos, $categoria, $subcategoria,$mensaje)
{
    $endpoint = "/api/$categoria/$subcategoria";
    $url = URL_API . $endpoint;
    
    // Convertir los datos del producto a formato JSON
    $jsonData = json_encode($datos);
    
    // Configurar el contexto de la solicitud
    $context = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                        "Accept: */*\r\n",
            'content' => $jsonData
        )
    ));
    
    // Realizar la solicitud POST
    $response = file_get_contents($url, false, $context);
    
    // Verificar si la solicitud fue exitosa
    if ($response !== false) {
        // Procesar la respuesta de la API aquí
        alertAviso("Mensaje",$mensaje, "Aceptar");
    } else {
        // Manejar el error de la API aquí
        $httpCode = http_response_code();
        echo "Error en la solicitud, el producto no se pudo registrar. Código de error: $httpCode";
        // Puedes obtener más detalles del error usando $http_response_header
    }
}
?>