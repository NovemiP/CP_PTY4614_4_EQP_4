<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/../app/config/bd.php';
require_once __DIR__ . '/../app/models/inventario.php';

class InventarioTest extends TestCase
{
    /** @var PDO|MockObject */
    private $conexionBDMock;

    /** @var Inventario */
    private $inventario;

    protected function setUp(): void
    {
        //crear mock
        $this->conexionBDMock = $this->createMock(PDO::class);

        // inicializar el modelo inventario con el mock de PDO
        $this->inventario = new Inventario($this->conexionBDMock);
    }

    public function testAgregarEntrada()
    {
        $existencia_inicial = 10;
        $fecha = '2024-11-13';
        $registrado_por = 'admin';
        $usuario_id = 1;
        $producto_id = 1;

       
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true); // Simulamos éxito al ejecutar

       
        $this->conexionBDMock->method('prepare')
                             ->willReturn($stmtMock);

        $resultado = $this->inventario->agregarEntrada($existencia_inicial, $fecha, $registrado_por, $usuario_id, $producto_id);

       
        $this->assertTrue($resultado);
    }

    
    public function testBorrarEntrada()
    {
        $id = 1;

        // Mock de PDOStatement
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->exactly(2))
                 ->method('execute')
                 ->willReturn(true);

        $stmtMock->expects($this->once())
                 ->method('fetch')
                 ->willReturn(['total' => 0]); 

     
        $this->conexionBDMock->method('prepare')
                             ->willReturn($stmtMock);

       
        $resultado = $this->inventario->borrarEntrada($id);

       
        $this->assertTrue($resultado);
    }

    
    public function testListarInventarios()
    {
        $limit = 10;
        $offset = 0;

        // Mock de PDOStatement
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);

        $stmtMock->expects($this->once())
                 ->method('fetchAll')
                 ->willReturn([
                     ['id' => 1, 'producto_id' => 1, 'existencia' => 100]
                 ]); 

        // Mock de prepare()
        $this->conexionBDMock->method('prepare')
                             ->willReturn($stmtMock);

        // Llamar al método del modelo
        $resultado = $this->inventario->listarInventarios($limit, $offset);

        // Comprobar que el resultado no esté vacío
        $this->assertNotEmpty($resultado);
        $this->assertGreaterThan(0, count($resultado)); 
    }

    protected function tearDown(): void
    {
       
    }
}

?>
