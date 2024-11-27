<?php

//las ubicaciones vienen precargadas en la base de datos


include_once '../models/ubicacion.php'; 


class UbicacionController {

    // metodo listar ubicaciones
    public function listarUbicaciones() {
        $listaUbicaciones = Ubicacion::listarUbicaciones(); 
        return $listaUbicaciones; // devuelte la lista
    }
}

// crea una nueva instancia de ubicacionController
$ubicacionController = new UbicacionController();

// Llama al metodo para obtener las ubicaciones
$listaUbicaciones = $ubicacionController->listarUbicaciones(); 
?>
