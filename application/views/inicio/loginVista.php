<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo "Pacheco's | ".$titulo ?></title>  
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
			  	<h1 style="text-align: center;">Pacheco's bar</h1>
			  	<div class="login-box-body">
			    	<p class="login-box-msg">Iniciar Sesión</p>
					<div class="alert alert-danger" hidden>
						El usuario y/o contraseña son incorrectos
					</div>
			    	<form id="frm-login">
			      		<div class="form-group has-feedback">
			        		<input type="text" class="form-control LetrasNumeros" placeholder="Usuario" id="txtUsuario" name="txtUsuario">
			        		<span class="glyphicon glyphicon-user form-control-feedback"></span>
			        		<span class="help-block" style="display: none;">El campo usuario es obligatorio</span>
			      		</div>
			      		<div class="form-group has-feedback">
			        		<input type="password" class="form-control LetrasNumeros" placeholder="Contraseña" id="txtContra" name="txtContra">
			        		<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			        		<span class="help-block" style="display: none;">El campo contraseña es obligatorio</span>
			      		</div>
			      		<div class="row">
				        	<div class="col-xs-4">
				          		<button type="button" id="btn-entrar" class="btn btn-danger">Entrar</button>		          		
				        	</div>
				        	<div class="col-xs-4">
				        		<button type="button" id="btn-registro" class="btn btn-default">Registrarse</button>
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
	<!-- SHA1 -->
	<script src="<?php echo base_url("assets/js/sha1-jshash.js") ?>"></script>
	<!-- Inicio JS -->
	<script>
		var base_url = '<?php echo base_url("index.php/") ?>';

		$('.LetrasNumeros').on('input', function (e) {
            if (!/^[ a-z0-9áéíóúüñ]*$/i.test(this.value)) {
                this.value = this.value.replace(/[^ a-z0-9áéíóúüñ]+/ig,"");
            }
        })

        $("#btn-registro").click( function () {
            window.location.href = base_url + "inicio/registro"
        })

        $("#btn-entrar").click(function () {        	
        	entrar()
        })

        $("#txtUsuario").keyup( function (e) {
            if (e.keyCode == 13 && $(this).val() != "")
                entrar()
        })

        $("#txtContra").keyup( function (e) {
            if (e.keyCode == 13 && $(this).val() != "")
                entrar()
        })

        function entrar () {
            $(".alert-danger").fadeOut('fast')          
            $.ajax({
                url: base_url + "inicio/login",
                data: {
                    contra: $("#txtContra").val()? hex_sha1($("#txtContra").val()): '',
                    usuario: $("#txtUsuario").val()
                },
                type: "POST",
                success: function ( res ) {
                    try {
                        res = JSON.parse(res)
                        switch (res.code) {
                            case 1: //Normal login
                                window.location.reload();
                                break;
                            case 0: //Error login
                                $(".alert-danger").show()
                                $(".form-group").addClass("has-error")
                                break;
                            case -1: //Validation error
                                let msg = res.msg.split("&")
                                for (let validacion of msg) {
                                    error = validacion.split("-")
                                    if (error.indexOf("r") != -1) {
                                        if (error.indexOf("usuario") != -1)
                                            $("#txtUsuario").parent().addClass("has-error").children(".help-block").show()
                                        else if (error.indexOf("contra") != -1)
                                            $("#txtContra").parent().addClass("has-error").children(".help-block").show()
                                    }
                                }
                                break;
                        }
                    }
                    catch ( e ) {
                        console.error(e)
                    }
                }
            })
        }

        $("#txtUsuario").focus( function () {
        	$(this).parent().removeClass("has-error").children(".help-block").hide()
        })
        $("#txtContra").focus( function () {
        	$(this).parent().removeClass("has-error").children(".help-block").hide()
        })
	</script>
</div>
</body>
</html>