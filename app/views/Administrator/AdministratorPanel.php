<?php
require_once '../app/controllers/AdministratorController.php';
?>
<section class="container d-flex row justify-content-center">
    <nav class="navbar col-10 m-1">
        <form class="container-fluid justify-content-start">
            <button id="btn_download" class="btn btn-outline-primary me-2">Descargar</button>
            <button id="btn_logout" class="btn btn-outline-danger me-2">Cerrar Sesión</button>
        </form>
        <h1>
            Información Solicitante
        </h1>
    </nav>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<p class='warning'>'{$_SESSION['error']}'</p>";
    }
    $result = AdministratorController::readBeneficiary();
    if ($result === false) {
        $result = '<p>Datos no encontrados</p>';
    }
    echo $result;

    ?>
    <!-- <div class="p-3 d-flex row col-2 btn-contain">
        <div class="p-3 btn">
            <button id="btn_download" class="btn btn-primary">Descargar</button>
        </div>
        <div class="p-3 btn">
            <button id="btn_logout" class="btn btn-primary">Cerrar Sesión</button>
        </div>
    </div> -->
</section>
<script src="js/panel.js"></script>
<script src="js/logout.js"></script>
<link href="resources/styles/administrator.css" rel="stylesheet">