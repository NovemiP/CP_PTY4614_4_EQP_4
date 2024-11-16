<?php
// Iniciar la sesión para acceso a variables de sesion
session_start();


include_once '../models/usuario.php';

//verifica si hay un usuario logueado y redirigir al inicio
if (isset($_SESSION['usuario'])) {
    header('Location: ../views/inicio.php'); 
    exit(); 
}

//verifica si el formulario se envio usando post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //sanitizar correo, cambiar a minuscula y eliminar caracteres especiales
    $correo = filter_var(strtolower($_POST['correo']), FILTER_SANITIZE_EMAIL);
    //obtiene la contraseña ingresada sin encriptacion
    $contrasena = $_POST['contrasena'];

    //llamo al metodo para verificar las credenciales
    $resultadoAutenticacion = Usuario::autenticarUsuario($correo, $contrasena);

    //verificar autenticacion
    if ($resultadoAutenticacion === true) {
        header('Location: ../views/inicio.php');
        exit();
    } else {
        
        
        $mensajeError = $resultadoAutenticacion;
    }
}

//vista del login
require '../views/login.php'; 

?>
