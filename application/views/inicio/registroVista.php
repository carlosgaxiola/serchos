<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo "Restaurante | ".$titulo ?></title>  
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
				    	<p class="login-box-msg">Registro de clientes</p>
				    	<form id="frm-registro">
				      		<div class="form-group has-feedback">
				      			<label for="txtNombre">Nombre(s)</label>
				        		<input type="text" class="form-control LetrasNumeros required" id="txtNombre" name="txtNombre">
				        		<span class="help-block error-box" style="display: none;">El campo Nombre(s) es obligatorio</span>
				      		</div>
				      		<div class="form-group has-feedback">
				      			<label for="txtPaterno">Apellido Paterno</label>
				        		<input type="text" class="form-control LetrasNumeros required" id="txtPaterno" name="txtPaterno">
				        		<span class="help-block error-box" style="display: none;">El campo Apellido Paterno es obligatorio</span>
				      		</div>
				      		<div class="form-group has-feedback">
				      			<label for="txtMaterno">Apellido Materno</label>
				        		<input type="text" class="form-control LetrasNumeros required" id="txtMaterno" name="txtMaterno">
				        		<span class="help-block error-box" style="display: none;">El campo Apellido Materno es obligatorio</span>
				      		</div>
				      		<div class="form-group has-feedback">
				      			<label for="txtUsuario">Usuario</label>
				        		<input type="text" class="form-control LetrasNumeros required" id="txtUsuario" name="txtUsuario">
				        		<span class="help-block error-box" style="display: none;">El campo Usuario es obligatorio</span>
				      		</div>
				      		<div class="form-group has-feedback">
				      			<label for="txtContra">Contraseña</label>
				        		<input type="password" class="form-control LetrasNumeros required" id="txtContra" name="txtContra">
				        		<span class="help-block error-box" style="display: none;">El campo Contraseña es obligatorio</span>
				      		</div>
				      		<div class="form-group has-feedback">
				      			<label for="txtConfContra">Confirmar Contraseña</label>
				        		<input type="password" class="form-control LetrasNumeros required" id="txtConfContra" name="txtConfContra">
				        		<span class="help-block error-box" style="display: none;">El campo Repetir Contraseña es obligatorio</span>
				      		</div>
				      		<div class="row">
					        	<div class="col-xs-4">
					        		<button type="button" id="btn-registro" class="btn btn-danger">Registrarse</button>
					        	</div>					        	
					        	<div class="col-xs-4 col-xs-offset-3">
					        		<button type="button" id="btn-iniciar" class="btn btn-default">Iniciar Sesión</button>
					        	</div>
					      	</div>
				    	</form>
				  	</div>
				</div>
			</section>
		</main>
		<?php $this->load->view("global/footer") ?>
		<!-- Registro JS -->
		<script>
			

	        $("#btn-registro").click( function () {
	        	let contra = $("#txtContra").val(),
	        		contraSha1 = contra != ""? hex_sha1(contra): "",
	        		confContra = $("#txtConfContra").val(),
	        		confContraSha1 = confContra != ""? hex_sha1(confContra): "";
	        	$("#txtConfContra").val(confContraSha1)
	        	$("#txtContra").val(contraSha1)
            	$.ajax({
            		url: base_url + "inicio/registrarse",
            		type: "POST",
            		data: $("#frm-registro").serialize() + "&fecha=" + getDate(),
            		success: function (res) {
            			try {
            				res = JSON.parse(res)
            				if (res.code == 0) {
            					validar(res.msg)
            					$("#txtContra").val(contra)
            					$("#txtConfContra").val(confContra)
            				}
            				else if (res.code < 0)
            					errorDialog(res.msg)
            				else if (res.code > 0)
            					window.location.href = base_url
            			}
            			catch ( e ) {
            				console.error(e)
            			}
            		}
            	})
	        })	        

	        $("#btn-iniciar").click(function () {        	
	        	window.location.href = base_url
	        })
		</script>	
	</div>	
</body>
</html>
