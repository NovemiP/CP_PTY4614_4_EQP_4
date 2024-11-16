<?php
session_start();

// Verificar si hay una sesión activa y si el rol es "Administrador"
if (!isset($_SESSION['usuario']['rol']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
    header('Location: ../views/login.php'); // Redirigir a la página de inicio de sesión
    exit();
}

include_once('../models/cliente.php');
include('../models/inventario.php');
if (isset($_GET['id_cliente'])) {
    $id_cliente = filter_input(INPUT_GET, 'id_cliente', FILTER_SANITIZE_NUMBER_INT);
    $cliente = Cliente::obtenerClientePorId($id_cliente);

    if (!$cliente) {
        echo "Error: No se encontraron los datos del cliente.";
        exit();
    }
} else {
    echo "Error: ID del client no especificado.";
    exit();
}
include('../../templates/header.php'); 

// Obtener productos con existencias bajas
$notificacionesExistencias = Inventario::verificarExistenciasBajas();

?>



<!--MAIN CONTENIDO-->
<main>
    <div class="p-2">

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


        <!--Contenido-->
        <h3 class="title fw-bold mt-3 mb-3 me-auto">Editar cliente</h3>
        <!-- formulario ingresar proveedor-->
        <div class="container-fluid">
            <div class="mb-3">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-4">

                        <form class="row form-registro mx-auto" action="../controllers/clienteController.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($cliente['id_cliente']); ?>">

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre cliente</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($cliente['ciudad']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" pattern="[0-9]{8}" title="Debe contener 8 dígitos" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="contacto" class="form-label">Contacto</label>
                                <input type="text" class="form-control" id="contacto" name="contacto" value="<?php echo htmlspecialchars($cliente['contacto']); ?>" required>
                            </div>

                            <input type="hidden" name="accion" value="editar">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-md btn-success">Guardar cambios</button>&nbsp;
                                <a href="vista_clientes.php" class="btn btn-md btn-secondary">Cerrar</a>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
        <!--fin del contenido-->
    </div>
</main>
<!--FIN MAIN CONTENIDO-->


<?php include('../../templates/footer.php'); ?>