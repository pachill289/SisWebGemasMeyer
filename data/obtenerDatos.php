<?php
require_once ('constantes.php');
/**
* Esta función obtiene endpoints desde una url de una API de tipo http
*/
function construirEndpoint($categoria, $subcategoria) {

    $endpoint = "/api/$categoria/$subcategoria";

    $url = URL_API.$endpoint;

    //encabezado
    $opciones = [
        'http' => [
            'method' => 'GET', // Método HTTP
        ],
    ];

    $context = stream_context_create($opciones);
    $respuesta = file_get_contents($url, false, $context);
    
    if ($respuesta === false) {
        // Error al realizar la solicitud
        echo "Error al realizar la solicitud a la API. con $categoria como categoría principal  y $subcategoria como la subcategoría";
    } else {
        // Procesar la respuesta
        $datos = json_decode($respuesta);
        //devolver los datos en un array
        return $datos;
    }
}
function construirEndpointParametro($categoria, $subcategoria,$arg) {

    $baseURL = 'https://apijoyeriav2.somee.com';

    $endpoint = "/api/$categoria/$subcategoria/$arg";

    // Construir la URL completa del endpoint
    $url = $baseURL . $endpoint;

    //encabezado
    $opciones = [
        'http' => [
            'method' => 'GET', // Método HTTP
        ],
    ];

    $context = stream_context_create($opciones);
    $respuesta = file_get_contents($url, false, $context);

    if ($respuesta === false) {
        // Error al realizar la solicitud
        echo "Error al realizar la solicitud a la API. con $categoria como categoría principal  y $subcategoria como la subcategoría";
    } else {
        // Procesar la respuesta
        $datos = json_decode($respuesta);
        //devolver los datos en un array
        return $datos;
    }
}
?>
