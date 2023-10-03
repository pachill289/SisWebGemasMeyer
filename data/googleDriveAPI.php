<?php
//Importar librería
require_once '../../vendor/autoload.php';
require_once '../../componentes/componentesHtml.php';
//Es necesario actualizar la cuenta de servicio de google si esta ha caducado,la misma caduca el 31   de diciembre de 2023
putenv('GOOGLE_APPLICATION_CREDENTIALS=../../data/webgemasmeyer-2670159b89b9.json');

//Funciones
//Función que obtiene el cliente de google
/**
 * Función que obtiene el cliente de google drive
 *
 * Esta función crea y configura un cliente de Google Drive utilizando credenciales de aplicación predeterminadas.
 * El cliente configurado tiene acceso a la API de Google Drive.
 *
 * @return Google_Client Una instancia del cliente de Google Drive configurada para acceder a la API de Google Drive.
 */
    function GetDriveClient()
    {
        //Definir el servicio de google
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        //$client->setScopes(['https://www.googleapis.com/auth/drive.file']);
        $client->addScope(Google_Service_Drive::DRIVE);
        return $client;
        //API key (opcional si se usa AuthO): AIzaSyBfPk0hwW5WmPEtkOTdJlIN7XEb283BgIM
    }
    /**
 * Obtiene una instancia del servicio de Google Drive utilizando un cliente de Google Drive proporcionado.
 *
 * Esta función crea y devuelve una instancia del servicio de Google Drive utilizando un cliente de Google Drive
 * previamente configurado. El servicio se puede utilizar para interactuar con la API de Google Drive.
 *
 * @param Google_Client $googleDriveClient El cliente de Google Drive configurado para acceder a la API de Google Drive.
 * @return Google_Service_Drive Una instancia del servicio de Google Drive configurada para interactuar con la API de Google Drive.
 */
    function GetDriveService($googleDriveClient)
    {
        return new Google_Service_Drive($googleDriveClient);
    }
/**
 * Verifica si un archivo existe en una carpeta específica en Google Drive.
 *
 * Esta función realiza una consulta en Google Drive para verificar si un archivo con el nombre dado ya existe
 * en la carpeta especificada. Si el archivo existe en la carpeta, muestra una alerta de advertencia.
 *
 * @param string $fileName El nombre del archivo que se desea verificar.
 * @param string $folderId El ID de la carpeta de Google Drive en la que se debe buscar el archivo.
 * @param Google_Service_Drive $googleDriveService Una instancia del servicio de Google Drive para realizar la verificación.
 * @return boolean Esta función devuelve true o false y muestra una alerta si el archivo ya existe.
 */
    function verifyFileInFolder($fileName,$folderId,$googleDriveService)
    {
        // Verificar si el archivo ya existe en la carpeta de destino
        $query = "name='$fileName' and '$folderId' in parents and trashed=false";
        $existingFiles = $googleDriveService->files->listFiles(array('q' => $query));
        if (count($existingFiles->getFiles()) > 0) {
          // El archivo ya existe en la carpeta
          alert('Advertencia','El archivo ya existe en la carpeta de Google Drive <br/> si ya subió la imagen considere en escoger una imagen almacenada','Ok');
          return true;
        }
        else
        {
            return false;
        }
    }
    /**
 * Crea un archivo en una carpeta específica en Google Drive a partir de una imagen local y obtiene su enlace web.
 *
 * Esta función crea un archivo en Google Drive utilizando una imagen local y lo coloca en la carpeta especificada.
 * Luego, obtiene el enlace web del archivo creado y lo devuelve.
 *
 * @param string $fileName El nombre del archivo que se creará en Google Drive.
 * @param string $folderId El ID de la carpeta de Google Drive donde se colocará el archivo.
 * @param string $fileImgPath La ruta local de la imagen que se utilizará para crear el archivo en Google Drive.
 * @param Google_Service_Drive $googleDriveService Una instancia del servicio de Google Drive para realizar la operación.
 * @return string El enlace web al archivo creado en Google Drive.
 */
    function CreateFileInFolderGetImgUrl($fileName,$folderId,$fileImgPath,$googleDriveService)
    {
        // Crear un archivo en Google Drive
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $fileName,
            'parents' => array($folderId), // ID de la carpeta de destino en Google Drive
          ));
        
        $fileContent = file_get_contents($fileImgPath);
    
        $file = $googleDriveService->files->create($fileMetadata, array(
        'data' => $fileContent,
        'uploadType' => 'multipart',
        'fields' => 'id, webContentLink',
        ));
        return str_replace('&export=download','',$file->webContentLink);
    }
?>