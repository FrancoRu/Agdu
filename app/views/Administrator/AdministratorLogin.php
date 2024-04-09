<section class="container">

    <script src="js/login.js" defer></script>
    <main class="row d-flex justify-content-center align-item-center">
        <form id="form" class="col-10 d-flex flex-column align-items-center">
            <label for="action"></label>
            <input type="hidden" name="action" value="login">
            <div class="form-group p-3">
                <h2>Ingreso Administrador AGDU</h2>
            </div>

            <div class=" form-group p-3">
                <label for="username">Usuario: </label>
                <input type="text" name="username" placeholder="Ingrese su usuario" class="form-control">
            </div>

            <div class="form-group p-3">
                <label for="password">Password: </label>
                <input type="password" name="password" placeholder="Ingrese su contraseña" class="form-control">
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='warning'>{$_SESSION['error']}</p>";
            }
            ?>

            <div class="form-group col-3 p-3">
                <button type="submit" class="btn btn-primary rounded-pill">Ingresar</button>
            </div>
        </form>

    </main>
</section>