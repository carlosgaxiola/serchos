$(document).ready( function () {				

	//Generico
	$("#btn-save").click( function () {
		if ($("#idServicio").val() === "") {
			add( function () {
				BootstrapDialog.alert({
					title: "Servicio agregado",
					message: "Se registro el servicio correctamente",
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
			url: base_url + "administrar/servicios/add",
			type: "POST",
			data: $("#frmServicio").serialize() + "&fecha=" + getDate(),
			success: function (res) {				
				try  {
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.log("error");								
								break
							default:
								let servicio = getFormLog(res);
								addTableLog(servicio);
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
			url: base_url + "administrar/servicios/edit",
			type: "POST",
			data: $("#frmServicio").serialize(),
			success: function (res) {				
				try  {					
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.error("error: "  + res);
								break
							default:
								let servicio = getFormLog(res);
								editTableLog(servicio);
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

//Generico
function setFormLog (servicio) {
	if (servicio != null && servicio != undefined) {
		$("#idServicio").val(servicio.id);
		$("#txtNombre").val(servicio.nombre);
		$("#txtCosto").val(servicio.costo);		
		return true;
	}
	else {
		BootstrapDialog.alert({
			title: "Error",
			message: "El servicio que desea editar no existe",
			type: BootstrapDialog.TYPE_DANGER,
			size: BootstrapDialog.SIZE_DANGER				
		})
		return false;
	}
}

function getTableLog (that) {
	let $tr = $(that).parent().parent();
	return {
		id: $(that).data("id"),
		nombre: tabla.row($tr).data()[1],
		costo: tabla.row($tr).data()[2],		
	};
}

function getFormLog (idServicio) {
	servicio = {
		id: idServicio,
		nombre: $("#txtNombre").val(),
		costo: $("#txtCosto").val()		
	}	
	return servicio;
}

function addTableLog (servicio) {
	let row = tabla.row.add([
		tabla.rows().count() + 1,
		servicio.nombre,
		servicio.costo,
		getDate(),
		'<label class="label label-success">Activo</label>',
		'<button type="button" class="btn btn-warning btn-sm btn-edit-log" data-id="' + servicio.id + '" title="Editar registro"><i class="fas fa-edit"></i></button>&nbsp;' +
		'<button type="button" class="btn btn-danger btn-sm btn-toggle-log" data-id="' + servicio.id  + '" title="Desactivar registro" data-status="1"><i class="fas fa-toggle-off"></i></button>'
	]).draw();
	$(row).data("id", servicio.id);	
	clearFormData();
}

function editTableLog (servicio) {
	let Break = {};
	try {		
		$.each($("#tblServicios tbody tr"), function (index, tr) {
			if ($(tr).data("id") == servicio.id) {
				$("td:eq(1)", tr).text(servicio.nombre);
				tabla.row(tr).data()[1] = servicio.nombre;
				$("td:eq(2)", tr).text(servicio.costo);
				tabla.row(tr).data()[2] = servicio.costo;				
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
		idServicio = $(that).data("id"),
		status = $(that).data("status")
		accion = 'dar de baja el servicio',
		tipo = BootstrapDialog.TYPE_DANGER,
		okClass = "btn-danger",
		cancelClass = "btn-default";
	if (status == 0) {
		tipo = BootstrapDialog.TYPE_SUCCESS;
		okClass = "btn-success";
		cancelClass = "btn-default";
		accion = "dar de alta el servicio";
	}		
	BootstrapDialog.confirm({
		title: "Cambiar estado",
		message: "¿Desea " + accion + " " + nombre + "?",
		type: tipo,
		size: BootstrapDialog.SIZE_SMALL,
		btnOKLabel: "Sí",
		btnOKClass: okClass,
		btnCancelLabel: "No",
		btnCancelClass: cancelClass,
		callback: function (res) {
			if (res) {
				$.ajax({
					url: base_url + "administrar/servicios/toggle",
					type: "POST",
					data: {
						idServicio: idServicio,
						status: status
					},
					success: function (res) {						
						try {
							res = JSON.parse(res);
							let iconClass = "fas fa-toggle-off",
								labelClass = "label label-success",
								labelText = "Activo",
								btnTitle = "Baja";
							if (res) {
								if (status == 1) {
									labelClass = "label label-danger";
									labelText = "Inactivo";
									iconClass = "fas fa-toggle-on";
									btnTitle = "Alta";
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
	$("#txtCosto").val("");		
	$("#idCosto").val("");
}