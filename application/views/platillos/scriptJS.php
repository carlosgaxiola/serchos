<script>
	var lblStatus = [
		"<span class='label label-danger'>Inactivo</span>",
		"<span class='label label-success'>Activo</span>"
	],
	btnStatus = [
		"<button type='button' class='btn btn-success btn-status btn-sm' title='Activar platillo'><i class='fas fa-toggle-on'></i></button>",
		"<button type='button' class='btn btn-danger btn-status btn-sm' title='Desactivar platillo'><i class='fas fa-toggle-off'></i></button>"
	],
	btnEdit = "<button type='button' class='btn btn-warning btn-edit' title='Editar platillo'><i class='fas fa-edit'></i></button>";
	var toggleMain = function () {
		if ($("#form").css("display") === "none") {
			$("#form").show()
			$("#tabla").hide()
		}
		else {
			$("#tabla").show()
			$("#form").hide()
		}
	}

	var clearForm = function () {
		$("#txtNombre").val("")
		$("#txtPrecio").val("")
		$("#txtStatus").empty()
		$("#idPlatillo").val("")
	}

	$(document).ready( function () {

		var filas = tabla.rows().nodes()

		$("#btn-form").click(function () {
			toggleMain()
			clearForm()
		})

		$("#btn-table").click( function () { toggleMain() })

		$("#tblPlatillos").delegate(".btn-edit", "click", function () {
			let $tr = $(this).parents("tr"), pla = $tr.data("platillo")
			if (pla) {
				toggleMain()
				$("#txtNombre").val(pla.nombre)
				$("#txtPrecio").val(pla.precio)
				$("#idPlatillo").val(pla.id)
				$("#txtStatus").html(lblStatus[pla.status])
				$("#frm-platillo").data("node", tabla.row($tr).node())
			}
			else
				errorDialog("Error, datos no validos")
		})

		$("#tblPlatillos").delegate(".btn-status", "click", function () {
			let $tr = $(this).parent().parent(), pla = $tr.data("platillo");
			BootstrapDialog.confirm({
				title: "Cambiar estado",
				message: "¿Desea cambiar el estado del platillo " + pla.nombre + "?",
				btnOKLabel: "Sí",
				btnOKClass: "btn-primary",
				btnCancelLabel: "No",
				type: BootstrapDialog.TYPE_PRIMARY,
				callback: function ( res ) {
					if (res) {
						let nuevoStatus = pla.status == 0 ? 1 : 0;
						toggle({
							data: {
								idPlatillo: pla.id,
								status: nuevoStatus
							},
							success: function () {
								BootstrapDialog.alert({
									title: "Platillo activado",
									message: "Platillo " + pla.nombre + " cambiado a " + lblStatus[nuevoStatus],
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								$(".label", $tr).replaceWith(lblStatus[nuevoStatus])
								$(".btn-status", $tr).replaceWith(btnStatus[nuevoStatus])
								pla.status = nuevoStatus;
								$tr.data("platillo", pla)
							}
						})
					}
				}
			})
		})

		$("#btn-save").click( function () {
			if ($("#idPlatillo").val() == "")
				add()
			else
				edit()
		})

		function edit () {
			$.ajax({
				url: base_url + "platillos/edit",
				type: "POST",
				data: $("#frm-platillo").serialize(),
				success: function ( answ ) {
					try {
						answ = JSON.parse(answ)
						if (answ.code == 0) {
							validar(answ.msg)
						}
						else if (answ.code > 0) {
							BootstrapDialog.alert({
								title: "Platillo editado",
								message: "Platillo editado con exito",
								type: BootstrapDialog.TYPE_SUCCESS,
								size: BootstrapDialog.SIZE_SMALL
							})
							let fila = $("#frm-platillo-").data("node")
							$("td:eq(1)", fila).text($("#txtNombre").val())
							$("td:eq(2)", fila).text($("#txtPrecio").val())
							$(fila).data("platillo", answ.platillo)
							toggleMain()
						}
						else if (answ.code < 0)
							errorDialog(answ.msg)
					} catch ( e ) { console.error(e) }
				}
			})
		}

		function add () {
			$.ajax({
				url: base_url + "platillos/add",
				type: "POST",
				data: $("#frm-platillo").serialize(),
				success: function ( answ ) {
					try {
						answ = JSON.parse(answ)
						if (answ.code == 0) {
							validar(answ.msg)
						}
						else if (answ.code < 0)
							errorDialog(answ.msg)
						else if (answ.code > 0) {
							let fila = tabla.row.add([
								tabla.rows().count() + 1,
								answ.pla.nombre, answ.pla.precio,
								lblStatus[1], btnEdit + "&nbsp;" + btnStatus[1]
							]).draw()
							$(fila.node()).data("platillo", platillo)
							toggleMain()
							BootstrapDialog.alert({
								title: "Platillo agregado",
								message: "Platillo agregado con exito",
								type: BootstrapDialog.TYPE_SUCCESS,
								size: BootstrapDialog.SIZE_SMALL
							})
						}
					} catch ( e ) { console.error(e) }
				}
			})
		}

		function toggle (config) {
			$.ajax({
				url: base_url + "platillos/toggle",
				type: "POST",
				data: config.data,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 1)
							config.success()
						else if (res.code == 0)
							errorDialog("No se pudo cambiar el estado")
						else if (res.code == -1)
							errorDialog("No se pude haber menos de 5 platillos desactivados")
					} catch ( e ) { console.error(e) }
				}
			})
		}

		function init (id = '') {
			$.ajax({
				url: base_url + "platillos/data" + id,
				success: function ( res ) {
					try {
						$.each(JSON.parse(res), function (index, platillo) {
							$(filas).find("[data-id='" + platillo.id + "']").parent().data("platillo", platillo)
						})
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