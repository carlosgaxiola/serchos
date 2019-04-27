<div class="row">
	<div class="col-xs-3 col-xs-offset-9">
		<button class="btn btn-danger pull-right" id="btn-table"><i class="fas fa-times"></i></button>
	</div>
</div>
<form id="frm-platillo">
	<input type="hidden" id="idPlatillo">
	<div class="row">
		<div class="form-group col-sm-4">
			<label for="txtNombre">Nombre</label>
			<input type="text" class="form-control" id="txtNombre" name="txtNombre">
			<small class="help-block error-box" style="display: none;"></small>
		</div>
		<div class="form-group col-sm-4">
			<label for="txtPrecio">Precio</label>
			<input type="text" class="form-control" id="txtPrecio" name="txtPrecio">
			<small class="help-block error-box" style="display: none;"></small>
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