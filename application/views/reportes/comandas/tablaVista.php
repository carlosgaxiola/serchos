<div class="row">
	<form class="inline-form">
		<?php if (isset($rango)): ?>
			<div class="col-xs-3">
				<label for="txtFechaInicio">Fecha Inicio</label>
				<input type="text" class="form-control" id="txtFechaInicio" name="txtFechaInicio">
			</div>
			<div class="col-xs-3">
				<label for="txtFechaFin">Fecha Fin</label>
				<input type="text" class="form-control" id="txtFechaFin" name="txtFechaFin">
			</div>
		<?php else: ?>
			<div class="col-xs-3">
				<label for="txtFecha">Fecha</label>
				<input type="text" class="form-control" id="txtFecha" name="txtFecha">
			</div>
		<?php endif; ?>
		<div class="col-xs-3">
			<button style="margin-top:10%;" type="button" class="btn btn-primary" id="btn-buscar" title="Buscar"><i class="fas fa-search"></i></button>
		</div>
	</form>
</div>
<div class="clear">&nbsp;</div>
<div class="container-fluid"  style="background-color: white; padding: 1%;">
	<?php $total = 0; ?>
	<table id="tblComandas" class="table table-responsive table-bordered table-striped table-hover datatable">
		<thead>
			<th>#</th>
			<th>Mesa</th>
			<th>Total</th>
			<th>Observaciones</th>
			<th>Mesero</th>
			<th>Fecha</th>
			<th>Hora</th>
			<th>Estado</th>
		</thead>
		<tbody>
			<?php 
				$totalPagadas = 0;
				$totalRechazadas = 0; 
				$button = "&nbsp;<button type='button' class='btn btn-primary btn-sm btn-see-obs'><i class='fas fa-eye'></i></button>";
			?>
			<?php if (is_array($comandas)): ?>
				<?php foreach ($comandas as $index => $comanda): ?>
					<tr>
						<td><?php echo $index + 1 ?></td>
						<td><?php echo $comanda['mesa'] ?></td>
						<td><?php echo $comanda['total'] ?></td>						
						<td <?php echo strlen($comanda['observaciones']) > 30? "data-obs='".$comanda['observaciones']."'": '' ?>>
							<?php 
								if (strlen($comanda['observaciones']) > 30)
									echo substr($comanda['observaciones'], 0, 30).$button;
								else
									echo $comanda['observaciones'] 

							?>
						</td>
						<td><?php echo $comanda['mesero'] ?></td>
						<td>
							<?php 
								$fecha = new datetime($comanda['fecha']);
								echo $fecha->format("d/m/Y") 
							?>
						</td>
						<td><?php echo $comanda['hora'] ?></td>
						<td>
							<?php 
								if ($comanda['status'] == 3) {
									echo "Pagada";
									$totalPagadas += $comanda['total'];
								}
								else {
									echo "Rechazadas";
									$totalRechazadas += $comanda['total'];
								}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<div class="clear">&nbsp;</div>
<div class="row">
	<div class="col-xs-3">
		<button title="Generar PDF" type="button" class="btn btn-primary" id="btn-pdf"><i class="fas fa-file-pdf"></i> PDF</button>
	</div>
	<div class="col-xs-3 col-xs-offset-3">
		<h4>Total Pagadas: <span id="total-pagadas"><?php echo $totalPagadas ?></span></h4>
	</div>
	<div class="col-xs-3">
		<h4>Total Rechazadas: <span id="total-rechazadas"><?php echo $totalRechazadas ?></span></h4>
	</div>
</div>