<script>
	$(document).ready( function () {
		$("#txtFecha").datepicker({
			format: "dd/mm/yyyy",
			autoclose: true,
			todayHighlight: true
		}).datepicker("setDate", new Date());

		var filas = tabla.rows().nodes()

		$("#btn-buscar").click( function () {
			$.ajax({
				url: base_url + "reportes/platillos/?fecha=" + $("#txtFecha").val(),
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						tabla.rows().remove()
						total = 0
						$.each(res, function (index, ele) {
							sub = ele.precio * ele.cantidad
							tabla.row.add([
								tabla.rows().count() + 1,
								ele.nombre,
								ele.precio,
								ele.cantidad,
								sub
							])
							total += sub
						})
						tabla.draw()
						$("#total").text(total)
					}
					catch ( e ) { console.error(e) }
				}
			})			
		})

		$("#btn-pdf").click( function () {
			if (tabla.rows().count()) {
				if ($("#txtFecha").val() != "")
					window.open(base_url + "reportes/generar/platillos?fecha=" + $("#txtFecha").val())
				else
					errorDialog("La fecha no es válida")
			}
			else {
				errorDialog("La tabla no tiene registros")
			}
		})
	})
</script>