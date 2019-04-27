<script>
	$(document).ready( function () {
		var filas,
			tiposMesa = [],
			statusLbls = [
				"<span class='label label-danger'>Fuera de servicio</span>",
				"<span class='label label-primary'>Disponible</span>",
				"<span class='label label-warning'>Ocupado</span>",
			],
			btnEdit =   
				"<button type='button' class='btn btn-warning btn-sm btn-edit'>" +
					"<i class='fas fa-edit'></i>" +
				"</button>&nbsp;",
			btnStatus = [];
			btnStatus[1] =
				"<button title='Ocupar mesa' type='button' class='btn btn-warning btn-sm btn-status'>" +
					"<i class='fa fa-circle'></i>" +
				"</button>&nbsp;";
			btnStatus[2] =
				"<button title='Desocupar mesa' type='button' class='btn btn-primary btn-sm btn-status'>" +
					"<i class='fa fa-circle-o'></i>" +
				"</button>&nbsp;";
			tiposMesa[1] = 'Para 2';
			tiposMesa[2] = 'Para 4';

		$("#txtFecha").val( getDate() ) 

		function toggleContent () {
			if ($("#form").css("display") == "none") {
				$("#form").show()
				$("#tabla").hide()
			}
			else {
				$("#tabla").show()
				$("#form").hide()
			}
		}

		function resetForm () {
			$("#txtNumero").val("Auto")
			$("#cmbTipoMesa").val("0")
			$("#idMesa").val("")
			$("#cmbStatus").val("-1")
			$(".form-group").removeClass("has-error")
			$(".error-box").hide()
		}

		function hayCambios (mesa) { return $("#cmbTipoMesa").val() != mesa.tipo_mesa || $("#cmbStatus").val() != mesa.status; }

		$("#btn-form").click(function () {
			toggleContent()
			$("#form-title").text("Nueva mesa")
		})

		$("#btn-table").click( function () {
			toggleContent()
			resetForm()
		})

		$("#tblMesas").delegate(".btn-edit", "click", function () {
			toggleContent()
			$("#form-title").text("Editar mesa")
			let mesa = $(this).parent().parent().data("mesa")
			$("#cmbTipoMesa").val(mesa.tipo_mesa)
			$("#cmbStatus").val(mesa.status)
			$("#idMesa").val(mesa.id)
			$("#txtNumero").val(mesa.id)
			$("#frm-mesa").data("mesa", mesa);
		})

		$("#tblMesas").delegate(".btn-status", "click", function () {
			let $btn = $(this),
				$tr = $btn.parent().parent(),
				mesa = $tr.data("mesa");
			if (mesa.status == 2) {
				BootstrapDialog.confirm({
					title: "La mesa " + mesa.id + "esta ocupada",
					message: "¿Desea desocuparla?",
					btnOKLabel: "Sí",
					btnOKClass: "btn-success",
					btnCancelLabel: "No",
					type: BootstrapDialog.TYPE_SUCCESS,
					callback: function (res) {
						if (res) {
							data = {
								idMesa: mesa.id,
								status: 1
							}
							toggle(data, function () {
								let noti = notif({
									'msg': "Mesa " + mesa.id + " desocupada",
									'type': 'info',
									'position': 'right'
								})
								$("td:eq(3)", $tr).html(btnEdit + btnStatus[1])
								$("td:eq(2)", $tr).html(statusLbls[1])
								setTimeout( function () {
									noti.destroy()
								}
								, 1000 * 2)
								mesa.status = 1
								$tr.data("mesa", mesa)
							})
						}
					}
				})
			}
			else if (mesa.status == 1) {
				toggle(
					{
						status: 2,
						idMesa: mesa.id
					},
					function () {
						$("td:eq(3)", $tr).html(btnEdit + btnStatus[2])
						$("td:eq(2)", $tr).html(statusLbls[2])
						let noti = notif({
							'msg': "Mesa " + mesa.id + " ocupada",
							'type': 'warning',
							'position': 'right'
						})
						setTimeout( function () {
							noti.destroy()
						}
						, 1000 * 2)
						mesa.status = 2
						$tr.data("mesa", mesa)
					}
				)
			}
		})

		$("#btn-save").click( function () {
			if ($("#idMesa").val() == "")
				add()
			else
				edit()
		})

		$("#btn-clean").click( function () { resetForm() })

		function edit () {
			let mesa = $("#frm-mesa").data("mesa")
			if (hayCambios(mesa)) {
				$.ajax({
					url: base_url + "mesas/edit",
					type: "POST",
					data: $("#frm-mesa").serialize(),
					success: function ( res ) {
						try {
							res = JSON.parse(res)
							if (res.code == 0) {
								validar(res.msg)
							}
							else if (res.code == 1) {
								BootstrapDialog.alert({
									title: "Mesa editada",
									message: "La mesa <strong>" + mesa.id + "</strong> se actualizó con exitó",
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								let fila = getFila(filas, mesa.id);
								mesa.tipo_mesa = $("#cmbTipoMesa").val()
								mesa.status = $("#cmbStatus").val()
								$("td:eq(1)", fila).text( tiposMesa[ mesa.tipo_mesa ] ) 
								$("td:eq(2)", fila).html( statusLbls[ mesa.status ] )
								$(fila).data("mesa", mesa)
								toggleContent()
								resetForm()
							}
							else if (res.code < 0) {
								errorDialog(res.msg)
							}
						}
						catch ( e ) {
							console.error(e)
						}
					}
				})
			}
			else {
				toggleContent()
				resetForm()
			}
		}

		function add () {
			$.ajax({
				url: base_url + "mesas/add",
				type: "POST",
				data: $("#frm-mesa").serialize(),
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 0) {
							validar(res.msg)
						}
						else if (res.code < 0) {
							errorDialog(res.msg)
						}
						else if (res.code > 0) {
							let mesa = {
								id: res.msg,
								tipo_mesa: $("#cmbTipoMesa").val(),
								status: $("#cmbStatus").val()
							},
							fila = tabla.row.add([
								res.msg,
								tiposMesa[ mesa.tipo_mesa ],
								statusLbls[ mesa.status ],
								btnEdit
							]).draw()
							$(tabla.row(fila).node()).data("mesa", mesa)
							toggleContent()
							resetForm()
							BootstrapDialog.alert({
								title: "Mesa agregada",
								message: "Mesa número <strong>" + res.msg + "</strong> agregada",
								type: BootstrapDialog.TYPE_SUCCESS,
								size: BootstrapDialog.SIZE_SMALL
							})
						}
					}
					catch ( e ) {
						console.error(e)
					}
				}
			})
		}

		function toggle (data, callback) {
			$.ajax({
				url: base_url + "mesas/toggle",
				type: "POST",
				data: data,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 1)
							callback()
						else if (res.code == 0)
							errorDialog("No se pudo cambiar el estado")
					}
					catch ( e ) {
						console.error(e)
					}
				}
			})
		}

		function init (id = '') {
			$.ajax({
				url: base_url + "mesas/data" + id,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res) {
							tabla.rows().remove()
							$.each(res, function (index, mesa) {
								let fila = tabla.row.add([
									mesa.id,
									tiposMesa[ mesa.tipo_mesa ],
									statusLbls[ mesa.status ],
									btnEdit + (mesa.status!=0?btnStatus[mesa.status]:'')
								])
								$(tabla.row(fila).node()).children("td:first").data("id", mesa.id)
								$(tabla.row(fila).node()).data("mesa", mesa)
								$(tabla.row(fila).node()).data("id", mesa.id)
							})
							filas = tabla.rows().nodes()
							tabla.draw()
						}
					}
					catch ( e ) {
						console.error(e)
					}
				}
			})
		}

		init()
	})
</script>