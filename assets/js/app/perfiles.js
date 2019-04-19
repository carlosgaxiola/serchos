$(document).ready( function () {			
	
	//Necesarias
	$("#btn-save").click( function () {
		if ($("#idPerfil").val() === "") {
			add( function () {
				BootstrapDialog.alert({
					title: "Perfil agregado",
					message: "Se registro el modulo correctamente",
					type: BootstrapDialog.TYPE_SUCCESS,					
				});
				toggleMain();
				$("#msg-error").hide();
			})			
		}
		else {
			edit( function () {				
				toggleMain();
				$("#msg-error").hide();
				tabla.draw()
			});
		}
	})

	function add (callback) {
		$.ajax({
			url: base_url + "administrar/perfiles/add",
			type: "POST",
			data: $("#frmPerfil").serialize() + "&fecha=" + getDate(),
			success: function (res) {				
				try  {
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.log("error");								
								break
							default:
								let perfil = getFormLog(res);
								addTableLog(perfil);
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
			url: base_url + "administrar/perfiles/edit",
			type: "POST",
			data: $("#frmPerfil").serialize(),
			success: function (res) {				
				try  {					
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.error("error: "  + res);
								break
							default:
								let perfil = getFormLog(res);								
								editTableLog(perfil);
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
});

//Genericas
function setFormLog (perfil) {
	if (perfil != null && perfil != undefined) {
		$("#idPerfil").val(perfil.id);
		$("#txtNombre").val(perfil.nombre);		
		$("#txtDescripcion").val(perfil.descripcion);
		if (perfil.modulos.length == 0)
			getModulos(perfil.id, setModulos);
		else
			setModulos(perfil.modulos);
		return true;
	}
	else {
		BootstrapDialog.alert({
			title: "Error",
			message: "El modulo que desea editar no existe",
			type: BootstrapDialog.TYPE_DANGER,
			size: BootstrapDialog.SIZE_DANGER				
		})
		return false;
	}
}

function getTableLog (that) {
	let $tr = $(that).parent().parent(), modulos = [];
	if ($tr.data("modulos") != undefined)
		modulos = $tr.data("modulos").split(",");	
	return {
		id: $(that).data("id"),
		nombre: tabla.row($tr).data()[1],
		descripcion: tabla.row($tr).data()[2],
		modulos: modulos
	};
}

function getFormLog (idPerfil) {
	let modulos = [];
	$.each ($("#frmPerfil .checkbox input"), function (index, input) {
		if ($(input).prop("checked"))
			modulos.push($(input).val());
	});
	return {
		id: idPerfil,
		nombre: $("#txtNombre").val(),
		descripcion: $("#txtDescripcion").val(),
		modulos: modulos
	};	
}

function addTableLog (perfil) {	
	let row = tabla.row.add([
		tabla.rows().count() + 1,
		perfil.nombre,
		perfil.descripcion,
		getDate(),
		'<label class="label label-success">Activo</label>',
		'<button type="button" class="btn btn-warning btn-sm btn-edit-log" data-id="' + perfil.id + '" title="Editar registro"><i class="fas fa-edit"></i></button>&nbsp;' +
		'<button type="button" class="btn btn-danger btn-sm btn-toggle-log" data-id="' + perfil.id  + '" title="Desactivar registro" data-status="1"><i class="fas fa-toggle-off"></i></button>'
	]).draw();
	$(row).data("id", perfil.id);	
	$(row).data("modulos", perfil.modulos.join(","));
	clearFormData();
}

function editTableLog (perfil) {
	let Break = {};
	try {		
		$.each($("#tblPerfiles tbody tr"), function (index, tr) {			
			if ($(tr).data("id") == perfil.id) {
				$("td:eq(1)", tr).text(perfil.nombre);
				tabla.row(tr).data()[1] = perfil.nombre;				
				$("td:eq(2)", tr).text(perfil.descripcion);
				tabla.row(tr).data()[2] = perfil.descripcion;
				$(tr).data("modulos", perfil.modulos.join(","));
				throw Break;
			}
		});
	} catch (e) {
		// console.error(e)
	}	
}

function toggleLog (that) {
	let $tr = $(that).parent().parent(),
		nombre = $("td:eq(1)", $tr).text(),
		idPerfil = $(that).data("id"),
		status = $(that).data("status")
		accion = 'desactivar el perfil',
		tipo = BootstrapDialog.TYPE_DANGER,
		okClass = "btn-danger",
		cancelClass = "btn-default";
	if (status == 0) {
		tipo = BootstrapDialog.TYPE_SUCCESS;
		okClass = "btn-success";
		cancelClass = "btn-default";
		accion = "activar el perfil";
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
					url: base_url + "administrar/perfiles/toggle",
					type: "POST",
					data: {
						idPerfil: idPerfil,
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
	$("#txtDescripcion").val("");
	$("#idPerfil").val("");
}

//Especiales
function setModulos (modulos) {	
	$.each ($("#frmPerfil .checkbox input"), function (index, input) {
		$(input).prop("checked", (modulos.indexOf($(input).val()) != -1 ))
	})	
}

function getModulos (idPerfil, callback) {	
	$.ajax({
		url: base_url + "administrar/perfiles/modulos",
		type: "POST",
		data: "idPerfil=" + idPerfil,
		success: function (modulos) {
			try {
				ids = [];
				modulos = JSON.parse(modulos);
				if (typeof modulos !== "boolean")
					for (let modulo of modulos)
						ids.push(modulo['id']);
				callback(ids);
			}
			catch (e) {
				console.error("Error::" + e)
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.error("Error::" + errorThrown)
		}
	})
}