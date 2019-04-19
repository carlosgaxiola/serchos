	<footer class="main-footer">
	    <div class="pull-right hidden-xs">
	      	<b>Version</b> 0.4
	    </div>
	    <strong>Copyright &copy; 2019 <a href="https://adminlte.io">Carlos Gaxiola</a>.</strong> All rights reserved.
	</footer>
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
	<script src="<?php echo base_url("assets/js/app/validaciones.js") ?>"></script>
	<!-- SHA1 -->
	<script src="<?php echo base_url("assets/js/sha1.js") ?>"></script>
	<!-- CryptoJS -->
	<script src="<?php echo base_url("assets/js/crypto-js.js") ?>"></script>
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