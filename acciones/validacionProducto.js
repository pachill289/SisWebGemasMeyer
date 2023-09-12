window.onload = () => {
    var targetElement = $("small").eq(5);
    targetElement.css("fontWeight", "bold");
    targetElement.css("color", "black");
}
//Si el usuario requiere una opción para borrar los datos
function recargarPagina()
{
    window.location.reload();
}
function validarNombreProducto(value) {
    function verificarNombre(nombre) {
        var regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 .()]{5,500}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
        return regex.test(nombre);
    }
    var targetElement = $("small").eq(0); // Acceder al primer elemento <small>
    const texto = 'El nombre debe tener mínimamente 5 caracteres, no puede exceder los 500 caracteres y solo puede utilizar letras,números y el punto/espacio.';
    const textoVacio = 'Debe ingresar un nombre.⚠';
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
function validarDescripcion(value) {
    function verificarDescripcion(descripcion) {
        var regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 .\n]{5,100}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
        return regex.test(descripcion);
    }
    var targetElement = $("small").eq(1); // Acceder al primer elemento <small>
    const texto = 'La descripción debe tener mínimamente 5 caracteres, no puede exceder los 100 caracteres y solo puede utilizar letras/espacio.';
    if (verificarDescripcion(value)) {
      targetElement.css("color", "green");
      targetElement.text(textoCorrecto);
    } else {
      targetElement.css("color", "red");
      targetElement.text(texto);
    }
}
/* Por hacer
function validarCategoria(value) {
    function verificarCategoria(categoria) {
        var regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 .\n]{5,50}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
        return regex.test(categoria);
    }
    var targetElement = $("small").eq(1); // Acceder al primer elemento <small>
    const texto = 'La categoría debe tener mínimamente 5 caracteres, no puede exceder los 50 caracteres y solo puede utilizar letras/espacio.';
    if (verificarCategoria(value)) {
      targetElement.css("color", "green");
      targetElement.text(textoCorrecto);
    } else {
      targetElement.css("color", "red");
      targetElement.text(texto);
    }
}*/
function validarPrecio(value) {
    function verificarPrecio(precio) {
        if(precio>0)
            return true;
        else
            return false;
      }
      var targetElement = $("small").eq(2); // Acceder al primer elemento <small>
      const texto = 'El precio debe ser mayor a 0.';
      const textoVacio = 'Debe ingresar un precio.⚠';
      if (verificarPrecio(value)) {
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
  function validarCantidad(value) {
    function verificarCantidad(cantidad) {
        if(cantidad>=0)
            return true;
        else
            return false;
      }
      var targetElement = $("small").eq(3); // Acceder al primer elemento <small>
      const texto = 'El cantidad debe ser mayor o igual a 0.';
      const textoVacio = 'Debe ingresar una cantidad.⚠';
      if(value == "")
      {
        targetElement.css("color", '#b59410');
        targetElement.text(textoVacio);
      }
      else if (verificarCantidad(value)) {
        targetElement.css("color", "green");
        targetElement.text(textoCorrecto);
      } else {
            targetElement.css("color", "red");
            targetElement.text(texto);
      }
  }