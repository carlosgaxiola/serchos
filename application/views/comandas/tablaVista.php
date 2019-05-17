<style>
	.nav-tabs-custom>.nav-tabs>li.active {
		border-top-color: black;
	}
	.nav-tabs-custom .nav>li>a>sup {
	    position: absolute;
	    top: 2px;
	    right: 5px;
	    text-align: center;
	    font-size: 15px;
	    padding: 2px 2px;
	    line-height: .9;
	}
	.comanda {
		box-shadow: 3px 3px 6px 1px rgba(0,0,0,0.75);
	}
	.comanda:hover {
		cursor: pointer;
	}
	th {
		border-left: 1px solid black !important;
	}
	.platillo-nombre-input {
		border: none !important;
	    width: 90% !important;
	    background-color: transparent;
	    box-shadow: none;
	}
</style>
<div class="clear">&nbsp;</div>
<script type="text/x-jQuery-tmpl" id="comanda-tmpl">
	<div class="info-box comanda">
        <span class="info-box-icon" id="bg-comanda">
        	<i class="fas fa-eye"></i>
        </span>
        <div class="info-box-content">
	        <span class="info-box-text" id="mesa-numero"></span>
	        <span class="info-box-number" id="platillos-cantidad"></span>
	    </div>
    </div>
</script>
<script type="text/x-jQuery-tmpl" id="cantidad-tmpl">
	<div class="container-fluid">
		<div class="form-group">
			<label for="txtCantidadTotal" class="col-xs-2">Cantidad</label>
			<div class="col-xs-8">
				<input type="text" class="form-control" id="txtCantidadTotal" name="txtCantidadTotal">
			</div>
		</div>
	</div>
</script>
<script type="text/x-jQuery-tmpl" id="platillo-tmpl">
	<div class="container-fluid">
		<form id="frm-platillo">
			<div class="form-group col-sm-3">
				<label for="txtPlatillo">Platillo</label>
				<input type="text" id="txtPlatillo" onchange="sugerirPrecio(this.value)" onkeyup="buscarPlatillo(this.value)" class="form-control" list="platillos" name="txtPlatillo">
				<small class="help-block error-box" style="display: none;"></small>
			</div>
			<div class="form-group col-sm-3">
				<label for="txtCantidad">Cantidad</label>
				<input type="text" id="txtCantidad" class="form-control" name="txtCantidad">
				<small class="help-block error-box" style="display: none;"></small>
			</div>
			<div class="form-group col-sm-3">
				<label for="txtPrecio">Precio</label>
				<input type="text" id="txtPrecio" class="form-control" name="txtPrecio">
				<small class="help-block error-box" style="display: none;"></small>
			</div>
		</form>
	</div>
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-6" id="lista">
			<h3 style="margin-top: 0;">Lista de comandas(<span id="total-comandas"></span>)</h3>
			<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
		            <li id="nuevas-tab" class="active">
		            	<a href="#nuevas" data-toggle="tab">
		            		<span class="label label-warning">Nuevas</span>
		            		<sup></sup>
		            	</a>
		            </li>
		            <li id="preparadas-tab">
		            	<a href="#preparadas" data-toggle="tab">
		            		<span class="label label-info">Preparadas</span>
		            		<sup></sup>
		            	</a>
		            </li>
		            <li id="entregadas-tab">
		            	<a href="#entregadas" data-toggle="tab">
		            		<span class="label label-primary">Entregadas</span>
		            		<sup></sup>
		            	</a>
		            </li>
		            <li id="pagadas-tab">
		            	<a href="#pagadas" data-toggle="tab">
		            		<span class="label label-success">Pagadas</span>
		            		<sup></sup>
		            	</a>
		            </li>
		            <li id="rechazadas-tab">
		            	<a href="#rechazadas" data-toggle="tab">
		            		<span class="label label-danger">Rechazadas</span>
		            		<sup></sup>
		            	</a>
		            </li>
	            </ul>
	            <div class="tab-content">
	            	<div class="tab-pane active" id="nuevas">
	            		<div class="pre-scrollable" style="padding: 1%;">
							<div class="default-message" class="text-muted">
								<strong>No hay comandas nuevas</strong>
							</div>
						</div>
	            	</div>
	              	<div class="tab-pane" id="preparadas">
	                	<div class="pre-scrollable" style="padding: 1%;">
	                		<div class="default-message" class="text-muted">
								<strong>No hay comandas preparadas</strong>
							</div>
	                	</div>
	            	</div>
	            	<div class="tab-pane" id="entregadas">
	                	<div class="pre-scrollable" style="padding: 1%;">
	                		<div class="default-message" class="text-muted">
								<strong>No hay comandas entregadas</strong>
							</div>
	                	</div>
	            	</div>
	            	<div class="tab-pane" id="pagadas">
	                	<div class="pre-scrollable" style="padding: 1%;">
	                		<div class="default-message" class="text-muted">
								<strong>No hay comandas pagadas</strong>
							</div>
	                	</div>
	            	</div>
	            	<div class="tab-pane" id="rechazadas">
	            		<div class="pre-scrollable" style="padding: 1%;">
	            			<div class="default-message" class="text-muted">
								<strong>No hay comandas rechazadas</strong>
							</div>
	                	</div>
	            	</div>
	            </div>
        	</div>
        	<!-- <button type="button" id="btn-act">actualizar</button> -->
		</div>
		<div class="col-sm-6" id="tabla-detalle-comanda">
			<h3 style="margin-top: 0;">Detalle de comanda</h3>
			<div class="row">
				<table class="table table-responsive table-striped" style="background-color: white;" id="tbl-detalle">
					<thead>
						<th>Platillo</th>
						<th>Cantidad</th>
						<th>Precio</th>
						<th>Subtotal</th>
						<?php if ($this->session->extempo['idPerfil'] == getIdPerfil("Administrador") || 
							$this->session->extempo['idPerfil'] == getIdPerfil("Cocina")): ?>
							<th>Acción</th>
						<?php endif; ?>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Fecha </strong>
					<span id="fecha-comanda">&nbsp;</span>
				</div>
				<div class="col-sm-3">
					<strong>Hora </strong>
					<span id="hora-comanda">&nbsp;</span>
				</div>
				<div class="col-sm-3">
					<strong>Total </strong>
					<span id="total-comanda">&nbsp;</span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<strong>Observaciónes</strong>
					<p id="observaciones">&nbsp;</p>
				</div>
			</div>
			<div class="row" id="buttons">
				<div id="default-buttons">
					<div class="form-group">
						
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
<datalist id='platillos'>
</datalist>