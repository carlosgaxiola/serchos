$(document).ready( function () {

	//Generico
	$("#btn-save").click( function () {
		if ($("#idCliente").val() === "") {
			add( function () {
				BootstrapDialog.alert({
					title: "Cliente agregado",
					message: "Se registro el cliente correctamente",
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
			url: base_url + "administrar/clientes/add",
			type: "POST",
			data: $("#frmCliente").serialize() + "&fecha=" + getDate(),
			success: function (res) {				
				try  {
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.log("error");								
								break
							default:
								let modulo = getFormLog(res);
								addTableLog(modulo);
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
			url: base_url + "administrar/clientes/edit",
			type: "POST",
			data: $("#frmCliente").serialize(),
			success: function (res) {				
				try  {					
					if (!isNaN(parseInt(res)))
						switch (res) {
							case 0:
								console.error("error: "  + res);
								break
							default:
								let modulo = getFormLog(res);
								editTableLog(modulo);
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
function setFormLog (cliente) {
	if (cliente != null && cliente != undefined) {
		$("#idCliente").val(cliente.id);
		$("#txtNombre").val(cliente.nombre);
		$("#txtTelefono").val(cliente.telefono);
		$("#txtDomicilio").val(cliente.domicilio);
		return true;
	}
	else {
		BootstrapDialog.alert({
			title: "Error",
			message: "El cliente que desea editar no existe",
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
		domicilio: tabla.row($tr).data()[2],
		telefono: tabla.row($tr).data()[3]		
	};
}

function getFormLog (idCliente) {
	cliente = {
		id: idCliente,
		nombre: $("#txtNombre").val(),
		domicilio: $("#txtDomicilio").val(),
		telefono: $("#txtTelefono").val()		
	}	
	return cliente;
}

function addTableLog (cliente) {
	let row = tabla.row.add([
		tabla.rows().count() + 1,
		cliente.nombre,
		cliente.domicilio,
		cliente.telefono,		
		getDate(),
		'<label class="label label-success">Activo</label>',
		'<button type="button" class="btn btn-warning btn-sm btn-edit-log" data-id="' + cliente.id + '" title="Editar registro"><i class="fas fa-edit"></i></button>&nbsp;' +
		'<button type="button" class="btn btn-danger btn-sm btn-toggle-log" data-id="' + cliente.id  + '" title="Desactivar registro" data-status="1"><i class="fas fa-toggle-off"></i></button>'
	]).draw();
	$(row).data("id", cliente.id);	
	clearFormData();
}

function editTableLog (cliente) {
	let Break = {};
	try {		
		$.each($("#tblClientes tbody tr"), function (index, tr) {				
			if ($(tr).data("id") == cliente.id) {
				$("td:eq(1)", tr).text(cliente.nombre);
				tabla.row(tr).data()[1] = cliente.nombre;
				$("td:eq(2)", tr).text(cliente.domicilio);
				tabla.row(tr).data()[2] = cliente.domicilio;
				$("td:eq(3)", tr).text(cliente.telefono);
				tabla.row(tr).data()[3] = cliente.telefono;							
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
		idCliente = $(that).data("id"),
		status = $(that).data("status")
		accion = 'dar de baja al cliente',
		tipo = BootstrapDialog.TYPE_DANGER,
		okClass = "btn-danger",
		cancelClass = "btn-default";
	if (status == 0) {
		tipo = BootstrapDialog.TYPE_SUCCESS;
		okClass = "btn-success";
		cancelClass = "btn-default";
		accion = "dar de alta al cliente";
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
					url: base_url + "administrar/clientes/toggle",
					type: "POST",
					data: {
						idCliente: idCliente,
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
	$("#txtDomicilio").val("");	
	$("#txtTelefono").val("");		
	$("#idCliente").val("");
}