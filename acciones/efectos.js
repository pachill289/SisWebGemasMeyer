var condicionClave = false;
document.addEventListener('DOMContentLoaded', function() {
    $("#iconoClave2").hide();
});
//Tiempo de carga de la página actual
var startTime = new Date().getTime();
window.addEventListener('load', function() {
  var endTime = new Date().getTime();
  var loadTime = endTime - startTime;
  //Si página tarda mas de 2 segundos en cargar sale un mensaje de recomendación
  if(loadTime >= 3500)
  {
    alert('Vaya 🤔, esta página tardo mas de lo previsto, es importante que no recargue varias veces la página si esta tardo mucho tiempo ya que esto puede afectar el rendimiento de nuestros servicios.');
  }
  console.log('La página tardó ' + loadTime/1000 + ' segundos en cargar.');
});
//funcionalidad para mostrar y ocultar una contraseña
function mostrarClave (input)
{
    condicionClave == false? condicionClave = true : condicionClave = false;
    if(condicionClave)
    {
        $("#clave").prop("type", "text");
        $("#iconoClave").hide();
        $("#iconoClave2").show();
    } 
    else
    {
        $("#clave").prop("type", "password");
        $("#iconoClave2").hide();
        $("#iconoClave").show();
    } 
}
function valorRange (valor) {
    $("#inputPrecioMin").val(valor);
}
function valorInput (valor) {
    if(valor>14000)
    {
        $("#inputPrecioMin").val(14000);
        $("#customRange1").val(valor);
    }
    else if(valor<304){
        $("#inputPrecioMin").val(304);
        $("#customRange1").val(valor);
    }
}
function valorRange2 (valor) {
    $("#inputPrecioMax").val(valor);
}
function valorInput2 (valor) {
    if(valor>14000)
    {
        $("#inputPrecioMax").val(14000);
        $("#customRange2").val(valor);
    }
    else if(valor<577){
        $("#inputPrecioMax").val(577);
        $("#customRange2").val(valor);
    }
}

