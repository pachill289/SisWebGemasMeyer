document.addEventListener('DOMContentLoaded', function() {
    var valorTipo = $("#tipoSelect").val();
    //código a ejecutar cuando la página se carga
    if(valorTipo == 1)
    {
        $("#promocionInputs").hide();
    }
    else
    {
        $("#promocionInputs").show();
    }
});
//Si el usuario requiere una opción para borrar los datos
function recargarPagina()
{
    window.location.reload();
}
function validarTituloPublicacion(value) {
    function verificarNombre(nombre) {
        var regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 .\n,%]{5,100}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
        return regex.test(nombre);
    }
    var targetElement = $("small").eq(0); // Acceder al primer elemento <small>
    const texto = 'El título debe tener mínimamente 5 caracteres, no puede exceder los 100 caracteres y solo puede utilizar la coma, letras/espacio.';
    const textoVacio = 'Debe ingresar un título.⚠';
    if (verificarNombre(value)) {
      targetElement.css("color", "green");
      targetElement.text(textoCorrecto);
    } else {
        if(value == "")
        {
            targetElement.css("color", '#b59410');
            targetElement.text(textoVacio);
        }
        else
        {
            targetElement.css("color", "red");
            targetElement.text(texto);
        }
    }
}
function validarDescripcionPublicacion(value) {
    function verificarDescripcion(descripcion) {
        var regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 .\n,%]{5,500}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
        return regex.test(descripcion);
    }
    var targetElement = $("small").eq(1); // Acceder al primer elemento <small>
    const texto = 'La descripción debe tener mínimamente 5 caracteres, no puede exceder los 500 caracteres y solo puede utilizar letras/espacio,comas y porcentajes.';
    if (verificarDescripcion(value)) {
      targetElement.css("color", "green");
      targetElement.text(textoCorrecto);
    } else {
      targetElement.css("color", "red");
      targetElement.text(texto);
    }
}

function mostrarCamposPromocion (value)
{
    if(value == 2)
    {
        $("#promocionInputs").show();
        $("#productoSelect").show();
    }
    else
    {
        $("#promocionInputs").hide();
        $("#productoSelect").hide();
    }
}