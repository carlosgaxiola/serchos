<div class="container-fluid">
	<a href="<?php echo base_url("index.php/comandas") ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i></a>
	<div class="clear">&nbsp;</div>
	<div class="row">
		<table class="table table-striped table-hover">
			<thead>
				<th>#</th>
				<th>Platillo</th>
				<th>Cantidad</th>
				<th>Precio</th>
				<th>Total</th>
				<th>Acciones</th>
			</thead>
			<tbody>
				<?php if (is_array($detalles)): ?>
					<?php foreach ($detalles as $index => $detalle): ?>
						<tr>
							<td><?php echo $index + 1 ?></td>
							<td><?php echo $detalle['platillo'] ?></td>
							<td><?php echo $detalle['cantidad'] ?></td>
							<td><?php echo $detalle['precio'] ?></td>
							<td><?php echo $detalle['cantidad'] * $detalle['precio'] ?></td>
							<td>
								<form action="<?php echo base_url("index.php/comandas/platillo") ?>"
									method="post">
									<input type="hidden" name="idComanda" value="<?php echo $detalle['id_comanda'] ?>">
									<input type="hidden" name="idPlatillo" value="<?php echo $detalle['id_platillo'] ?>">
									<button type="submit" class="btn btn-warning">
										Modificar
									</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>