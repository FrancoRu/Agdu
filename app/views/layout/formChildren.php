<div class="row" id="rowChildren-<?php echo $_GET['index'] ?>">
  <div class="col-md-4">
    <div class="form-group">
      <label for="name-<?php echo $_GET['index'] ?>" class="info-input">Nombre*</label>
      <input type="text" class="form-control" name="name-<?php echo $_GET['index'] ?>" id="name-<?php echo $_GET['index'] ?>" placeholder="Nombre del hijo <?php echo $_GET['index'] ?>" pattern="^[\p{L}]+(?: [\p{L}]+)*$">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label for="education-<?php echo $_GET['index'] ?>" class="info-input">Nivel educativo*</label>
      <select name="education-<?php echo $_GET['index'] ?>" id="education-<?php echo $_GET['index'] ?>" class="form-control" disabled>
        <option value="0">Inicial</option>
        <option value="1">Primaria</option>
        <option value="2">Secundaria</option>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label for="formFile-<?php echo $_GET['index'] ?>" class="form-label info-input">Constancia*</label>
      <input class="form-control" name="formFile-<?php echo $_GET['index'] ?>" type="file" id="formFile-<?php echo $_GET['index'] ?>" accept="application/pdf,image/jpeg,image/jpg,image/png" disabled>
    </div>
  </div>
</div>