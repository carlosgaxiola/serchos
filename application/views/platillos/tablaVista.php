<div class="row">
	<div class="col-xs-3">
		<a class="btn btn-success" href="<?php echo base_url("index.php/platillos/agregar") ?>">Nuevo</a>
	</div>	
</div>
<div class="clear">&nbsp;</div>
<div class="container-fluid">
	<table id="tblPlatillos" class="table table-responsive table-bordered table-striped table-hover">
		<thead style="background-color: #00a65a; color: white;">
			<th>#</th>
			<th>Nombre</th>
			<th>Precio</th>
			<th>Estado</th>
			<th>Opciones</th>
		</thead>
		<tbody>
			<?php if (is_array($platillos)): ?>
				<?php foreach ($platillos as $index => $platillo): ?>
					<tr>
						<td><?php echo $index + 1 ?></td>
						<td><?php echo $platillo['nombre'] ?></td>
						<td><?php echo $platillo['precio'] ?></td>
						<td><?php
							if ($platillo['status'] == 1)
								echo "Activo";
							else
								echo "Inactivo";
						?></td>
						<td>
							<a class="btn btn-warning" 
								href="<?php echo base_url("index.php/platillos/editar/").$platillo['id'] ?>">
								Modificar
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>