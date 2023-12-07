<?php
    /**
    * Envía argumentos php a una función JavaScript.
    *
    * @param string $nombreFuncion El nombre de la función JavaScript.
    * @param mixed  ...$args       Los argumentos que se enviarán a la función.
    *
    * @return void
    */
    function sendJsArgs($nombreFuncion, ...$args)
    {
        //Juntar la cadena en un string con un separador en esta caso la coma
        $argsString = implode(',', $args);
        //Devuelve una cadena: funcion(arg1,arg2,arg3...) para ser interpretada como una función javaScript
        echo "$nombreFuncion($argsString)";
    }

    function recieveCantidad($cantidad)
    {
        echo $cantidad;
    }
?>