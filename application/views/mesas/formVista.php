<?php $this->load->view("global/header") ?>
<?php $this->load->view("global/navbar") ?>
<main class="content-wrapper no-aside">
	<section class="content" id="tabla">
		<div class="row">
			<div class="col-xs-3">
				<a href="<?php echo base_url("index.php/mesas") ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i></a>
			</div>
		</div>
		<form id="frm-mesa" method="post"
			action="<?php echo base_url("index.php/mesas/").$metodo ?>">
			<input type="hidden" id="idMesa" name="idMesa" value="<?php echo $mesa['id'] ?>">
			<div class="row">
				<div class="form-group col-sm-4 <?php echo form_error("txtNumero")?'has-error':'' ?>">
					<?php 
						$numero = isset($mesa)? $mesa['id']: 'Auto';
						if (set_value("txtNumero"))
							$numero = set_value("txtNumero");
					?>
					<label for="txtNumero">Número</label>
					<input type="text" class="form-control" id="txtNumero" 
						value="<?php echo $numero ?>" disabled="true">
					<?php if (form_error("txtNumero")): ?>
						<small class="help-block text-danger">
							<?php echo form_error("txtNumero") ?>
						</small>
					<?php endif; ?>
				</div>
				<div class="form-group col-sm-4 <?php echo form_error("cmbTipoMesa")? 'has-error' : '' ?>">
					<?php 
						$tipo = isset($mesa)? $mesa['tipo_mesa']: '';
						if (set_value("cmbTipoMesa"))
							$tipo = set_value("cmbTipoMesa");
					?>
					<label for="cmbTipoMesa">Tipo de mesa</label>
					<select name="cmbTipoMesa" id="cmbTipoMesa" class="form-control">
						<option value="0" <?php echo $tipo == 0? 'selected': '' ?>>Selecciona un tipo de mesa</option>
						<option value="1" <?php echo $tipo == 1? 'selected': '' ?>>Mesa para 2</option>
						<option value="2" <?php echo $tipo == 2? 'selected': '' ?>>Mesa para 4</option>
					</select>
					<?php if (form_error("cmbTipoMesa")): ?>
						<small class="help-block text-danger">
							<?php echo form_error("cmbTipoMesa") ?>
						</small>
					<?php endif; ?>
				</div>
				<div class="form-group col-sm-4 <?php form_error("cmbStatus")? 'has-error': '' ?>">
					<?php 
						$status = isset($mesa)? $mesa['status']: '';
						if (set_value("cmbStatus"))
							$status = set_value("cmbStatus");
					?>
					<label for="cmbStatus">Estado de la mesa</label>
					<select name="cmbStatus" id="cmbStatus" class="form-control">
						<option value="-1" <?php echo $status == -1? 'selected': '' ?>>Selecciona un estado inicial</option>
						<option value="0" <?php echo $status == 0? 'selecteed': '' ?>><span class="label label-danger">Fuera de servicio</span></option>
						<option value="1" <?php echo $status == 1? 'selected': '' ?>>Disponible</option>
						<option value="2" <?php echo $status == 2? 'selected': '' ?>>Ocupado</option>
					</select>
					<?php if (form_error("cmbStatus")): ?>
						<small class="help-block text-danger">
							<?php echo form_error("cmbStatus") ?>
						</small>
					<?php endif; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-4">
					<button type="submit" class="btn btn-success" id="btn-save">Guardar</button>
					<button type="reset" class="btn btn-default" id="btn-clean">Limpiar</button>
				</div>
			</div>
		</form>
	</section>
</main>
<?php $this->load->view("global/footer") ?>
<?php $this->load->view("mesas/scriptJS") ?>