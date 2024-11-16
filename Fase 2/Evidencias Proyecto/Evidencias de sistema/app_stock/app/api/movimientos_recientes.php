<?php

include_once '../config/bd.php';
include_once '../models/movimiento.php';

try {
    // llamo al metodo para obtener el listado de movimientos
    $movimientos = Movimiento::listarMovimientosCard();

    header('Content-Type: application/json');
    
    // convierte el array en formato JSON y lo devuelve al frontend
    echo json_encode($movimientos);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>