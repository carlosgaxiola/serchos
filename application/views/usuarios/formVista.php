<div class="row">
	<div class="col-xs-3">
		<h3 id="form-title"></h3>
	</div>
	<div class="col-xs-3 col-xs-offset-6">
		<button class="btn btn-danger pull-right" id="btn-table"><i class="fas fa-times"></i></button>
	</div>
</div>
<form id="frm-usuario">
	<input type="hidden" id="idUsuario" name="idUsuario">
	<input type="hidden" id="idPerfil" value="<?php echo $idPerfil ?>" name="idPerfil">
	<input type="hidden" id="perfil" name="perfil">
	<div class="row">
		<div class="form-group col-sm-4">
			<label for="txtNombre">Nombre(s)</label>
			<input type="text" class="form-control" id="txtNombre" name="txtNombre">
			<small class="help-block error-box" style="display: none;"></small>
		</div>
		<div class="form-group col-sm-4">
			<label for="txtPaterno">Apellido Paterno</label>
			<input type="text" class="form-control" id="txtPaterno" name="txtPaterno">
			<small class="help-block error-box" style="display: none;"></small>
		</div>
		<div class="form-group col-sm-4">
			<label for="txtMaterno">Apellido Materno</label>
			<input type="text" class="form-control" id="txtMaterno" name="txtMaterno">
			<small class="help-block error-box" style="display: none;"></small>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-sm-4">
			<label for="txtUsuario">Usuario</label>
			<input type="text" class="form-control" id="txtUsuario" name="txtUsuario">
			<small class="help-block error-box" style="display: none;"></small>
		</div>
		<div class="contraGroup">
			<div class="form-group col-sm-4">
				<label for="txtContra">Contraseña</label>
				<input type="password" class="form-control" id="txtContra" name="txtContra">
				<small class="help-block error-box" style="display: none;"></small>
			</div>
			<div class="form-group col-sm-4">
				<label for="txtConfContra">Repetir Contraseña</label>
				<input type="password" class="form-control" id="txtConfContra" name="txtConfContra">
				<small class="help-block error-box" style="display: none;"></small>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-sm-4">
			<label for="txtFecha">Fecha</label>
			<input type="text" class="form-control" id="txtFecha" name="txtFecha" disabled="true">
		</div>
		<div class="form-group col-sm-4">
			<label for="txtStatus">Estado</label>
			<div class="form-control" id="txtStatus" disabled="true"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-4">
			<button type="button" class="btn btn-success" id="btn-save">Guardar</button>
			<button type="button" class="btn btn-default" id="btn-clean">Limpiar</button>
		</div>
	</div>
</form>