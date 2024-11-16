<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

include_once __DIR__ . '/../app/config/bd.php';
require_once __DIR__ . '/../app/models/inventario.php';

class InventarioTest extends TestCase
{
    /** @var PDO|MockObject */
    private $conexionBDMock;

    /** @var Inventario */
    private $inventario;

    protected function setUp(): void
    {
        // Creamos un mock de la clase PDO
        $this->conexionBDMock = $this->createMock(PDO::class);

        // Usamos el mock de la conexión en el modelo Inventario
        $this->inventario = new Inventario($this->conexionBDMock);
    }

    // Probar el método agregarEntrada con mock
    public function testAgregarEntrada()
    {
        // Simulamos los valores para la entrada
        $existencia_inicial = 10;
        $fecha = '2024-11-13';
        $registrado_por = 'admin';
        $usuario_id = 1;
        $producto_id = 1;

        // Simulamos el valor unitario del producto en la base de datos (mock)
        $producto = ['valor_unitario' => 100];

        // Mock de la consulta a la base de datos
        $this->conexionBDMock->expects($this->once())
                             ->method('prepare')
                             ->willReturn($this->createMock(PDOStatement::class));
        
        // Definir un comportamiento simulado para el método execute()
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true); // Simulamos que la ejecución fue exitosa

        $this->conexionBDMock->method('prepare')
                             ->willReturn($stmtMock);
        
        // Llamamos al método a probar
        $resultado = $this->inventario->agregarEntrada($existencia_inicial, $fecha, $registrado_por, $usuario_id, $producto_id);

        // Verificamos que el resultado sea verdadero (indica éxito)
        $this->assertTrue($resultado);
    }

    // Probar el método borrarEntrada con mock
    public function testBorrarEntrada()
    {
        $id = 1;

        // Simulamos que no hay salidas asociadas con la entrada
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);

        $stmtMock->expects($this->once())
                 ->method('fetch')
                 ->willReturn(['total' => 0]); // Simulamos que no hay salidas

        $this->conexionBDMock->method('prepare')
                             ->willReturn($stmtMock);

        // Llamamos al método a probar
        $resultado = $this->inventario->borrarEntrada($id);

        // Verificamos que la entrada fue eliminada correctamente
        $this->assertTrue($resultado);
    }

    // Probar listarInventarios con mock
    public function testListarInventarios()
    {
        $limit = 10;
        $offset = 0;

        // Simulamos el resultado de listarInventarios
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);
        
        // Simulamos que el método fetchAll retorna un array no vacío
        $stmtMock->expects($this->once())
                 ->method('fetchAll')
                 ->willReturn([['id' => 1, 'producto_id' => 1, 'existencia' => 100]]);

        $this->conexionBDMock->method('prepare')
                             ->willReturn($stmtMock);

        // Llamamos al método a probar
        $resultado = $this->inventario->listarInventarios($limit, $offset);

        // Verificamos que el resultado no esté vacío
        $this->assertNotEmpty($resultado);
        $this->assertGreaterThan(0, count($resultado)); // Verificamos que hay al menos un inventario
    }

    // Limpiar la base de datos después de cada prueba
    protected function tearDown(): void
    {
        // Limpiar cualquier dato temporal si es necesario
    }
}

?>


