<script>
	$(document).ready( function () {

		var filas = tabla.rows().nodes(),
			perfil = JSON.parse('<?php echo json_encode($perfil) ?>');

		let url = window.location.href.split("/")
		$("#perfil").val(url[url.length - 1])

		$("#btn-form").click(function () {
			$("#form").show()
			$("#tabla").hide()
			$("#form-title").text("Nuevo " + perfil.singular.trim())
			$("#txtFecha").val(getDate())
			$("#txtStatus").html("<span class='label label-success'>Activo</span>")
		})

		$("#btn-table").click( function () {
			$("#form").hide()
			$("#tabla").show()
			$("#txtNombre").val("")
			$("#txtPaterno").val("")
			$("#txtMaterno").val("")
			$("#txtUsuario").val("")
			$("#txtContra").val("")
			$("#txtConfContra").val("")
			$("#txtFecha").val("")
			$("#txtStatus").html("")
			$("#idUsuario").val("")
			resetForm()
		})

		$("#tblUsuarios").delegate(".btn-edit", "click", function () {
			$("#form").show()
			$("#tabla").hide()
			$("#form-title").text("Editar " + perfil.singular.trim())
			let usuario = $(this).parent().parent().data("usuario")
			$("#txtNombre").val(usuario.nombre)
			$("#txtPaterno").val(usuario.paterno)
			$("#txtMaterno").val(usuario.materno)
			$("#txtUsuario").val(usuario.usuario)
			$("#txtFecha").val(moment(usuario.create_at).format("DD/MM/YYYY"))
			$("#idUsuario").val(usuario.id)
			if (usuario.status)
				$("#txtStatus").html("<span class='label label-success'>Activo</span>")
			else
				$("#txtStatus").html("<span class='label label-danger'>Inactivo</span>")
			$("#frm-usuario").data("usuario", usuario);
		})

		$("#tblUsuarios").delegate(".btn-status", "click", function () {
			let $tr = $(this).parent().parent(),
				usuario = $tr.data("usuario");
			if (usuario.status == 0) {
				BootstrapDialog.confirm({
					title: "Activar el usuario",
					message: "¿Desea activar el usuario " + usuario.nombre + "?",
					btnOKLabel: "Sí",
					btnOKClass: "btn-success",
					btnCancelLabel: "No",
					type: BootstrapDialog.TYPE_SUCCESS,
					callback: function (res) {
						if (res) {
							data = {
								idUsuario: usuario.id,
								status: 1
							}
							toggle(data, function () {
								BootstrapDialog.alert({
									title: "Usuario activado",
									message: "Usuario activado con exito",
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								$("td:eq(3)", $tr).html("<span class='label label-success'>Activo</span>")
								$(".btn-status", $tr)
									.removeClass("btn-success")
									.addClass("btn-danger")
									.prop("title", "Desactivar usuario")
									.children("i")
										.removeClass("fa-toggle-on")
										.addClass("fa-toggle-off")
								resetForm()
							})
						}
					}
				})
			}
			else {
				BootstrapDialog.confirm({
					title: "Desactivar el usuario",
					message: "¿Desea desactivar el usuario " + usuario.nombre + "?",
					btnOKLabel: "Sí",
					btnOKClass: "btn-danger",
					btnCancelLabel: "No",
					type: BootstrapDialog.TYPE_DANGER,
					callback: function (res) {
						if (res) {
							data = {
								idUsuario: usuario.id,
								status: 0
							}
							toggle(data, function () {
								BootstrapDialog.alert({
									title: "Usuario desactivado",
									message: "Usuario desactivado con exito",
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								$("td:eq(3)", $tr).html("<span class='label label-danger'>Inactivo</span>")
								$(".btn-status", $tr)
									.removeClass("btn-danger")
									.addClass("btn-success")
									.prop("title", "Activar usuario")
									.children("i")
										.removeClass("fa-toggle-off")
										.addClass("fa-toggle-on")
							})
						}
					}
				})
			}
		})

		$("#btn-save").click( function () {
			if ($("#idUsuario").val() == "")
				add()
			else
				edit()
		})

		$("#btn-clean").click( function () {
			$("#txtNombre").val("")
			$("#txtPaterno").val("")
			$("#txtMaterno").val("")
			$("#txtUsuario").val("")
			$("#txtFecha").val(moment().format("DD/MM/YYYY"))
			$("#txtStatus").html("<span class='label label-success'>Activo</span>")
			$("#txtContra").val("")
			$("#txtConfContra").val("")
			resetForm()
		})

		function edit () {
			let contra = $("#txtContra").val(),
				confContra = $("#txtConfContra").val(),
				usuario = $("#frm-usuario").data("usuario");
			if ($("#txtContra").val() == "" && $("#txtConfContra").val() == "") {
				$("#txtConfContra").val(usuario.contra)
				$("#txtContra").val(usuario.contra)
			}
			else {
				$("#txtContra").val(contra!=""?hex_sha1(contra):"")
				$("#txtConfContra").val(confContra!=""?hex_sha1(confContra):"")
			}
			if (hayCambios(usuario)) {
				$.ajax({
					url: base_url + "usuarios/edit",
					type: "POST",
					data: $("#frm-usuario").serialize() + "&txtFecha=" + $("#txtFecha").val(),
					success: function ( res ) {
						try {
							res = JSON.parse(res)
							if (res.code == 0) {
								validar(res.msg)
								$("#txtContra").val(contra)
								$("#txtConfContra").val(confContra)
							}
							else if (res.code > 0) {
								BootstrapDialog.alert({
									title: "Usuario editado",
									message: "Usuario editado con exito",
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								let $tr = $(filas).find("td", "[data-id='" + res.msg + "']").parent()
								$("td:eq(1)", $tr).text($("#txtNombre").val())
								$("td:eq(2)", $tr).text($("#txtPaterno").val())
								$("td:eq(3)", $tr).text($("#txtMaterno").val())
								$("td:eq(4)", $tr).text($("#txtUsuario").val())
								init("/" + res.msg)
								$("#tabla").show()
								$("#form").hide()
								$("#txtContra").val("")
								$("#txtConfContra").val("")
								resetForm()
							}
							else if (res.code < 0) {
								errorDialog(res.msg)
								$("#txtContra").val("")
								$("#txtConfContra").val("")
							}
						}
						catch ( e ) {
							console.error(e)
						}
					}
				})
			}
			else {
				$("#tabla").show()
				$("#form").hide()
				$("#txtContra").val("")
				$("#txtConfContra").val("")
				resetForm()
			}			
		}

		function add () {
			let contra = $("#txtContra").val(),
				confContra = $("#txtConfContra").val();
			$("#txtConfContra").val(confContra!=""?hex_sha1(confContra):"")
			$("#txtContra").val(contra!=""?hex_sha1(contra):"")
			$.ajax({
				url: base_url + "usuarios/add",
				type: "POST",
				data: $("#frm-usuario").serialize() + "&txtFecha=" + $("#txtFecha").val(),
				success: function ( res ) {
					try {
						$("#txtConfContra").val(confContra)
						$("#txtContra").val(contra)
						res = JSON.parse(res)
						if (res.code == 0) {
							validar(res.msg)
						}
						else if (res.code < 0)
							errorDialog(res.msg)
						else if (res.code > 0) {
							let fecha = moment().format("DD/MM/YYYY"),
								fila = tabla.row.add([
								tabla.rows().count() + 1,
								$("#txtNombre").val(),
								$("#txtPaterno").val(),
								$("#txtMaterno").val(),
								$("#txtUsuario").val(),
								perfil.singular.trim(),
								fecha,
								"<span class='label label-success'>Activo</span>",
								"<button type='button' class='btn btn-warning btn-sm btn-edit'><i class='fas fa-edit'></i></button>&nbsp;" +
								"<button type='button' class='btn btn-danger btn-sm btn-status'><i class='fas fa-toggle-off'></i></button>"
							]).draw()
							let usuario = {
								id: res.msg,
								nombre: $("#txtNombre").val(),
								paterno: $("#txtPaterno").val(),
								materno: $("#txtMaterno").val(),
								usuario: $("#txtUsuario").val(),
								id_perfil: $("#idPerfil").val(), 
								create_at: moment(fecha).format("YYYY-MM-DD"),
								status: 1
							}
							$(tabla.row(fila).node()).data("usuario", usuario)
							$("#form").hide()
							$("#tabla").show()
							resetForm()
							BootstrapDialog.alert({
								title: "Usuario agregado",
								message: "Usuario agregado con exito",
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
				url: base_url + "usuarios/toggle",
				type: "POST",
				data: data,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res.code)
							callback()
						else if (res.code)
							errorDialog("No se pudo cambiar el estado")
					}
					catch ( e ) {
						console.error(e)
					}
				}
			})
		}

		function init (id = '', $tr = '') {
			$.ajax({
				url: base_url + "usuarios/data/" + $("#perfil").val() + id,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res) {
							if (id != "") {
								res = res[0];
								let fila = $(filas).find("td", "[data-id='" + res.id + "']").parent();
								$("td:eq(1)", tabla.row(fila).node()).text(res.nombre)
								$("td:eq(2)", tabla.row(fila).node()).text(res.paterno)
								$("td:eq(3)", tabla.row(fila).node()).text(res.materno)
								$("td:eq(4)", tabla.row(fila).node()).text(res.usuario)
								$(tabla.row(fila).node()).data("usuario", res)
							}
							else {
								tabla.rows().remove()
								$.each(res, function (index, usuario) {
									let lblStatus = "<span class='label label-success'>Activo</span>",
										btnEdit = "<button class='btn btn-warning btn-edit btn-sm'><i class='fas fa-edit'></i></button>",
										btnStatus = "&nbsp;<button class='btn btn-danger btn-status btn-sm'><i class='fas fa-toggle-off'></i></button>";
									if (usuario.status == 0) {
										lblStatus = "<span class='label label-danger'>Inactivo</span>";
										btnStatus = "<button class='btn btn-success btn-status'><i class='fas fa-toggle-on'></i></button>";
									}
									let fila = tabla.row.add([
										tabla.rows().count() + 1,
										usuario.nombre,
										usuario.paterno,
										usuario.materno,
										usuario.usuario,
										perfil.singular.trim(),
										moment(usuario.create_at).format("DD/MM/YYYY"),
										lblStatus,
										btnEdit + btnStatus
									])
									$(tabla.row(fila).node()).children("td:first").data("id", usuario.id)
									$(tabla.row(fila).node()).data("usuario", usuario)
								})
								filas = tabla.rows().nodes()
							}
							tabla.draw()
						}
					}
					catch ( e ) {
						console.error(e)
					}
				}
			})
		}

		function hayCambios (usuario) {
			return $("#txtNombre").val() != usuario.nombre
				|| $("#txtPaterno").val() != usuario.paterno
				|| $("#txtMaterno").val() != usuario.materno
				|| $("#txtUsuario").val() != usuario.usuario
				|| $("#txtContra").val() != usuario.contra
				|| $("#txtConfContra").val() != usuario.contra;
		}

		function resetForm () {
			$("#txtNombre").parent().removeClass("has-error").children("small").hide()
			$("#txtPaterno").parent().removeClass("has-error").children("small").hide()
			$("#txtMaterno").parent().removeClass("has-error").children("small").hide()
			$("#txtUsuario").parent().removeClass("has-error").children("small").hide()
			$("#txtContra").parent().removeClass("has-error").children("small").hide()
			$("#txtConfContra").parent().removeClass("has-error").children("small").hide()
		}

		init()
	})
</script>