<script>
	$(document).ready( function () {
		<?php if (isset($rango)): ?>
			$("#txtFechaInicio").datepicker({
				format: "dd/mm/yyyy",
				autoclose: true,
				todayHighlight: true
			}).datepicker("setDate", new Date());

			$("#txtFechaFin").datepicker({
				format: "dd/mm/yyyy",
				autoclose: true,
				todayHighlight: true
			}).datepicker("setDate", new Date());
		<?php else:  ?>
			$("#txtFecha").datepicker({
				format: "dd/mm/yyyy",
				autoclose: true,
				todayHighlight: true
			}).datepicker("setDate", new Date());
		<?php endif; ?>

		var filas = tabla.rows().nodes()

		$("#btn-buscar").click( function () {
			<?php if (isset($rango)): ?>
				let metodo = "rango?inicio=" + $("#txtFechaInicio").val() + "&fin=" + $("#txtFechaFin").val()
			<?php else: ?>
				let metodo = "diario?fecha=" + $("#txtFecha").val()
			<?php endif; ?>
			$.ajax({
				url: base_url + "reportes/" + metodo,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						tabla.rows().remove()
						totalPagadas = totalRechazadas = 0
						$.each(res, function (index, ele) {
							if (ele.status == 3) {
								status = "Pagada"
								totalPagadas += parseFloat(ele.total)
							}
							else {
								status = "Rechazada"
								totalRechazadas += parseFloat(ele.total)
							}
							obs = {preview: ele.observaciones.substr(0, 30)}
							if (ele.observaciones.length > 30)
								obs.preview +="&nbsp;<button type='button' class='btn btn-primary btn-sm btn-see-obs'><i class='fas fa-eye'></i></button>"
							tabla.row.add([
								tabla.rows().count() + 1,
								ele.mesa,
								ele.total,
								obs.preview,
								ele.mesero,
								moment(ele.fecha).format("DD/MM/YYYY"),
								ele.hora,
								status
							])
							let $tabla = $(tabla.rows().nodes())
							$($tabla[$tabla.length-1]).children("td:eq(3)").data("obs", ele.observaciones)
						})
						tabla.draw()
						$("#total-pagadas").text(totalPagadas)
						$("#total-rechazadas").text(totalRechazadas)
					}
					catch ( e ) { console.error(e) }
				}
			})			
		})

		$("#btn-pdf").click( function () {
			<?php if (isset($rango)): ?>
				let metodo = "rango?inicio=" + $("#txtFechaInicio").val() + "&fin=" + $("#txtFechaFin").val()
			<?php else: ?>
				let metodo = "diario?fecha=" + $("#txtFecha").val()
			<?php endif; ?>
			if (tabla.rows().count()) {
				<?php if (isset($rango)): ?>
					let valido = $("#txtFechaInicio").val() != "" && $("#txtFechaFin").val() != ""
				<?php else: ?>
					let valido = $("#txtFecha").val() != ""
				<?php endif; ?>
				if (valido)
					window.open(base_url + "reportes/generar/" + metodo)
				else
					errorDialog("La fecha no es válida")
			}
			else {
				errorDialog("La tabla no tiene registros")
			}
		})

		$("#tblComandas").delegate(".btn-see-obs", "click", function () {
			let $td = $(this).parent(), obs = $td.data("obs");
			BootstrapDialog.alert({
				title: "Observaciones",
				message: "<div class='container-fluid'>"
							+ "<div class='row' style='overflow: scroll;'>"
								+ obs +
							+ "</div>"
						+ "</div>",
				size: BootstrapDialog.SIZE_SMALL
			})
		})
	})
</script>