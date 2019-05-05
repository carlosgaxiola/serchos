<script>
	$(document).ready( function () {
		
		$("#txtFecha").datepicker({
			format: "dd/mm/yyyy",
			autoclose: true,
			todayHighlight: true
		}).datepicker("setDate", new Date())

		$("#txtHora").timepicker({ 
			showInputs: false,
			minuteStep: 60,
			showMeridian: false
		})

		$("#btn-save").click( function () {
			$.ajax({
				url: base_url + "reservaciones/add",
				data: $("#frm-reservacion").serialize() + 
					"&idCliente=" + $("#txtCliente").data("id-cliente"),
				type: "POST",
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 1) {
							BootstrapDialog.alert({
								title: "Exitó",
								message: res.msgSuccess,
								type: BootstrapDialog.TYPE_SUCCESS,
								size: BootstrapDialog.SIZE_SMALL
							})
							validar(res.msg)

						}
						else if (res.code == 0)
							validar(res.msg)
						else if (res.code == -1)
							errorDialog(res.msg)
					} catch ( e ) { console.error(e) }
				}
			})
		})

		$("#btn-clean").click( function () {
			$("#txtCliente").val("").data("id-cliente", 0)
			$("#cmbTipoMesa").val(0)
			$("#txtFecha").val(moment().format("DD/MM/YYYY"))
			$("#cmbHora").val(0)
		})
		
		$("#btn-horarios").click( function () {
			$.ajax({
				url: base_url + "reservaciones/horarios",
				type: "POST",
				data: $("#frm-reservacion").serialize(),
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 0) {
							validar(res.msg)
						}
						else if (res.code == 1) {
							validar(res.msg)
							$("#horarios-group").show()
							$("#fecha-res").text($("#txtFecha").val())
							$("#mesa-res").text($("#cmbTipoMesa option:selected").text())
							$("#tblHoras tbody").empty()
							if (res.data) {
								for (let horas of res.data) {
									$("#tblHoras tbody")
										.append("<tr><td>" + horas.hora_inicio + "</td>"
											+ "<td>" + horas.hora_fin + "</td></tr>"
										)
								}
							}
							else {
								$("#tblHoras tbody")
									.append("<tr><td colspan='2'>" 
										+ "<h5 style='text-align: center;'>No hay horas disponibles</h5>" 
										+ "</td></tr>")
							}
						}
					} catch ( e ) { console.error(e) }
				}
			})
		})

		$("#txtCliente").change( function () {
			let id = $("#clientes-list option[value='" + $(this).val() + "']").data("id-cliente");
			$("#txtCliente").data("id-cliente", id)
		})

		$("#txtCliente").blur( function () {
			let id = $("#clientes-list option[value='" + $(this).val() + "']").data("id-cliente");
			$("#txtCliente").data("id-cliente", id)
		})

		$("#btn-add-cliente").click( function () {
			BootstrapDialog.show({
				title: "Agregar cliente",
				message: $("#tmpl-frm-cliente").tmpl(),
				buttons: [
					{
						label: "Registrar cliente",
						cssClass: "btn-primary",
						action: function (dialog) {
							addCliente(function (cli) {
								dialog.close()
								nombre = cli.nombre + " " + cli.paterno + " " + cli.materno
								$("#clientes-list")
									.append("<option value='" + nombre + "' " +
										"data-id-cliente='" + cli.id + "'></option>")
							})							
						}
					},
					{
						label: "Cancelar",
						cssClass: "btn-danger",
						action: function (dialog) { dialog.close() }
					}
				]
			})
		})

		function addCliente ( callback ) {
			$.ajax({
				url: base_url + "reservaciones/addCliente",
				type: "POST",
				data: $("#frm-cliente").serialize(),
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 1)
							callback({
								nombre: $("#txtNombre").val(),
								paterno: $("#txtPaterno").val(),
								materno: $("#txtMaterno").val(),
								id: res.msg
							})
						else if (res.code == 0)
							validar(res.msg)
						else if (res.code < 0)
							errorDialog(res.msg)
					} catch ( e ) { console.error(e) }
				}
			})
		}
	})
</script>