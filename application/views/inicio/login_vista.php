<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo "Lavanderia | ".$titulo ?></title>  
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/fontawesome.package.min.css") ?>">  
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/AdminLTE.min.css") ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/_all-skins.min.css") ?>">        
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-3.min.css") ?>">
</head>
<body class="hold-transition skin-blue sidebar-collapse">
<div class="wrapper">    
	<main class="content-wrapper" style="min-height: 100%">
		<section class="content">
			<div class="login-box" style="margin-top: 0px; margin-bottom: 0px;">
			  	<h1>Restaurante Extempo</h1>
			  	<div class="login-box-body">
			    	<p class="login-box-msg">Iniciar Sesión</p>
			    	<?php if ($this->session->flashdata("error")): ?>
						<div class="alert alert-danger">
							<?php echo $this->session->flashdata("error") ?>
						</div>
			    	<?php endif; ?>
			    	<form id="frmLogin" method="post" action="<?php echo base_url("inicio/login") ?>">
			      		<div class="form-group has-feedback <?php echo !empty(form_error("txtUsuario")) ? "has-error" : "" ?>">
			        		<input type="text" class="form-control LetrasNumeros" placeholder="Usuario" id="txtUsuario" name="txtUsuario" value="<?php echo set_value("txtUsuario") ?>">
			        		<span class="glyphicon glyphicon-user form-control-feedback"></span>
			        		<?php echo form_error("txtUsuario", "<span class='help-block'>", "</span>") ?>
			      		</div>
			      		<div class="form-group has-feedback <?php echo !empty(form_error("txtContra")) ? "has-error" : "" ?>">
			        		<input type="password" class="form-control LetrasNumeros" placeholder="Contraseña" id="txtContra" name="txtContra" value="<?php echo set_value("txtContra") ?>">
			        		<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			        		<?php echo form_error("txtContra", "<span class='help-block'>", "</span>") ?>
			      		</div>
			      		<div class="row">
				        	<div class="col-xs-4">
				          		<button type="submit" id="btn-entrar" class="btn btn-primary btn-block btn-flat">Entrar</button>
				        	</div>
				      	</div>
			    	</form>
			  	</div>
			</div>
		</section>
	</main>
	<!-- jQuery 3 -->
	<script src="<?php echo base_url("assets/js/jquery-3.min.js") ?>"></script>		
	<!-- Bootstrap 3.3.7 -->
	<script src="<?php echo base_url("assets/js/bootstrap-3.min.js") ?>"></script>	
	<!-- BootstrapDialog -->
	<script src="<?php echo base_url("assets/js/bootstrap-dialog.min.js") ?>"></script>	
	<!-- Inicio JS -->
	<script>
		$('.LetrasNumeros').on('input', function (e) {
            if (!/^[ a-z0-9áéíóúüñ]*$/i.test(this.value)) {
                this.value = this.value.replace(/[^ a-z0-9áéíóúüñ]+/ig,"");
            }
        });				
	</script>	
</div>
</body>
</html>