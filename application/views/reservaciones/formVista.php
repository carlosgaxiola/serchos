<script type="text/x-jQuery-tmpl" id="tmpl-frm-cliente">
	<form id="frm-cliente">
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
	</form>
</script>
<div class="col-xs-3 col-xs-offset-9">
	<button type="button" class="btn btn-danger pull-right" id="btn-table"><i class="fas fa-times"></i></button>
</div>
<form id="frm-reservacion">
	<?php 
		$idPerfil = $this->session->extempo['idPerfil'];
		$idPefil = $idPerfil == getIdPerfil("Recepcionista");
		$idPefil = $idPerfil == getIdPerfil("Administrador");
		$idPefil = $idPerfil == getIdPerfil("Gerente");
	?>
	<?php if ($idPerfil != false): ?>
		<div class="row">
			<div class="form-group col-sm-3">
				<label for="txtCliente">Cliente</label>
				<input type="text" placeholder="Selecciona un cliente" name="txtCliente" id="txtCliente" class="form-control" list="clientes-list">
				<button type="button" class="btn btn-sm btn-primary" id="btn-add-cliente"
					style="
					    float: right;
					    margin-top: -33px;
					    margin-right: -40px;
					">
					<i class="fas fa-user-plus"></i>
				</button>
				<small class="help-block error-box"></small>
				<datalist id="clientes-list">
					<?php if (is_array($clientes)): ?>
						<?php foreach ($clientes as $cliente): ?>
							<?php 
								$nombre = $cliente['nombre']." ".$cliente['paterno']." ".$cliente['materno']; 
							?>
							<option value="<?php echo $nombre ?>" data-id-cliente="<?php echo $cliente['id'] ?>">
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</datalist>
			</div>
			<div class="form-group col-sm-3" style="margin-left: 20px;">
				<label for="cmbTipoMesa">Tipo de mesa</label>
				<select name="cmbTipoMesa" id="cmbTipoMesa" class="form-control">
					<option value="0">Selecciona un tipo de mesa</option>
					<option value="1">Mesa para 2</option>
					<option value="2">Mesa para 4</option>
				</select>
				<small class="help-block error-box" style="display: none;"></small>
			</div>
			<div class="form-group col-sm-2">
				<label for="txtFecha">Fecha</label>
				<input type="text" class="fecha form-control" name="txtFecha" id="txtFecha">
				<small class="help-block error-box" style="display: none;"></small>
			</div>
			<div class="form-group col-sm-3">
				<label for="txtHora">Hora</label>
				<select name="cmbHora" id="cmbHora" class="form-control">
					<option value="0">Selecciona un hora</option>
					<option value="1">09:00:00 - 11:00:00</option>
					<option value="2">11:00:00 - 13:00:00</option>
					<option value="3">13:00:00 - 15:00:00</option>
					<option value="4">15:00:00 - 17:00:00</option>
					<option value="5">17:00:00 - 19:00:00</option>
				</select>
				<small class="help-block error-box" style="display: none;"></small>
			</div>
		</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-xs-4">
			<button type="button" class="btn btn-primary" id="btn-horarios">Horarios disponibles</button>
			<button type="button" data-type="save" class="btn btn-success" id="btn-save">Reservar</button>
			<button type="button" class="btn btn-default" id="btn-clean">Limpiar</button>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6" id="horarios-group" style="display: none;">
			<div class="row">
				<div class="col-xs-6">
					<h3>Fecha: <strong><span id="fecha-res"></span></strong></h3>
				</div>
				<div class="col-xs-6">
					<h3>Mesa: <strong><span id="mesa-res"></span></strong></h3>
				</div>
			</div>
			<table id="tblHoras" class="table table-responsive table-striped" style="background-color: white;">
				<thead><th>Hora Inicio</th><th>Hora Fin</th><th>Mesas</th></thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</form>