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
