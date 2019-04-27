	<!-- jQuery 3 -->
	<script src="<?php echo base_url("assets/js/jquery-3.min.js") ?>"></script>		
	<!-- Bootstrap 3.3.7 -->
	<script src="<?php echo base_url("assets/js/bootstrap-3.min.js") ?>"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo base_url("assets/js/adminlte.min.js") ?>"></script>
	<!-- BootstrapDialog -->
	<script src="<?php echo base_url("assets/js/bootstrap-dialog.min.js") ?>"></script>
	<!-- DataTables Jquery JS -->
	<script src="<?php echo base_url("assets/js/jquery.dataTables.min.js") ?>"></script>
	<!-- DataTables Bootstrap JS -->
	<script src="<?php echo base_url("assets/js/dataTables.bootstrap.min.js") ?>"></script>
	<!-- DateTimePicker -->
	<script src="<?php echo base_url("assets/js/bootstrap-datepicker.min.js") ?>"></script>
	<!-- Validaciones -->
	<script src="<?php echo base_url("assets/js/validaciones.js") ?>"></script>
	<!-- SHA1 -->
	<script src="<?php echo base_url("assets/js/sha1-jshash.js") ?>"></script>
	<!-- NotifIt -->
	<script src="<?php echo base_url("assets/js/notifIt.js") ?>"></script>
	<!-- MomentJS -->
	<script src="<?php echo base_url("assets/js/moment.js") ?>"></script>
	<!-- JQueryTMPL -->
	<script src="<?php echo base_url("assets/js/jquery.tmpl.min.js") ?>"></script>
	<!-- Activar datatables -->
	<script>			
		var tabla = $(".datatable").DataTable({
		    'paging'			: true,
		    'lengthChange' 		: false,
		    'searching'    		: true,
		    'ordering'     		: true,
		    'info'         		: true,
		    'scrollx'      		:true,
		    'autoWidth'    		: false,
		    'destroy'      		: true,
		    "iDisplayLength"	: 10,
		    "language"     : {  
		    	"url": '<?php echo base_url('assets/files/datatables/spanish.json')?>'  
		    }			     	
		})
		
		var base_url = '<?php echo base_url() ?>'
		
		function getDate () {
			let fecha = new Date()				
			dia = fecha.getDate()
			mes = fecha.getMonth() + 1
			año = fecha.getFullYear()
			dia = dia < 10? "0" + dia: dia
			mes = mes < 10? "0" + mes: mes				
			return dia + "/" + mes + "/" + año				
		}

		function getFila (filas, id) {
			for (let fila of filas.toArray())
				if ($(fila).data("id") == id)
					return fila
		}

		function validar (errorMessage) {
			errors = errorMessage.split("&")
			for (let error of errors) {
				error = error.split("=")
				if (error[1] == "")
					$("#" + error[0])
						.parent()
						.removeClass("has-error")
						.children(".error-box")
							.text("")
							.hide()
				else
					$("#" + error[0])
						.parent()
						.addClass("has-error")
						.children(".error-box")
							.text(error[1])
							.show()
			}
		}		

		$(document).ready( function () {
			$(".sidebar-toggle").click (function () {
				setTimeout(function () {
					if ($("body").hasClass("sidebar-collapse"))
						$.each($(".main-sidebar a .no-icon"), function (index, icon) {
							$(icon).show();
						})						
					else {							
						$.each($(".main-sidebar a .no-icon"), function (index, icon) {
							$(icon).hide();
						})
					}
				}, 500)					
			})

			$(".btn-log-out").click( function () {
				BootstrapDialog.confirm({
					title: "Cerrar Sesión",
					message: "¿Confirmar cerrar sesión?",
					btnOKLabel: "Sí",
					btnOKClass: "btn-primary",
					btnCancelLabel: "No",
					callback: function (res) {
						if (res)
							window.location.href = base_url + "inicio/logout";				
					}
				})
			})
		})

		function errorDialog (msg = "Ocurrio un error desconocido", title = "Error") {
			BootstrapDialog.alert({
				title: "Error",
				message: msg,
				type: BootstrapDialog.TYPE_DANGER,
				size: BootstrapDialog.SIZE_SMALL
			})
		}
	</script>
	<!-- App -->
	<?php if (isset($scripts)): ?>
		<?php if (is_array($scripts)): ?>
			<?php foreach ($scripts as $script): ?>
				<script src="<?php echo base_url($script) ?>"></script>
			<?php endforeach; ?>
		<?php else: ?>
			<script src="<?php echo base_url($scripts) ?>"></script>
		<?php endif; ?>
	<?php endif; ?>
</body>
</html>