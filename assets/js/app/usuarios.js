$(document).ready( function () {

	//Generico
	$("#btn-save").click( function () {
		if ($("#idUsuario").val() === "") {
			add( function () {
				BootstrapDialog.alert({
					title: "Usuario agregado",
					message: "Se registro el usuario correctamente",
					type: BootstrapDialog.TYPE_SUCCESS,					
				});
				toggleMain();
				$("#msg-error").hide();
			})			
		}
		else {
			edit( function () {
				BootstrapDialog.alert({
					title: "Usuario modificado",
					message: "Se registro el usuario correctamente",
					type: BootstrapDialog.TYPE_SUCCESS,
				})
				toggleMain();
				$("#msg-error").hide();
				tabla.draw()
			});
		}
	})

	function add (callback) {
		$.ajax({
			url: base_url + "administrar/usuarios/add",
			type: "POST",
			data: $("#frmUsuario").serialize() + "&fecha=" + getDate(),
			success: function (res) {				
				try  {
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.log("error");								
								break
							default:
								let usuario = getFormLog(res);
								addTableLog(usuario);
								callback();
								break
						}
					else {
						$("#msg-error").show();
						$("#list-error").html(res);
					}
				}
				catch (e) {
					console.error(e);
				}				
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.error("Error::" + errorThrown);
			}
		})
	}

	function edit (callback) {
		$.ajax({
			url: base_url + "administrar/usuarios/edit",
			type: "POST",
			data: $("#frmModulo").serialize(),
			success: function (res) {				
				try  {					
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.error("error: "  + res);
								break
							default:
								let usuario = getFormLog(res);
								editTableLog(usuario);
								callback();
								break
						}
					else {
						$("#msg-error").show();
						$("#list-error").html(res);
					}
				}
				catch (e) {
					console.error(e);
				}				
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.error("Error::" + errorThrown);
			}
		})
	}
})

//Generico
function setFormLog (usuario) {
	if (usuario != null && usuario != undefined) {
		$("#idUsuario").val(usuario.id);
		$("#txtNombre").val(usuario.nombre);				
		$("#cmbPerfil").val(usuario.perfil);		
		return true;
	}
	else {
		BootstrapDialog.alert({
			title: "Error",
			message: "El usuario que desea editar no existe",
			type: BootstrapDialog.TYPE_DANGER,
			size: BootstrapDialog.SIZE_DANGER				
		})
		return false;
	}
}

function getTableLog (that) {
	let $tr = $(that).parent().parent();
	log = {
		id: $(that).data("id"),
		nombre: tabla.row($tr).data()[1],		
		perfil: $("td:eq(2)", $tr).data("perfil")
	};	
	return log;
}

function getFormLog (idUsuario) {
	let perfilNombre = $("#cmbPerfil option:selected").text();	
	usuario = {
		id: idUsuario,
		nombre: $("#txtNombre").val(),		
		perfil: $("#cmbPerfil").val(),
		perfilNombre: perfilNombre
	}
	return usuario;
}

function addTableLog (usuario) {
	let row = tabla.row.add([
		tabla.rows().count() + 1,
		usuario.nombre,		
		usuario.perfilNombre,
		getDate(),
		'<label class="label label-success">Activo</label>',
		'<button type="button" class="btn btn-warning btn-sm btn-edit-log" data-id="' + usuario.id + '" title="Editar registro"><i class="fas fa-edit"></i></button>&nbsp;' +
		'<button type="button" class="btn btn-danger btn-sm btn-toggle-log" data-id="' + usuario.id  + '" title="Desactivar registro" data-status="1"><i class="fas fa-toggle-off"></i></button>'
	]).draw();
	$(row).data("id", usuario.id);	
	$("td:eq(2)", row).data("perfil", usuario.perfil)
	clearFormData();
}

function editTableLog (usuario) {
	let Break = {};
	try {		
		$.each($("#tblUsuarios tbody tr"), function (index, tr) {				
			if ($(tr).data("id") == usuario.id) {
				$("td:eq(1)", tr).text(usuario.nombre);
				tabla.row(tr).data()[1] = usuario.nombre;
				$("td:eq(2)", tr).text(usuario.padreNombre);
				$("td:eq(2)", tr).data(usuario.padre);				
				throw Break;
			}
		})
	} catch (e) {
		// console.error(e)
	}	
}

function toggleLog (that) {
	let $tr = $(that).parent().parent(),
		nombre = $("td:eq(1)", $tr).text(),
		idUsuario = $(that).data("id"),
		status = $(that).data("status")
		accion = 'desactivar el modulo',
		tipo = BootstrapDialog.TYPE_DANGER,
		okClass = "btn-danger",
		cancelClass = "btn-default";
	if (status == 0) {
		tipo = BootstrapDialog.TYPE_SUCCESS;
		okClass = "btn-success";
		cancelClass = "btn-default";
		accion = "activar el modulo";
	}		
	BootstrapDialog.confirm({
		title: "Cambiar estado",
		message: "¿Desea " + accion + " de " + nombre + "?",
		type: tipo,
		size: BootstrapDialog.SIZE_SMALL,
		btnOKLabel: "Sí",
		btnOKClass: okClass,
		btnCancelLabel: "No",
		btnCancelClass: cancelClass,
		callback: function (res) {
			if (res) {
				$.ajax({
					url: base_url + "administrar/usuarios/toggle",
					type: "POST",
					data: {
						idUsuario: idUsuario,
						status: status
					},
					success: function (res) {						
						try {
							res = JSON.parse(res);
							let iconClass = "fas fa-toggle-off",
								labelClass = "label label-success",
								labelText = "Activo",
								btnTitle = "Desactivar registro";
							if (res) {
								if (status == 1) {
									labelClass = "label label-danger";
									labelText = "Inactivo";
									iconClass = "fas fa-toggle-on";
									btnTitle = "Activar registro";
								}
								$(that).data("status", status? 0 : 1);
							}							
							$(".btn-toggle-log", $tr).prop("title", btnTitle);
							$("label", $tr).removeClass().addClass(labelClass).text(labelText);
							$(".btn-toggle-log i", $tr).removeClass().addClass(iconClass);
						} catch (e) {
							console.error(e);
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.error("Error::" + errorThrown);
					}
				});
			}
		}
	});
}

function clearFormData () {
	$("#txtNombre").val("");
	$("#txtContra").val("");	
	$("#txtConfirmar").val("");
	$("#cmbPerfil").val("0");	
	$("#idUsuario").val("");
}