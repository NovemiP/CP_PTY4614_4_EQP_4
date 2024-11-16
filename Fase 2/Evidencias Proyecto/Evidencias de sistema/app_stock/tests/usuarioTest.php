<?php

use PHPUnit\Framework\TestCase;
include_once '../config/bd.php';
include_once '../models/Usuario.php';

class UsuarioTest extends TestCase
{
    
    protected $usuario;

    protected function setUp(): void
    {
        
        $this->usuario = new Usuario();
    }

    
    public function testAutenticarUsuarioExitoso()
    {
        
        $correo = "test@correo.com";
        $contrasena = "contraseñaCorrecta";

        $resultado = Usuario::autenticarUsuario($correo, $contrasena);

        $this->assertTrue($resultado);  
    }

    public function testAutenticarUsuarioNoExiste()
    {
        // Usuario que no existe en la base de datos
        $correo = "usuarioInexistente@correo.com";
        $contrasena = "contrasena";

        $resultado = Usuario::autenticarUsuario($correo, $contrasena);

        $this->assertEquals("El usuario ingresado no existe.", $resultado);
    }

    
    public function testCambiarEstadoUsuario()
    {
        $id = 1;  
        $nuevoEstado = "Inactivo";

        $resultado = Usuario::cambiarEstadoUsuario($id, $nuevoEstado);

        $this->assertTrue($resultado);  
    }

    // Prueba para el método 'agregarUsuario'
    public function testAgregarUsuario()
    {
        // Datos del nuevo usuario
        $nombre = "Nuevo";
        $apellido = "Usuario";
        $correo = "nuevo@correo.com";
        $rol = "Cliente";
        $contrasena = "contraseña";

        // Se espera que el nuevo usuario sea agregado correctamente
        $resultado = Usuario::agregarUsuario($nombre, $apellido, $correo, $rol, $contrasena);

        $this->assertTrue($resultado);  
    }

    
    public function testObtenerRolPorCorreo()
    {
        $correo = "test@correo.com";  

        $resultado = Usuario::obtenerRolPorCorreo($correo);

        $this->assertEquals("Cliente", $resultado); 
    }

    // Prueba para editar un usuario
    public function testEditarUsuario()
    {
        $id = 1;  
        $nombre = "Usuario Editado";
        $apellido = "Apellido Editado";
        $correo = "editado@correo.com";
        $rol = "Admin";
        $contrasena = "nuevaContraseña";

       
        Usuario::editarUsuario($id, $nombre, $apellido, $correo, $rol, $contrasena);

        
        $usuario = Usuario::obtenerUsuarioPorId($id);
        $this->assertEquals("Usuario Editado", $usuario['nombre']);
        $this->assertEquals("Apellido Editado", $usuario['apellido']);
    }

    
    public function testEliminarUsuario()
    {
        $id = 2;  

        $resultado = Usuario::borrarUsuario($id);

        $this->assertTrue($resultado);  
    }

    
    public function testListarUsuarios()
    {
        $limit = 5;
        $offset = 0;

        $usuarios = Usuario::listarUsuarios($limit, $offset);

        $this->assertIsArray($usuarios);  
        $this->assertCount($limit, $usuarios);  
    }

    // Prueba para la paginación de usuarios
    public function testUsuariosPaginacion()
    {
        $totalUsuarios = Usuario::UsuariosPaginacion();

        $this->assertGreaterThan(0, $totalUsuarios);  
    }
}
