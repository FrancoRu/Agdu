<section>
    <div class="container mt-4">
        <form id="form" enctype="multipart/form-data">
            <label for="action"></label>
            <input type="hidden" name="action" value="upload">
            <div class="row form-section info-display">
                <div class="row">
                    <div class="div-title-form">
                        <p>
                            Datos cuenta bancaria:
                        </p>
                    </div>
                </div>

                <div class="row d-flex justify-content-end p-2">
                    <div class="col-md-8 d-flex justify-content-end">
                        <p id="CUIL"> CUIL beneficiario : <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?></p>
                    </div>
                </div>

                <div class=" row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="apellido" class="info-input">Apellido*</label>
                            <input type="text" class="form-control" name="lastname" placeholder="Apellido" pattern="^[\p{L}]+(?: [\p{L}]+)*$" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nombre" class="info-input">Nombre*</label>
                            <input type="text" class="form-control" name="username" placeholder="Nombre" pattern="^[\p{L}]+(?: [\p{L}]+)*$" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="banco" class="info-input">CBU / CVU / Alias*</label>
                            <input type="text" class="form-control" name="banco" placeholder="Ingrese su CBU / CVU / Alias" pattern="^[0-9]*|[\p{L}]+(?:[\p{L}0-9]*\.[\p{L}0-9]*)*$" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tipoCuenta" class="info-input">Tipo de Cuenta*</label>
                            <select name="tipoCuenta" class="form-control" required>
                                <option value="0">Cuenta corriente</option>
                                <option value="1">Caja de ahorro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email" class="info-input">Correo electrónico*</label>
                            <input type="email" class="form-control" name="email" placeholder="Correo electrónico" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-section info-display mt-3">
                <div class="row">
                    <div class="div-title-form ">
                        <p>
                            Información sobre los Hijos:
                        </p>
                    </div>
                </div>
                <div id="rootChildren">
                </div>
            </div>
            <div class="row form-section d-felx justify-content-end pt-2">
                <button type="submit" class="btn btn-primary rounded-pill col-sm-6 col-md-4 col-xl-1">Enviar</button>
            </div>
        </form>
    </div>
</section>
<script src="js/chargeInfo.js"></script>