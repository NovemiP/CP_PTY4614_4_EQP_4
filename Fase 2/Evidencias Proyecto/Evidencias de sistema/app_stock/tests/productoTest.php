<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

include_once __DIR__ . '/../app/config/bd.php';
require_once __DIR__ . '/../app/models/producto.php';

class ProductoTest extends TestCase
{
    protected $conexionBD;
    protected $producto;

    protected function setUp(): void
    {
        // Crear mock de la base de datos
        $this->conexionBD = $this->createMock(PDO::class);
        
        // Crear una instancia del modelo Producto
        $this->producto = new Producto($this->conexionBD); // Asegúrate de pasar el mock al constructor
    }

    public function testAgregarProducto()
    {
        // Datos de prueba para agregar un producto
        $nombre_producto = "Producto Test";
        $unidad_medida = "und.";
        $valor = 1000;
        $fecha_registro_prod = date("Y-m-d");
        $proveedor_id = 1;  
        $categoria_id = 1;   
        $ubicacion_id = 1;

        // Crear mock para el statement
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->willReturn(true); // Simulamos que la inserción fue exitosa

        // Simulamos que prepare devuelve el mock del statement
        $this->conexionBD->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Ejecutar el método de agregar producto
        $resultado = $this->producto->agregarProducto($nombre_producto, $unidad_medida, $valor, $fecha_registro_prod, $proveedor_id, $categoria_id, $ubicacion_id);

        // Verifica que la inserción se haya realizado correctamente
        $this->assertTrue($resultado);
    }

    public function testEditarProducto()
    {
        // Datos para editar
        $producto_id = 1; // Asumiendo que el producto con ID 1 existe
        $nombre_producto = "Producto Editado";
        $unidad_medida = "kg";
        $valor = 1200;
        $fecha_registro_prod = date("Y-m-d");
        $proveedor_id = 1;
        $categoria_id = 1;
        $ubicacion_id = 1;

        // Crear mock para el statement
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->willReturn(true); // Simulamos que la edición fue exitosa

        // Simulamos que prepare devuelve el mock del statement
        $this->conexionBD->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Ejecutar el método de editar producto
        $resultado = $this->producto->editarProducto($producto_id, $nombre_producto, $unidad_medida, $valor, $fecha_registro_prod, $proveedor_id, $categoria_id, $ubicacion_id);

        $this->assertTrue($resultado);
    }

    public function testEliminarProducto()
    {
        // Asumiendo que el producto con ID 1 existe
        $producto_id = 1;

        // Crear mock para el statement
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->willReturn(true); // Simulamos que la eliminación fue exitosa

        // Simulamos que prepare devuelve el mock del statement
        $this->conexionBD->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Ejecutar el método de eliminar producto
        $resultado = $this->producto->eliminarProducto($producto_id);
        
        $this->assertTrue($resultado);
    }

    public function testListarProductos()
    {
        // Simulamos que la consulta de productos retorna algunos resultados
        $productosMock = [['id' => 1, 'nombre' => 'Producto Test']];
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn($productosMock);

        $this->conexionBD->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Ejecutamos el método para listar productos
        $productos = $this->producto->listarProductos();

        // Verifica que la lista no esté vacía
        $this->assertNotEmpty($productos);
    }

    public function testBuscarProductoPorId()
    {
        $producto_id = 1;
        
        // Simulamos la búsqueda de un producto
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => $producto_id, 'nombre' => 'Producto Test']);

        $this->conexionBD->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $producto = $this->producto->buscarProductoPorId($producto_id);

        // Verifica que el producto no sea null y que el ID coincida
        $this->assertNotNull($producto);
        $this->assertEquals($producto_id, $producto['id']);
    }

    public function testBuscarProductoPorNombre()
    {
        $nombre_producto = "Producto Test";
        
        // Simulamos que se encuentra un producto con el nombre proporcionado
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([['id' => 1, 'nombre' => $nombre_producto]]);

        $this->conexionBD->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $productos = $this->producto->buscarProductoPorNombre($nombre_producto);

        // Verifica que el producto se encuentre
        $this->assertNotEmpty($productos);
        $this->assertEquals($nombre_producto, $productos[0]['nombre']);
    }
}
?>


?>