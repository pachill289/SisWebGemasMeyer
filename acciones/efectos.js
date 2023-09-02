var condicionClave = false;
window.onload = () =>
{
    $("#iconoClave2").hide();
}
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

