<div class="row">
	<div class="col-xs-3 col-xs-offset-9">
		<button type="button" class="btn btn-primary pull-right" id="btn-form"><i class="fas fa-plus"></i></button>
	</div>	
</div>
<div class="clear">&nbsp;</div>
<div class="container-fluid"  style="background-color: white; padding: 1%;">
	<table id="tblPlatillos" class="table table-responsive table-bordered table-striped table-hover datatable">
		<thead>
			<th>#</th>
			<th>Nombre</th>
			<th>Precio</th>
			<th>Estado</th>
			<th>Opciones</th>
		</thead>
		<tbody>
			<?php foreach ($platillos as $index => $platillo): ?>
				<tr>
					<?php 
						if ($platillo['status'] == 0) {
							$statusButton = "<button class='btn btn-success btn-sm btn-status' title='Activar platillo'><i class='fas fa-toggle-on'></i></button>";
							$statusLabel = "<span class='label label-danger'>Inactivo</span>";
						}
						else {
							$statusButton = "<button class='btn btn-danger btn-sm btn-status' title='Desactivar platillo'><i class='fas fa-toggle-off'></i></button>";
							$statusLabel = "<span class='label label-success'>Activo</span>";
						}
					?>
					<td data-id="<?php echo $platillo['id'] ?>"><?php echo $index + 1 ?></td>
					<td><?php echo $platillo['nombre'] ?></td>
					<td><?php echo $platillo['precio'] ?></td>
					<td><?php echo $statusLabel ?></td>
					<td>
						<button class="btn btn-warning btn-sm btn-edit"><i class="fas fa-edit"></i></button>
						<?php echo $statusButton ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>