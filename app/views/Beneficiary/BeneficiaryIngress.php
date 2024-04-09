<section class="container">
    <main class="row d-flex justify-content-center align-item-center">
        <div class="div-title-form col-9">
            <p>Ayuda escolar Ciclo lectivo 2024</p>
        </div>
        <form id="form" class="col-10 d-flex row justify-content-center">
            <label for="action"></label>
            <input type="hidden" name="action" value="home">
            <div class="form-group">
                <label for="CUIL" class="visually-hidden">CUIL</label>
                <div class="row">
                    <div class="col d-flex justify-content-center" id="text-CUIL">
                        <p class="mb-0"><strong>CUIL del afiliado/a: </strong></p>
                    </div>
                    <div class="col">
                        <input type="text" name="user_id" class="form-control" id="CUIL" pattern="[1-9][0-9]*" minlength="11" maxlength="11" placeholder="Ingrese CUIL" aria-describedby="cuilHelp" required>
                        <div id="cuilHelp" class="form-text">Ingrese su CUIL (11 dígitos sin guiones)</div>
                        <?php
                        if (isset($_SESSION['error'])) {
                            echo "<p class='warning'>{$_SESSION['error']}</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group  col-3">
                <button type=" submit" class="btn btn-primary rounded-pill">Ingresar</button>
            </div>
        </form>
    </main>
</section>
<footer class="d-flex flex-column align-items-center" id="footer-ingress">
    <div id="content-footer">
        <div class="d-flex justify-content-left div-title-form col-10 mb-0">
            <p class="footer-text">
                Pasos para Solicitar:
            </p>
        </div>
        <div class="d-flex justify-content-center col-10 mb-0">
            <ul class="footer-text">
                <li>Complete el formulario de solicitud disponible en nuestra página web.</li>
                <li>Por cada hijo para el cual solicita el subsidio, ingrese únicamente el nombre de pila.</li>
                <li>Adjunte el certificado de alumno regular correspondiente a cada estudiante. Asegúrese de que los documentos estén en formato aceptado (PDF, JPEG, etc.).</li>
            </ul>
        </div>
        <div class="d-flex justify-content-center col-10">
            <p class="warning footer-text">
                Importante: Todos los campos del formulario deben ser llenados para facilitar el proceso de evaluación. La información proporcionada es crucial para verificar la elegibilidad y garantizar una distribución equitativa del subsidio.
            </p>
        </div>
    </div>
</footer>


<script src="js/ingress.js"></script>