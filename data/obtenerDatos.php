<?php
function construirEndpoint($categoria, $subcategoria) {
    $baseURL = 'http://apijoyeriav2.somee.com';
    $endpoint = '/api/';

    // Concatenar las partes del endpoint
    $endpoint .= $categoria . '/' . $subcategoria;

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
function construirEndpointParametro($categoria, $subcategoria,$arg) {
    $baseURL = 'http://apijoyeriav2.somee.com';
    $endpoint = '/api/';

    // Concatenar las partes del endpoint
    $endpoint .= $categoria . '/' . $subcategoria.'/'.$arg;

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
