<script>
	$(document).ready( function () {

		var filas = tabla.rows().nodes()

		$("#btn-form").click(function () {
			$("#form").show()
			$("#tabla").hide()
			$("#txtNombre").val("")
			$("#txtPrecio").val("")
			$("#txtStatus").html("")
			$("#idPlatillo").val("")
		})

		$("#btn-table").click( function () {
			$("#form").hide()
			$("#tabla").show()
		})

		$("#tblPlatillos").delegate(".btn-edit", "click", function () {
			$("#form").show()
			$("#tabla").hide()
			let platillo = $(this).parent().parent().data("platillo")
			console.log(platillo)
			$("#txtNombre").val(platillo.nombre)
			$("#txtPrecio").val(platillo.precio)
			$("#idPlatillo").val(platillo.id)
			if (!platillo.status)
				$("#txtStatus").html("<span class='label label-danger'>Inactivo</span>")
			else
				$("#txtStatus").html("<span class='label label-success'>Activo</span>")
		})

		$("#tblPlatillos").delegate(".btn-status", "click", function () {
			let $tr = $(this).parent().parent(),
				platillo = $tr.data("platillo");
			if (platillo.status == 0)
				BootstrapDialog.confirm({
					title: "Activar el platillo",
					message: "¿Desea activar el platillo " + platillo.nombre + "?",
					btnOKLabel: "Sí",
					btnOKClass: "btn-success",
					btnCancelLabel: "No",
					type: BootstrapDialog.TYPE_SUCCESS,
					callback: function (res) {
						if (res) {
							data = {
								idPlatillo: platillo.id,
								status: 1
							}
							toggle(data, function () {
								BootstrapDialog.alert({
									title: "Platillo activado",
									message: "Platillo activado con exito",
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								$("td:eq(3)", $tr).html("<span class='label label-success'>Activo</span>")
								$(".btn-status", $tr)
									.removeClass("btn-success")
									.addClass("btn-danger")
									.prop("title", "Desactivar platillo")
									.children("i")
										.removeClass("fa-toggle-on")
										.addClass("fa-toggle-off")
								platillo.status = 1;
								$tr.data("platillo", platillo)
							})
						}
					}
				})
			else
				BootstrapDialog.confirm({
					title: "Desactivar el platillo",
					message: "¿Desea desactivar el platillo " + platillo.nombre + "?",
					btnOKLabel: "Sí",
					btnOKClass: "btn-danger",
					btnCancelLabel: "No",
					type: BootstrapDialog.TYPE_DANGER,
					callback: function (res) {
						if (res) {
							data = {
								idPlatillo: platillo.id,
								status: 0
							}
							toggle(data, function () {
								BootstrapDialog.alert({
									title: "Platillo desactivado",
									message: "Platillo desactivado con exito",
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								$("td:eq(3)", $tr).html("<span class='label label-danger'>Inactivo</span>")
								$(".btn-status", $tr)
									.removeClass("btn-danger")
									.addClass("btn-success")
									.prop("title", "Activar platillo")
									.children("i")
										.removeClass("fa-toggle-off")
										.addClass("fa-toggle-on")
								platillo.status = 0;
								$tr.data("platillo", platillo)
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
				url: base_url + "platilos/edit",
				type: "POST",
				data: {
					idPlatillo: $("#idPlatillo").val(),
					txtNombre: $("#txtNombre").val(),
					txtPrecio: $("#txtPrecio").val()
				},
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 0) {
							validar(res.msg)
						}
						else if (res.code > 0) {
							BootstrapDialog.alert({
								title: "Platillo editado",
								message: "Platillo editado con exito",
								type: BootstrapDialog.TYPE_SUCCESS,
								size: BootstrapDialog.SIZE_SMALL
							})
							let $tr = $(filas).find("[data-id='" + res.msg + "']").parent()
							$("td:eq(1)", $tr).text($("#txtNombre").val())
							$("td:eq(2)", $tr).text($("#txtPrecio").val())
							init("/" + res.msg)
							$("#tabla").show()
							$("#form").hide()
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

		function add () {
			$.ajax({
				url: base_url + "platillos/add",
				type: "POST",
				data: {
					txtNombre: $("#txtNombre").val(),
					txtPrecio: $("#txtPrecio").val()? parseFloat($("#txtPrecio").val()): '',
					fecha: getDate()
				},
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 0) {
							validar(res.msg)
						}
						else if (res.code < 0)
							errorDialog(res.msg)
						else if (res.code > 0) {
							let fila = tabla.row.add([
								tabla.rows().count() + 1,
								$("#txtNombre").val(),
								$("#txtPrecio").val(),
								"<span class='label label-success'>Activo</span>",
								"<button type='button' class='btn btn-warning btn-sm btn-edit'><i class='fas fa-edit'></i></button>&nbsp;" +
								"<button type='button' class='btn btn-danger btn-sm btn-status'><i class='fas fa-toggle-off'></i></button>"
							]).draw()
							let platillo = {
								id: res.msg,
								nombre: $("#txtNombre").val(),
								precio: $("#txtPrecio").val(),
								status: 1
							}
							$(tabla.row(fila).node()).data("platillo", platillo)
							$("#form").hide()
							$("#tabla").show()
							BootstrapDialog.alert({
								title: "Platillo agregado",
								message: "Platillo agregado con exito",
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
				url: base_url + "platillos/toggle",
				type: "POST",
				data: data,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code == 1)
							callback()
						else if (res.code == 0)
							errorDialog("No se pudo cambiar el estado")
						else if (res.code == -1)
							errorDialog("No se pude haber menos de 5 platillos desactivados")
					}
					catch ( e ) {
						console.error(e)
					}
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