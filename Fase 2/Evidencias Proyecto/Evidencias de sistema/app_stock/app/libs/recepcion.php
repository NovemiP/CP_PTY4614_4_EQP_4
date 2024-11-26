<?php

require_once('tcpdf/tcpdf.php');

// clase para el pdf
class RecepcionPDF extends TCPDF
{
    public function Header()
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Comprobante de recepción', 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Obtener el número de factura seleccionado del formulario
$nro_recepcion_seleccionado = $_GET['nro_recepcion'] ?? null;

if ($nro_recepcion_seleccionado) {
    // Crear instancia del PDF
    $pdf = new RecepcionPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Comprobante de recepción');
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();

    // Conexión con la bd y consulta con los datos para el comprobante de recepcion
    try {
        $conexionBD = new PDO('mysql:host=localhost;dbname=stock_control', 'root', '');
        $sql = "SELECT nro_recepcion,
                       proveedor.nombre_prove,
                       producto.cod_producto, 
                       producto.nombre_producto, 
                       inventario.existencia_inicial ,
                       inventario.existencia_actual , 
                       producto.valor_unitario,
                       total_facturado as Total, 
                       fecha_emision, 
                       inventario.registrado_por as Responsable
                FROM recepcion
                LEFT JOIN inventario ON recepcion.inventario_id_inventario = inventario.id_inventario
                LEFT JOIN producto ON inventario.producto_id_producto = producto.id_producto
                LEFT JOIN proveedor ON inventario.producto_id_producto = producto.id_producto
                WHERE nro_recepcion = :nro_recepcion
                GROUP BY nro_recepcion";
        $stmt = $conexionBD->prepare($sql);
        $stmt->bindParam(':nro_recepcion', $nro_recepcion_seleccionado);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($datos) {
            $pdf->SetFont('helvetica', '', 10);
            // info del comprobante de recepción
            $pdf->Cell(35, 10, 'Nro. de recepción:', 0, 0, 'L');
            $pdf->Cell(60, 10, $datos[0]['nro_recepcion'], 0, 1, 'L');
            $pdf->Cell(30, 10, 'Proveedor:', 0, 0, 'L');
            $pdf->Cell(60, 10, $datos[0]['nombre_prove'], 0, 1, 'L');
            $pdf->Cell(30, 10, 'Fecha de Emisión:', 0, 0, 'L');
            $pdf->Cell(60, 10, $datos[0]['fecha_emision'], 0, 1, 'L');
            $pdf->Cell(40, 10, 'Responsable recepción:', 0, 0, 'L');
            $pdf->Cell(60, 10, $datos[0]['Responsable'], 0, 1, 'L');
            $pdf->Ln(10);

            // encabezado de la tabla para el producto
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(30, 10, 'Código', 1, 0, 'C');
            $pdf->Cell(35, 10, 'Producto', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Cantidad inicial', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Cantidad actual', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Valor Unitario', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Total', 1, 1, 'C');

            $pdf->SetFont('helvetica', '', 10);

            // Variables para calcular el total y el IVA
            $subtotal = 0;
            $iva = 0;
            $iva_porcentaje = 0.19; // IVA del 19%

            // Filas de productos
            foreach ($datos as $fila) {
                $total_producto = $fila['existencia_actual'] * $fila['valor_unitario'];
                $subtotal += $total_producto;
                
                $pdf->Cell(30, 10, $fila['cod_producto'], 1, 0, 'C');
                $pdf->Cell(35, 10, $fila['nombre_producto'], 1, 0, 'C');
                $pdf->Cell(30, 10, $fila['existencia_inicial'], 1, 0, 'C');
                $pdf->Cell(30, 10, $fila['existencia_actual'], 1, 0, 'C');
                $pdf->Cell(30, 10, number_format($fila['valor_unitario'], 0), 1, 0, 'C');
                $pdf->Cell(30, 10, number_format($total_producto, 0), 1, 1, 'C');
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
            $pdf->Cell(0, 10, 'No se encontraron datos para la recepción.', 0, 1, 'C');
        }

        // Descargar el PDF
        $pdf->Output('Comprobante de recepción__' . $nro_recepcion_seleccionado . '.pdf', 'D');
    } catch (PDOException $e) {
        echo "Error en la conexión: " . $e->getMessage();
    }
} else {
    echo "No se ha seleccionado un número de recepción válido.";
}

?>
