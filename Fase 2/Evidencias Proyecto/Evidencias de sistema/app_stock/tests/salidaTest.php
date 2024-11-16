<?php

use PHPUnit\Framework\TestCase;

class SalidaTest extends TestCase
{
    
    protected $mockDB;

    protected function setUp(): void
    {
        $this->mockDB = $this->getMockBuilder('BD')
                              ->setMethods(['crearInstancia'])
                              ->getMock();
    }

    public function testRegistrarSalidaExito()
    {
    
        $inventario_id = 1;
        $cliente_id = 1;
        $cantidad_salida = 10;
        $fecha_salida = '2024-11-16';
        $registrado_por = 1;

      
        $this->mockDB->expects($this->once())
                     ->method('crearInstancia')
                     ->willReturn(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

        $resultado = Salida::registrarSalida($inventario_id, $cliente_id, $cantidad_salida, $fecha_salida, $registrado_por);
        $this->assertTrue($resultado);
    }

    public function testRegistrarSalidaSinExistencia()
    {
        // Datos de prueba
        $inventario_id = 1;
        $cliente_id = 1;
        $cantidad_salida = 1000;  
        $fecha_salida = '2024-11-16';
        $registrado_por = 1;

        
        $this->mockDB->expects($this->once())
                     ->method('crearInstancia')
                     ->willReturn(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

        
        $resultado = Salida::registrarSalida($inventario_id, $cliente_id, $cantidad_salida, $fecha_salida, $registrado_por);
        $this->assertFalse($resultado);
    }

  
    public function testRegistrarSalidaCantidadNegativa()
    {
        // Datos de prueba
        $inventario_id = 1;
        $cliente_id = 1;
        $cantidad_salida = -5;  
        $fecha_salida = '2024-11-16';
        $registrado_por = 1;

        
        $this->mockDB->expects($this->once())
                     ->method('crearInstancia')
                     ->willReturn(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

        // Probamos la salida con cantidad negativa
        $resultado = Salida::registrarSalida($inventario_id, $cliente_id, $cantidad_salida, $fecha_salida, $registrado_por);
        $this->assertFalse($resultado);
    }

    
    public function testRegistrarSalidaInventarioNoEncontrado()
    {
        // Datos de prueba
        $inventario_id = 999;  
        $cliente_id = 1;
        $cantidad_salida = 10;
        $fecha_salida = '2024-11-16';
        $registrado_por = 1;

        
        $this->mockDB->expects($this->once())
                     ->method('crearInstancia')
                     ->willReturn(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

        
        $resultado = Salida::registrarSalida($inventario_id, $cliente_id, $cantidad_salida, $fecha_salida, $registrado_por);
        $this->assertFalse($resultado);
    }
}
?>
