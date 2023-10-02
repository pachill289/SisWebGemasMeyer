<?php
    function actualizarDatosPorId($datos, $categoria, $subcategoria,$id,$mensaje)
    {
        // URL de la API
        $endpoint = "/api/$categoria/$subcategoria";
        $url = URL_API . $endpoint."/{$id}";
        // Inicializar cURL
        $ch = curl_init($url);
        
        // Configurar la solicitud PUT y otros ajustes necesarios
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Establecer el encabezado "Content-Type"
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
            ));
        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
        }
    
        // Obtener el código de respuesta HTTP
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // Cerrar la conexión cURL
        curl_close($ch);
    
        // Procesar la respuesta
        if ($httpCode == 200) {
            alertAviso("Mensaje",$mensaje,"Aceptar");
            //header('Location:index.php');
        } else {
            var_dump($datos);
            echo 'Error en la solicitud PUT. Código de respuesta: ' . $httpCode;
        }
    }
?>