<script>
	var btnStatus = {
		0: "<button type='button' class='btn btn-sm btn-success btn-status'><i class='fas fa-toggle-on'></i></button>",
		1: "<button type='button' class='btn btn-sm btn-danger btn-status'><i class='fas fa-toggle-off'></i></button>",
	},
	lblStatus = {
		1: "<span class='label label-success'>Activo</span>",
		0: "<span class='label label-danger'>Inactivo</span>"
	},
	btnEdit = "<button type='button' class='btn btn-sm btn-edit btn-warning'><i class='fas fa-edit'></i></button>";
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
			let $tr = $(this).parents("tr"), usuario = $tr.data("usuario")
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
			$("#frm-usuario").data("node", tabla.row($tr).node());
		})

		$("#tblUsuarios").delegate(".btn-status", "click", function () {
			let $tr = $(this).parents("tr"),
				usuario = $tr.data("usuario"),
				nuevoStatus = usuario.status == 0 ? 1 : 0;
			BootstrapDialog.confirm({
				title: "Cambiar estado",
				message: "¿Cambiar estado del usuario " + usuario.nombre + " a " + lblStatus[nuevoStatus] + "?",
				btnOKLabel: "Sí",
				btnOKClass: "btn-primary",
				btnCancelLabel: "No",
				callback: function (btn) {
					if (btn) {
						toggle({
							id: usuario.id,
							status: nuevoStatus,
							callback: function () {
								BootstrapDialog.alert({
									title: "Usuario activado",
									message: "Usuario activado con exito",
									type: BootstrapDialog.TYPE_SUCCESS,
									size: BootstrapDialog.SIZE_SMALL
								})
								$("td:eq(7)", $tr).html(lblStatus[nuevoStatus])
								$(".btn-status", $tr).replaceWith(btnStatus[nuevoStatus])
								usuario.status = nuevoStatus
								$tr.data("usuario", usuario)
								resetForm()
							}
						})
					}
				}
			})
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
								let fila = $("#frm-usuario").data("node")
								$("td:eq(1)", fila).text(res.usuario.nombre)
								$("td:eq(2)", fila).text(res.usuario.paterno)
								$("td:eq(3)", fila).text(res.usuario.materno)
								$("td:eq(4)", fila).text(res.usuario.usuario)
								$(fila).data("usuario", res.usuario)
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
							let fila = tabla.row.add([
								tabla.rows().count() + 1,
								$("#txtNombre").val(),
								$("#txtPaterno").val(),
								$("#txtMaterno").val(),
								$("#txtUsuario").val(),
								perfil.singular.trim(),
								moment().format("DD/MM/YYYY"),
								lblStatus[1],
								btnEdit + "&nbsp;" + btnStatus[1]
							]).draw()
							$(fila.node()).data("usuario", res.usuario)
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

		function toggle (config) {
			$.ajax({
				url: base_url + "usuarios/toggle",
				type: "POST",
				data: {id: config.id, status: config.status},
				success: function ( answ ) {
					try {
						answ = JSON.parse(answ)
						if (answ.code == 0)
							errorDialog(answ.msgError)
						else if (answ.code == 1)
							config.callback()
					} catch ( e ) { console.error(e) }
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
									let fila = tabla.row.add([
										tabla.rows().count() + 1,
										usuario.nombre,
										usuario.paterno,
										usuario.materno,
										usuario.usuario,
										perfil.singular.trim(),
										moment(usuario.create_at).format("DD/MM/YYYY"),
										lblStatus[usuario.status],
										btnEdit + "&nbsp;" + btnStatus[usuario.status]
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
			$("#idUsuario").val("")
			$("#txtNombre").val("").parent().removeClass("has-error").children("small").hide()
			$("#txtPaterno").val("").parent().removeClass("has-error").children("small").hide()
			$("#txtMaterno").val("").parent().removeClass("has-error").children("small").hide()
			$("#txtUsuario").val("").parent().removeClass("has-error").children("small").hide()
			$("#txtContra").val("").parent().removeClass("has-error").children("small").hide()
			$("#txtConfContra").val("").parent().removeClass("has-error").children("small").hide()
			$("#frm-usuario").data("usuario", {})
			$("#frm-usuario").data("node", "")
		}

		init()
	})
</script>