<?php

use PHPUnit\Framework\TestCase;

require_once '../config/bd.php';
require_once '../models/Producto.php';

class ProductoTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = Producto::crearInstancia();
        $this->assertNotNull($this->db, "La conexión a la base de datos no debe ser nula.");
    }

    public function testListarProductos()
    {
        $productos = Producto::listarProductos(10, 0);
        $this->assertIsArray($productos, "El resultado debe ser un array.");
        $this->assertGreaterThanOrEqual(0, count($productos), "El array puede estar vacío pero no debe lanzar errores.");
    }

    public function testAgregarProducto()
    {
        $resultado = Producto::agregarProducto(
            "Producto Test",
            "Unidad",
            100.50,
            date("Y-m-d"),
            1, 
            1, 
            1   
        );
        $this->assertTrue($resultado, "Debe retornar true si el producto se agrega correctamente.");
    }

    public function testEditarProducto()
    {
        $producto = Producto::obtenerProductoPorId(1); 
        $this->assertNotNull($producto, "El producto con ID 1 debe existir para esta prueba.");

        $resultado = Producto::editarProducto(
            $producto['id_producto'],
            "Producto Modificado",
            "Unidad Modificada",
            200.75,
            date("Y-m-d"),
            1, 
            1, 
            1  
        );
        $this->assertTrue($resultado, "Debe retornar true si el producto se edita correctamente.");
    }

    public function testBorrarProducto()
    {
        $producto = Producto::obtenerProductoPorId(1); 
        $this->assertNotNull($producto, "El producto con ID 1 debe existir para esta prueba.");

        $resultado = Producto::borrarProducto($producto['id_producto']);
        $this->assertTrue($resultado, "Debe retornar true si el producto se elimina correctamente.");
    }

    public function testContarProductos()
    {
        $total = Producto::contarProductos();
        $this->assertIsInt($total, "El total de productos debe ser un entero.");
        $this->assertGreaterThanOrEqual(0, $total, "El total debe ser igual o mayor a 0.");
    }

    

    protected function tearDown(): void
    {
        $this->db = null;
    }
}
