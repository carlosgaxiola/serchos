<script>
	$(document).ready( function () {
		var btnEdit = 
					"<button type='button' title='Editar reservación' class='btn btn-sm btn-warning btn-edit-res'>" +
						"<i class='fas fa-edit'></i>" +
					"</button>",
			btnStatus = 
					"<button type='button' title='Cacelar reservación' class='btn btn-sm btn-danger btn-cancelar'>" +
						"<i class='fas fa-times'></i>" +
					"</button>",
			lblStatus = [
				"<span class='label label-danger'>Cancelada</span>",
				"<span class='label label-primary'>Reservada</span>",
				"<span class='label label-success'>Concluida</span>"
			];

		$("#btn-form").click( function () { toogleMain() })

		$("#btn-table").click( function () { toogleMain(false) })

		$(".fecha").datepicker({
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
			let $btn = $(this)
			if ($btn.data("type") == "save") {
				$.ajax({
					url: base_url + "reservaciones/add",
					data: $("#frm-reservacion").serialize() + 
						"&idCliente=" + $("#txtCliente").data("id-cliente"),
					type: "POST",
					success: function ( answ ) {
						try {
							answ = JSON.parse(answ)
							if (answ.code == 1) {
								BootstrapDialog.alert({
									title: "Exitó",
									message: answ.msgSuccess,
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								validar(answ.msg)
								let fila = tabla.row.add([
									answ.res.id,
									answ.res.cliente,
									answ.res.id_mesa,
									answ.res.mesa,
									moment(answ.res.fecha).format("DD/MM/YYYY"),
									answ.res.hora_inicio,
									answ.res.hora_fin,
									lblStatus[answ.res.status],
									btnEdit + "&nbsp;" + btnStatus
								]).draw()
								let tr = tabla.row(fila).node()
								$(tr).data("res", answ.res)
								toogleMain(false)
							}
							else if (answ.code == 0)
								validar(answ.msg)
							else if (answ.code == -1)
								errorDialog(answ.msg)
						} catch ( e ) { console.error(e) }
					}
				})
			}
			else {
				$.ajax({
					url: base_url + "reservaciones/edit",
					data: $("#frm-reservacion").serialize() + 
						"&idCliente=" + $("#txtCliente").data("id-cliente") +
						"&idReservacion=" + $btn.data("id-res"),
					type: "POST",
					success: function ( answ ) {
						try {
							answ = JSON.parse(answ)
							if (answ.code == 1) {
								BootstrapDialog.alert({
									title: "Exitó",
									message: answ.msgSuccess,
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								validar(answ.msg)
								let $tr = $btn.data("tr")
								$("td:eq(0)", $tr).text(answ.res.id)
								$("td:eq(1)", $tr).text(answ.res.cliente)
								$("td:eq(2)", $tr).text(answ.res.id_mesa)
								$("td:eq(3)", $tr).text(answ.res.mesa)
								$("td:eq(4)", $tr).text(moment(answ.res.fecha).format("DD/MM/YYYY"))
								$("td:eq(5)", $tr).text(answ.res.hora_inicio)
								$("td:eq(6)", $tr).text(answ.res.hora_fin)
								$tr.data("res", answ.res)
								toogleMain(false)
							}
							else if (answ.code == 0)
								validar(answ.msg)
							else if (answ.code == -1)
								errorDialog(answ.msg)
						} catch ( e ) { console.error(e) }
					}
				})				
			}
		})

		$("#btn-clean").click( function () {
			$("#txtCliente").val("").data("id-cliente", 0)
			$("#cmbTipoMesa").val(0)
			$("#txtFecha").val(moment().format("DD/MM/YYYY"))
			$("#cmbHora").val(0)
			$("#btn-save").data("type", "save")
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
											+ "<td>" + horas.hora_fin + "</td>"
											+ "<td>" + horas.mesas + "</td></tr>"
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

		$("#tblReservaciones").delegate(".btn-cancelar", "click", function () {
			let $tr = $(this).parents("tr"), res = $tr.data("res")
			if (res) {
				BootstrapDialog.confirm({
					title: "Cancelar reservación",
					message: "¿Desea cancelar la reservación de <storng>" + res.cliente + "</strong>?",
					btnOKLabel: "Sí",
					btnOKClass: "btn-danger",
					btnCancelLabel: "No",
					callback: function ( ok ) {
						if (ok) {
							$.ajax({
								url: base_url + "reservaciones/cancelar/" + res.id,
								success: function ( answ ) {
									try {
										answ = JSON.parse(answ)
										if (answ.code == 1) {
											BootstrapDialog.alert({
												title: "Cancelada",
												message: answ.msg,
												type: BootstrapDialog.TYPE_PRIMARY,
												size: BootstrapDialog.SIZE_SMALL
											})
											$tr.children("td:eq(7)").html(lblStatus[0])
											$tr.children("td:eq(8)").empty()
										}
										else if (answ.code == 0) {
											errorDialog(answ.msg)
										}
									} catch ( e ) { console.error(e) }									
								}
							})
						}
					}
				})
			}
			else
				errorDialog("La reservación no es válida")
		})

		$("#tblReservaciones").delegate(".btn-edit-res", "click", function () {
			let $tr = $(this).parents("tr"), res = $tr.data("res")
			if (res) {
				let id = $("#clientes-list option[value='" + res.cliente + "']").data("id-cliente")
				$("#txtCliente").val(res.cliente).data("id-cliente", id)
				$("#cmbTipoMesa").val(res.tipo_mesa)
				$("#txtFecha").val(moment(res.fecha).format("DD/MM/YYYY"))
				let horas = $("#cmbHora option").toArray()
				for (let index in horas) {
					if (!isNaN(index)) {
						let hora = horas[index]
						resHora = res.hora_inicio + " - " + res.hora_fin
						if ($(hora).text() == resHora) {
							$("#cmbHora").val($(hora).val())
							break
						}
					}		
				}
				$("#btn-save")
					.data("id-res", res.id)
					.data("tr", $tr)
					.data("type", "edit")
					.text("Guardar")
				toogleMain()
			}
			else
				errorDialog("La reservación no es válida")
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

		$("#btn-buscar").click( function () { init() })
	
		function init () {
			$.ajax({
				url: base_url + "reservaciones/data",
				type: "POST",
				data: {
					fecha: $("#txtFechaFiltro").val()
				},
				success: function ( answ )  {
					try {
						answ = JSON.parse(answ)
						tabla.clear().draw()
						if (answ.code == 1) {
							for (let index in answ.data) {
								if (!isNaN(index)) {
									let res = answ.data[index]
									tabla.row.add([
										res.id,
										res.cliente,
										res.id_mesa,
										res.mesa,
										moment(res.fecha).format("DD/MM/YYYY"),
										res.hora_inicio,
										res.hora_fin,
										lblStatus[res.status],
										res.status==1?btnEdit + "&nbsp;" + btnStatus: ''
									])
									let filas = tabla.rows().nodes()
									$(filas[filas.length - 1]).data("res", res)
								}
							}
							tabla.draw()
						}
					} catch ( e ) { console.error(e) }
				}
			})
		}

		function toogleMain (frm = true) {
			if (frm) {
				$("#tbl").hide()
				$("#frm").show()
			}
			else {
				$("#tbl").show()
				$("#frm").hide()
				$("#horarios-group").hide()
				$("#txtCliente").data("id-cliente", "0").val("")
				$("#cmbTipoMesa").val(0)
				$("#txtFecha").val(moment().format("DD/MM/YYYY"))
				$("#cmbHora").val(0)
				$("#btn-save").text("Resevar").data("type", "save")
			}
		}

		init()		
	})
</script>