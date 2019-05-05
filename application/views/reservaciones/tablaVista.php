<div class="row">
	<div class="col-md-3">
		<div class="form-group">
			<label for="txtFechaFiltro">Fecha</label>
			<input type="text" class="form-control fecha" id="txtFechaFiltro" name="txtFechaFiltro">
			<small class="help-block error-box" style="display: none;"></small>
			<button class="btn btn-primary btn-sm" id="btn-buscar" type="button"
				style="margin-top: -33px;
				margin-right: -40px;
				float: right;">
				<i class="fas fa-search"></i>
			</button>
		</div>
	</div>
	<div class="col-xs-3 col-xs-offset-6">
		<button type="button" class="btn btn-primary pull-right" id="btn-form"><i class="fas fa-plus"></i></button>
	</div>
</div>
<div class="clear">&nbsp;</div>
<div class="container-fluid"  style="background-color: white; padding: 1%;">
	<table id="tblReservaciones" class="table table-responsive table-bordered table-striped table-hover datatable">
		<thead>
			<th>#</th>
			<th>Cliente</th>
			<th>No. Mesa</th>
			<th>Mesa</th>
			<th>Fecha</th>
			<th>Inicio</th>
			<th>Fin</th>
			<th>Estado</th>
			<th>Opciones</th>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>