<script>
	Array.prototype.remove = function (element) {
	    var index = this.indexOf(element);
	    if (index > -1)
	    	this.splice(index, 1);		    
	}
	$(document).ready( function () {
		var platillos = [],
			comandasBgs = [
				'bg-red',
				'bg-yellow',
				'bg-blue',
				'bg-green'
			],
			comandasGroups = [
				'rechazadas',
				'nuevas',
				'atendidas',
				'pagadas'
			],
			comandasList = {nuevas: [], atendidas: [], pagadas: [], rechazadas: []},
			trNoDetalle = '<tr><td colspan="4" class="text-muted text-center"><h4>Selecciona una comanda</h4></td></tr>';
		<?php if ($this->session->extempo['idPerfil'] == getIdPerfil("Caja")): ?>
			let actionBnts = {
				0: "<button type='button' id='btn-pagar' class='btn btn-success'>Pagar</button>"
			};
		<?php elseif ($this->session->extempo['idPerfil'] == getIdPerfil("Cocina")): ?>
			let actionBnts = {
				0: "<button type='button' id='btn-antender' class='btn btn-primary'>Atender</button>"
			};
		<?php elseif ($this->session->extempo['idPerfil'] == getIdPerfil("Administrador")): ?>
			let actionBnts =  {
				0: "<button type='button' id='btn-pagar' class='btn btn-success'>Pagar</button>",
				1: "<button type='button' id='btn-antender' class='btn btn-primary'>Atender</button>",
				2: "<button type='button' id='btn-rechazar' class='btn btn-danger'>Rechazar</button>",
				3: "<button type='button' data-type='edit' id='btn-editar-comanda' class='btn btn-warning'>Editar</button>"
			}
		<?php elseif ($this->session->extempo['idPerfil'] == getIdPerfil("Gerente")): ?>
			let actionBnts = {
				0: "<button type='button' id='btn-pagar' class='btn btn-success'>Pagar</button>",
				1: "<button type='button' id='btn-rechazar' class='btn btn-danger'>Rechazar</button>",
				2: "<button type='button' data-type='edit' id='btn-editar-comanda' class='btn btn-warning'>Editar</button>"
			}
		<?php endif; ?>
		$("#tbl-detalle tbody").html(trNoDetalle)
		
		$("#btn-save").click( function () {
			if ($("#idMesa").val() == "")
				add()
			else
				edit()
		})
		
		for (let index in actionBnts)
			$("#default-buttons .form-group").append(actionBnts[index] + "&nbsp;")

		function platillo ( e ) { buscarPlatillo($(this).val()) }

		function setDetalles (comanda) {
			if (comanda && comanda.detalles) {
				$("#tbl-detalle tbody").empty()
				for (let detalle of comanda.detalles) {
					$("#tbl-detalle tbody")
						.append($("<tr></tr>")
							.append("<td class='platillo-nombre'>" + detalle.platillo + "</td>")
							.append("<td class='cantidad'>" + detalle.cantidad + "</td>")
							.append("<td class='precio'>" + detalle.precio + "</td>")
							.append("<td class='subtotal'>" + (detalle.cantidad * detalle.precio) + "</td>")
						)
				}
				$("#fecha-comanda").text(moment(comanda.fecha).format("DD/MM/YYYY"))
				$("#hora-comanda").text(comanda.hora)
				$("#total-comanda").text(comanda.total)
				$("#observaciones").text(comanda.observaciones)
				$("#btn-antender").prop("disabled", comanda.status == 2)
				$("#btn-pagar").prop("disabled", comanda.status != 2)
				$("#btn-rechazar").prop("disabled", comanda.status == 0 || comanda.status == 2)
				$("#btn-editar-comanda").prop("disabled", comanda.status == 3 || comanda.status == 0)
				$("#tabla-detalle-comanda").data("comanda", comanda)
			}
			else
				throw "Error, comanda no valida";
		}

		$("#btn-editar-comanda").click( function () {
			let comanda = $("#tabla-detalle-comanda").data("comanda");			
			if (comanda) {
				if ($(this).data("type") == "edit") {
					$(this).text("Guardar")
					$(this).data("type", "save")
					let celdas = $("#tbl-detalle tbody tr td").toArray();
					let contadorColumnas = 0;
					for (let index in celdas) {
						let text = $(celdas[index]).text()
						if ((index % 4) == 0)
							$(celdas[index]).html("<input type='text' list='platillos' class='form-control' value='" + text + "' >")
						else if (contadorColumnas != 3) {
							$(celdas[index]).html("<input type='text' class='form-control' value='" + text + "' style='text-align: right;'>")
						}
						contadorColumnas++;
						if (contadorColumnas == 4)
							contadorColumnas = 0;						
					}
					let $observaciones = $("#tabla-detalle-comanda #observaciones");
					$observaciones.html("<input type='text' class='form-control' value='" +
						$observaciones.text() + "'>")
					$("#tbl-detalle tbody tr .platillo-nombre-input:first").focus()
				}
				else if ($(this).data("type") == "save") {
					let detalles = [], detalle = {}, contador = 0,
						celdas = $("#tbl-detalle tbody tr td").toArray();
					
					for (let index in celdas) {
						let $td = $(celdas[index])
						contador++;
						if ($td.hasClass("platillo-nombre"))
							detalle['platillo'] = $td.children("input").val();
						else if ($td.hasClass("cantidad"))
							detalle['cantidad'] = $td.children("input").val();
						else if ($td.hasClass("precio"))
							detalle['precio'] = $td.children("input").val();
						if (contador == 4) {
							detalles.push( detalle ) 
							detalle = {}
							contador = 0;
						}
					}

					if (validarDetalles(detalles)) {
						$("#tbl-detalle tbody").prop("contenteditable", false)
						$(this).text("Editar")
						$(this).data("type", "edit")
						let filas = $("#tbl-detalle tbody tr").toArray(), total = 0;
						for (let index in filas)
							total += parseInt(detalles[index].cantidad) * parseInt(detalles[index].precio)

						$observaciones = $("#tabla-detalle-comanda #observaciones");
						
						nuevaComanda = {
							id: comanda.id,
							hora: $("#hora-comanda").text(),
							fecha: moment($("#fecha-comanda").text(), "DD/MM/YYYY").format("YYYY-MM-DD"),
							total: total,
							observaciones: $observaciones.children("input").val(),
							detalles: detalles,
							id_mesa: comanda.id_mesa,
							id_mesero: comanda.id_mesero,
							status: comanda.status
						}

						$.ajax({
							url: base_url + "comandas/actualizar",
							type: "POST",
							data: nuevaComanda,
							success: function (res) {
								try {
									res = JSON.parse(res)
									if (res.code == 1) {
										BootstrapDialog.alert({
											title: "Comanda editada",
											message: "La comanda se actualizó con éxito",
											type: BootstrapDialog.TYPE_SUCCESS,
											size: BootstrapDialog.SIZE_SMALL
										})
										setDetalles(nuevaComanda)
										actualizarComandaLista(nuevaComanda)
									}
									else if (res.code == 0) {
										errorDialog(res.msg)
									}
									else  if (res.code == -1) {
										errorDialog(res.msg)	
										setDetalles(comanda)
									}
								} catch ( e ) { console.error( e ) }
							}
						})
					}
				}
				else {
					errorDialog("Botón no valido")
				}

			}
			else
				BootstrapDialog.alert({
					title: "No hay comanda",
					message: "Selecciona una comanda primero",
					type: BootstrapDialog.TYPE_DANGER,
					size: BootstrapDialog.SIZE_SMALL
				})
		})

		$("#btn-rechazar").click( function () {
			let comanda = $("#tabla-detalle-comanda").data("comanda")
			if (comanda) {
				BootstrapDialog.confirm({
					title: "Rechazar comanda",
					message: "¿Desea rechazar la comanda?",
					type: BootstrapDialog.TYPE_WARNING,
					btnOKLabel: "Sí",
					btnOKClass: "btn-danger",
					btnCancelLabel: "No",
					callback: function ( res ) {
						if (res) {
							$.ajax({
								url: base_url + "comandas/rechazar/" + comanda.id,
								success: function ( res ) {
									try {
										res = JSON.parse(res) 
										if (res.code == 1) {
											BootstrapDialog.alert({
												title: "Cancelada",
												message: "Comanda cancelada",
												type: BootstrapDialog.TYPE_PRIMARY,
												size: BootstrapDialog.SIZE_SMALL
											})
											delComanadList(comanda)
											comanda.status = 0;
											addComandaList(comanda)
										}
										else {
											errorDialog("Error al cancelar la comanda")
										}
									}
									catch ( e ) { console.error( e ) }
								}
							})
						}
					}
				})
			}
			else
				errorDialog("Selecciona un comanda")
		})

		$("#btn-antender").click( function () {
			let comanda = $("#tabla-detalle-comanda").data("comanda");
			if (comanda) {
				BootstrapDialog.confirm({
					title: "Atender",
					message: "Se atendera la comanda de la <strong>mesa " + comanda.id_mesa + "</strong>",
					type: BootstrapDialog.TYPE_PRIMARY,
					btnOKLabel: "Aceptar",
					btnOKClass: "btn-primary",
					btnCancelLabel: "Cancelar",
					callback: function ( res ) {
						if (res) {
							$.ajax({
								url: base_url + "comandas/atender/" + comanda.id,
								success: function ( res ) {
									try {
										res = JSON.parse(res)
										if (res.code == 1) {
											delComanadList(comanda)
											comanda.status = 2 //Status comanda atendia
											addComandaList(comanda)
										}
										else if (res.code == 0) {
											errorDialog("Error al atender la comanda")
										}
									}
									catch ( e ) { console.error(e) }
								}
							})
						}
					}
				})
			}
			else {
				errorDialog("Selecciona un comanda")
			}
		})

		$("#btn-pagar").click( function () {
			let comanda = $("#tabla-detalle-comanda").data("comanda");
			if (comanda) {
				BootstrapDialog.confirm({
					title: "Pagar",
					message: "Se pagara la comanda de la <strong>mesa " + comanda.id_mesa + "</strong>",
					type: BootstrapDialog.TYPE_PRIMARY,
					btnOKLabel: "Aceptar",
					btnOKClass: "btn-primary",
					btnCancelLabel: "Cancelar",
					callback: function ( res ) {
						if (res) {
							$.ajax({
								url: base_url + "comandas/pagar/" + comanda.id,
								success: function ( res ) {
									try {
										res = JSON.parse(res)
										if (res.code == 1) {
											delComanadList(comanda)
											comanda.status = 3 //Status comanda pagada
											addComandaList(comanda)
										}
										else if (res.code == 0) {
											errorDialog("Error al pagar la comanda")
										}
									}
									catch ( e ) { console.error(e) }
								}
							})
						}
					}
				})
			}
			else {
				errorDialog("Selecciona una comanda")
			}
		})

		$("#tbl-detalle").delegate(".platillos", "keyup", function ( e ) {
			let val = $(this).val()
			buscarPlatillo(nombre)
		})

		function validarDetalles (detalles) {
			let comanda = $("#tabla-detalle-comanda").data("comanda")
			if (detalles) {
				let noValidos = [];
				for (let detalle of detalles) {
					let esInvalido = true;
					for (let platillo of platillos) {	
						if (detalle.platillo.toLowerCase() == platillo.nombre.toLowerCase()) {
							detalle['id'] = platillo.id
							esInvalido = false;
						}
					}
					if (esInvalido)
						noValidos.push(detalle)
				}
				if (noValidos.length != 0) {
					BootstrapDialog.alert({
						title: "Platillo(s) inválido(s)",
						message: function ( dialog ) {
							let msg = "Los siguientes platillos no existen";
							for (let noValido of noValidos) { 
								msg += "<br><li><strong>" + noValido.platillo + "</strong></li>" 
							}
							return msg;
						},
						type: BootstrapDialog.TYPE_DANGER,
						size: BootstrapDialog.TYPE_SMALL
					})
					return false;
				}
				return true;
			}
			else
				errorDialog("Detalles no válidos")
		}

		function buscarPlatillo ( nombre = '' ) {
			$.ajax({
				url: "comandas/buscar/" + nombre,
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if ( res ) {
							$("#platillos").html("");
							$.each(res, function (index, platillo) {
								$("#platillos")
									.append($("<option></option>")
										.val(platillo.nombre)
										.data("id-platillo", platillo.id)
									)
							})
						}
					}
					catch ( e ) {
						console.error(e)
					}
				}
			})
		}		

		function getPlatillos () {
			$.ajax({
				url: base_url + "comandas/platillos",
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res) { platillos = res }
					}
					catch ( e ) { console.error(e) }
				}
			})
		}

		function addComandaList (comanda) {
			if (comanda) {
				let tmpl = $("#comanda-tmpl").tmpl()
				tmpl.find("#bg-comanda").addClass(comandasBgs[comanda.status])
				tmpl.find("#mesa-numero").text("Mesa " + comanda.id_mesa)
				tmpl.find("#platillos-cantidad").text("Platillos " + comanda.detalles.length)
				tmpl.data("comanda", comanda)
				tmpl.data("id", comanda.id)
				tmpl.click( function () {
					let comanda = $(this).data("comanda")
					setDetalles(comanda)
				})
				$("#" + comandasGroups[comanda.status])
					.find(".pre-scrollable")
					.append(tmpl)
					.find(".default-message").hide()
				let cantidad = parseInt($("#" + comandasGroups[comanda.status] + "-tab a sup").text()) || 0;
				cantidad++;
				$("#" + comandasGroups[comanda.status] + "-tab a sup").text(cantidad)
				comandasList[comandasGroups[comanda.status]].push(comanda.id)
			}
			else
				throw "Error, comanda no válida";
		}

		function delComanadList (comandaDel) {
			let comandas = $("#" + comandasGroups[comandaDel.status] + " .comanda").toArray(),
				totalComandas = comandas.length;
			for (let comanda of comandas)
				if ($(comanda).data("id") == comandaDel.id) {
					$(comanda).remove()
					totalComandas--;
					if (totalComandas == 0) {
						$("#" + comandasGroups[comandaDel.status] + " .default-message").show()
						totalComandas = ''
					}
					$("#" + comandasGroups[comandaDel.status] + "-tab a sup").text(totalComandas)
					comandasList[comandasGroups[comandaDel.status]].remove(comandaDel.id)
					break
				}
		}

		function init () {
			$.ajax({
				url: base_url + "comandas/data",
				data: { fecha: moment().format("YYYY-MM-DD") },
				type: "POST",
				success: function ( res ) {
					try {
						res = JSON.parse(res)
						if (res) {
							refreshComandas(res)
						}
					}
					catch ( e ) {
						console.error(e)
					}
				}
			})
		}

		function refreshComandas (comandas) {
			if (comandas) {
				for (let index in comandas) {
					if (!isNaN(index)) {
						let comanda = comandas[index]
						if (comandasList[comandasGroups[comanda.status]].indexOf(comanda.id) == -1) {
							addComandaList(comanda)
							if (comanda.status == 0) {
								comanda.status = 3;
								delComanadList(comanda)
							}
							if (comanda.status == 3) {
								comanda.status = 2;
								delComanadList(comanda)
							}
							if (comanda.status == 2) {
								comanda.status = 1;
								delComanadList(comanda)
							}
						}
					}
				}
				$("#total-comandas").text(comandas.length)
			}
			else
				throw "Error las comandas no son validas";
		}

		init()
		buscarPlatillo()
		getPlatillos()

		setInterval(init, 1000 * 5)
	})
</script>