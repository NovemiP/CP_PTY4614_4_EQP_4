<?php
session_start();

// Verificar si hay una sesión activa
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php'); // Redirigir a la página de inicio de sesión
    exit();
}



include_once '../models/inventario.php';
include_once '../models/producto.php';
include_once '../models/usuario.php';
include_once '../models/ubicacion.php';
include('../../templates/header.php');



// Obtener productos con existencias bajas
$notificacionesExistencias = Inventario::verificarExistenciasBajas();

?>

<!-- MAIN CONTENIDO -->
<main>
    <div class="p-2">
        <!-- Navbar -->
        <nav class="px-3 py-2 bg-white rounded shadow d-flex justify-content-between align-items-center">
            <!-- Botón menú para pantallas pequeñas -->
            <i class="ri-menu-line sidebar-toggle me-3 d-block d-md-none text-dark"></i>

            <!-- matener vacio -->
            <div class="d-none d-md-flex flex-grow-1 me-3">

            </div>

            <!-- Contenedor de notificaciones y usuario -->
            <div class="d-flex align-items-center">
                <!-- Notificaciones -->
                <div class="dropdown me-2">
                    <div class="cursor-pointer dropdown-toggle navbar-link" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-notification-line"></i>
                        <!-- Mostrar el número de notificaciones -->
                        <span id="notification-count" class="topbar-button-total topbar-button-total-notification">
                            <?php echo count($notificacionesExistencias); ?>
                        </span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end fx-dropdown-menu">
                        <div id="notification-list" class="list-group list-group-flush">
                            <?php if (count($notificacionesExistencias) > 0): ?>
                                <?php
                                // Agrupar notificaciones por producto
                                $notificacionesAgrupadas = [];
                                foreach ($notificacionesExistencias as $notificacion) {
                                    $nombre = $notificacion['nombre_producto'];
                                    $existencia = $notificacion['existencia_actual'];

                                    if (!isset($notificacionesAgrupadas[$nombre])) {
                                        $notificacionesAgrupadas[$nombre] = 0;
                                    }
                                    $notificacionesAgrupadas[$nombre] += $existencia;
                                }
                                ?>
                                <?php foreach ($notificacionesAgrupadas as $producto => $cantidad): ?>
                                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center notification-item" onclick="marcarComoLeida(this, '<?php echo $producto; ?>')">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center">
                                                <span class="dropdown-menu-notification-item-icon danger me-2">
                                                    <i class="ri-alert-line"></i>
                                                </span>
                                                <p class="dropdown-menu-notification-item-time mb-0">
                                                    ¡Crítico! <?php echo $producto; ?> tiene solo <?php echo $cantidad; ?> unidades.
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="dropdown-menu-notification-item-text mb-1">No hay notificaciones</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Fin notificaciones -->
                <!-- Usuario -->
                <div class="dropdown">
                    <div class="d-flex align-items-center cursor-pointer dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="me-2 d-none d-sm-block"> <?php echo $_SESSION['usuario']['nombre']; ?> </span>
                        <img class="navbar-profile-image"
                            src="../../public/img/usuario.png"
                            alt="Image">
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li class="nom-user dropdown-item"> <?php echo $_SESSION['usuario']['nombre']; ?> </li>
                        <li><a class="dropdown-item" href="vista_perfil.php">Perfil</a></li>
                        <li>
                            <form action="../controllers/logoutController.php" method="POST" style="display:inline;">
                                <input type="hidden" name="accion" value="logout">
                                <button type="submit" class="dropdown-item" style="border: none; background: none; cursor: pointer;">
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>


        <!-- Fin Navbar -->

        <!-- Contenido -->
        <h3 class="title fw-bold mt-3 mb-3 me-auto">Registrar entrada</h3>
        <!-- Formulario registro de entrada -->
        <div class="container-fluid">
            <div class="mb-3">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-4">

                        <form id="form-inventario" class="row form-registro mx-auto" action="../controllers/inventarioController.php" method="POST">
                            <!-- Producto -->
                            <div class="mb-3">
                                 <select class="form-select" id="producto_id" name="producto_id" required>
                                    <option value="" disabled selected>Seleccionar Nro Factura</option>
                                    <?php
                                    // Llama a la función que lista los productos desde el modelo
                                    $productos = Producto::listarProdEntrada();


                                    foreach ($productos as $producto) {
                                        echo '<option value="' . htmlspecialchars($producto['id_producto']) . '" 
                        data-proveedor="' . htmlspecialchars($producto['nombre_prove']) . '"
                        data-codigo="' . htmlspecialchars($producto['cod_producto']) . '" 
                        data-nombre="' . htmlspecialchars($producto['nombre_producto']) . '" 
                        data-valor="$' . number_format($producto['valor_unitario'], 0, ',', '.') . '" 
                        data-ubicacion="' . htmlspecialchars($producto['nombre_zona']) . '">'
                                            . htmlspecialchars($producto['nro_factura']) . ' - ' . htmlspecialchars($producto['nombre_producto']) .
                                            '</option>';
                                    }
                                    ?>
                                </select> 
                               
                            </div>

                            <!-- Detalles automáticos del producto -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Proveedor</label>
                                <input type="text" class="form-control" id="nombre_prove" name="nombre_prove" placeholder="Proveedor" readonly>
                            </div>


                            <div class="mb-3">
                                <label for="nombre" class="form-label">Código producto</label>
                                <input type="text" class="form-control" id="cod_producto" name="cod_producto" placeholder="Código producto" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Producto</label>
                                <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" placeholder="Producto" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Valor unitario</label>
                                <input type="text" class="form-control" id="valor_unitario" name="valor_unitario" placeholder="Valor Unitario" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">IVA</label>
                                <input type="text" class="form-control" id="iva" name="iva" placeholder="Iva (19%)" readonly>
                            </div>


                            <div class="mb-3">
                                <label for="nombre" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="existencia_inicial" name="existencia_inicial" placeholder="Ingresar cantidad" required>
                            </div>



                            <!-- Campos ocultos para manejar la acción -->
                            <input type="hidden" name="accion" value="agregarEntrada">

                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-md btn-success">Registrar</button>&nbsp;
                                <a href="vista_entradas.php" class="btn btn-md btn-success">Cerrar</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- FIN MAIN CONTENIDO -->


<?php include('../../templates/footer.php'); ?>