<div class="row">
	<div class="col-xs-3">
		<div class="form-group">
			<label for="txtFecha">Fecha</label>
			<input type="text" class="form-control fecha" id="txtFecha" name="txtFecha">
			<small class="help-block error-box" style="display: none;"></small>
			<button class="btn btn-primary btn-sm" id="btn-buscar" type="button"
				style="margin-top: -33px;
				margin-right: -40px;
				float: right;">
				<i class="fas fa-search"></i>
			</button>
		</div>
	</div>
	<div class="col-xs-1 col-xs-offset-8">
		<a class="btn btn-success" style="margin-top: 30%;"
			href="<?php echo base_url("index.php/reservaciones/ver") ?>">
			Nueva
		</a>
	</div>
</div>
<div class="clear">&nbsp;</div>
<div class="container-fluid"  style="background-color: white; padding: 1%;">
	<table id="tblReservaciones" class="table table-responsive table-bordered table-striped table-hover datatable">
		<thead style="background-color: #00a65a; color: white;">
			<th>#</th>
			<th>Cliente</th>
			<th>Mesa</th>
			<th>Cantidad Mesas</th>
			<th>Fecha</th>
			<th>Hora</th>
			<th>Estado</th>
			<th>Opciones</th>
		</thead>
		<tbody>
			<?php if (is_array($reservaciones)): ?>
				<?php foreach ($reservaciones as $index => $res): ?>
					<?php 
						$fecha = new datetime($res['fecha']);
						if ($res['status'] == 0)
							$estado = "Cancelada";
						else if ($res['status'] == 1)
							$estado = "Activa";
						else if ($res['status'] == 2)
							$estado = "Terminada";
						else if ($res['status'] == 3)
							$estado = "Caducada";
					?>
					<tr>
						<td><?php echo $index + 1 ?></td>
						<td><?php echo $res['cliente'] ?></td>
						<td><?php echo $res['mesa'] ?></td>
						<td><?php echo $res['cant_mesa'] ?></td>
						<td><?php echo $fecha->format("d/m/Y") ?></td>
						<td><?php echo $res['hora_inicio'] . " - " . $res['hora_fin'] ?></td>
						<td><?php echo $estado ?></td>
						<td>
							<a class="btn btn-warning"
							href="<?php echo base_url("index.php/reservaciones/ver/").$res['id'] ?>">
								Editar
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>