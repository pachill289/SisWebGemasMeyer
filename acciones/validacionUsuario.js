const textoCorrecto = 'Correcto ✅';

window.onload = () => {
  var targetElement = $("small").eq(4);
  targetElement.css("fontWeight", "bold");
  targetElement.css("color", "black");
}
function validarCI(value) {
  function verificarCi(ci) {
    var regex = /^[0-9]{7,10}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
    return regex.test(ci);
    }
    var targetElement = $("small").eq(0); // Acceder al primer elemento <small>
    const texto = 'El CI debe contener al menos 7 dígitos.';
    if (verificarCi(value)) {
      targetElement.css("color", "green");
      targetElement.text(textoCorrecto);
    } else {
      targetElement.css("color", "red");
      targetElement.text(texto);
    }
}
function validarNombre(value) {
    function verificarNombre(nombre) {
        var regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{15,50}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
        return regex.test(nombre);
    }
    var targetElement = $("small").eq(1); // Acceder al primer elemento <small>
    const texto = 'El nombre debe tener mínimamente 15 caracteres, no puede exceder los 50 caracteres y solo puede utilizar letras/espacio.';
    if (verificarNombre(value)) {
      targetElement.css("color", "green");
      targetElement.text(textoCorrecto);
    } else {
      targetElement.css("color", "red");
      targetElement.text(texto);
    }
}
function validarClave(value) {
  function verificarClave(clave) {
      var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
      return regex.test(clave);
  }
  var targetElement = $("small").eq(2); // Acceder al primer elemento <small>
  const texto = 'La contraseña debe tener 8 caracteres y contener por lo menos un letra mayúscula,una letra minúscula, un número y un caracter especial.';
  if (verificarClave(value)) {
    targetElement.css("color", "green");
    targetElement.text(textoCorrecto);
  } else {
    targetElement.css("color", "red");
    targetElement.text(texto);
  }
}
function validarCorreo(value) {
  function verificarClave(clave) {
      var regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,3}$/; // Expresión regular que verifica solo letras en mayúsculas y minúsculas
      return regex.test(clave);
  }
  var targetElement = $("small").eq(3); // Acceder al primer elemento <small>
  const texto = 'El correo electrónico debe contener almenos una @ y un dominio .com .es,etc..';
  if (verificarClave(value)) {
    targetElement.css("color", "green");
    targetElement.text(textoCorrecto);
  } else {
    targetElement.css("color", "red");
    targetElement.text(texto);
  }
}
  
  
