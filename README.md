Este archivo contiene la documentación de las versiones del sistema web:
Primera versión estable ver 1.0:
-Funcionalidades:
    -Conexión atraves de una API hecha en .Net Core 5 con swagger y el host de somee.com.
    -Login y Logout.
    -Registro de usuarios (clientes,vendedores y administradores).
    -Uso de sesiones: las sesiones duran 1 hora aproximadamente para cada usuario.
    -El administrador puede: listar usuarios,registrar usuarios,modificar usuarios,anular usuarios,registrar productos (solo en localhost),listar productos.
    -El cliente puede añadir productos a un carrito pero no puede guardar cambios ni pedir dichos productos.
-Validaciones:
    -Existen validaciones para los formularios de usuarios y productos.
-Librerias/addons:
    -Bootstrap 5
    -JQuery
    -Tipografías: DancingScript-VariableFont_wght de google fonts
    -Bootstrap 5 (Íconos)
    -Composer 2.5.8
    -API de google drive (para almacenar imágenes de alta calidad que se registren en productos)
    -Existe una incorporación a paypal que no se utiliza.
TODO (por hacer):
    -Incorporar el módulo de pedidos para que el administrador/vendedor puedan: listar los pedidos,
    concretar pedidos y anular pedidos.