<?php

require_once('tcpdf/tcpdf.php');


class GuiaSalidaPDF extends TCPDF
{
    public function Header()
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Orden de salida de bodega', 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Obtener el nro de guia seleccionado del formulario
$nro_guia_seleccionado = $_GET['nro_orden'] ?? null;

if ($nro_guia_seleccionado) {
    // Crear instancia del PDF
    $pdf = new GuiaSalidaPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Guía de salida de bodega');
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();

    // Conexion con la bd y consulta para la guia de traslado
    try {
        $conexionBD = new PDO('mysql:host=localhost;dbname=stock_control', 'root', '');
        $sql = "SELECT 
                    nro_orden, 
                    producto.cod_producto, 
                    producto.nombre_producto, 
                    detalle_orden.cantidad, 
                    detalle_orden.cantidad * producto.valor_unitario as Total,
                    proveedor.nombre_prove, 
                    cliente.nombre AS Cliente, 
                    orden_salida.destino AS Direccion, 
                    orden_salida.fecha_emision AS Fecha
                FROM orden_salida
                LEFT JOIN cliente ON orden_salida.cliente_id_cliente = cliente.id_cliente
                LEFT JOIN inventario ON orden_salida.inventario_id_inventario = inventario.id_inventario
                LEFT JOIN producto ON inventario.producto_id_producto = producto.id_producto
                LEFT JOIN detalle_orden ON detalle_orden.orden_salida_id_orden_salida = orden_salida.id_orden_salida
                LEFT JOIN proveedor ON inventario.producto_id_producto = producto.id_producto
                WHERE nro_orden = :nro_orden
                GROUP BY nro_orden";

        $stmt = $conexionBD->prepare($sql);
        $stmt->bindParam(':nro_orden', $nro_guia_seleccionado);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($datos) {
            $pdf->SetFont('helvetica', '', 10);
            // Información de la orden
            $pdf->Cell(30, 10, 'Nro. de Orden:', 0, 0, 'L');
            $pdf->Cell(40, 10, $datos[0]['nro_orden'], 0, 1, 'L');
            $pdf->Cell(30, 10, 'Proveedor:', 0, 0, 'L');
            $pdf->Cell(40, 10, $datos[0]['nombre_prove'], 0, 1, 'L');
            $pdf->Cell(30, 10, 'Destino:', 0, 0, 'L');
            $pdf->Cell(40, 10, $datos[0]['Cliente'], 0, 1, 'L');
            $pdf->Cell(30, 10, 'Dirección destino:', 0, 0, 'L');
            $pdf->Cell(40, 10, $datos[0]['Direccion'], 0, 1, 'L');
            $pdf->Cell(30, 10, 'Fecha de Emisión:', 0, 0, 'L');
            $pdf->Cell(40, 10, $datos[0]['Fecha'], 0, 1, 'L');
            $pdf->Ln(10);

            // Encabezado de la tabla de productos
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(30, 10, 'Código', 1, 0, 'C');
            $pdf->Cell(70, 10, 'Producto', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Total', 1, 1, 'C');
            $pdf->SetFont('helvetica', '', 10);

            $subtotal = 0;
            $iva = 0;
            $iva_porcentaje = 0.19;

            // Datos de la tabla de productos
            foreach ($datos as $fila) {
                $total_producto = $fila['Total'];
                $subtotal += $total_producto;

                $pdf->Cell(30, 10, $fila['cod_producto'], 1, 0, 'C');
                $pdf->Cell(70, 10, $fila['nombre_producto'], 1, 0, 'C');
                $pdf->Cell(30, 10, $fila['cantidad'], 1, 0, 'C');
                $pdf->Cell(30, 10, number_format($fila['Total'], 2), 1, 1, 'C');
            }
            // Calcular el IVA y el total
            $iva = $subtotal * $iva_porcentaje;
            $total_con_iva = $subtotal + $iva;

            // Mostrar el subtotal, el IVA y el total con IVA
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(155, 10, 'Subtotal:', 0, 0, 'R');
            $pdf->Cell(30, 10, number_format($subtotal, 0, '', '.'), 1, 1, 'R');
            $pdf->Cell(155, 10, 'IVA (19%):', 0, 0, 'R');
            $pdf->Cell(30, 10, number_format($iva, 0, '', '.'), 1, 1, 'R');
            $pdf->Cell(155, 10, 'Total con IVA:', 0, 0, 'R');
            $pdf->Cell(30, 10, number_format($total_con_iva, 0, '', '.'), 1, 1, 'R');
        } else {
            $pdf->Cell(0, 10, 'No se encontraron datos para la orden de salida.', 0, 1, 'C');
        }

        // Descargar el PDF
        $pdf->Output('Orden de salida__' . $nro_guia_seleccionado . '.pdf', 'D');
    } catch (PDOException $e) {
        echo "Error en la conexión: " . $e->getMessage();
    }
} else {
    echo "No se ha seleccionado un número de guía válido.";
}
