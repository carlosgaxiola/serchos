<script>
	$(document).ready( function () {
		
		if ($(".hora")) {
			$(".hora").timepicker({
				showMeridian: false
			})
		}

		<?php if (isset($dia) && isset($mes) && isset($año)): ?>
			$("#txtFecha").datepicker({
				format: "dd/mm/yyyy",
				autoclose: true,
				todayHighlight: true
			}).datepicker("setDate", new Date(<?php echo $año.",".$mes.",".$dia ?>));
		<?php endif; ?>
	});

	var verificar = function () {
			let cliente = true;
			<?php if (getIdPerfil("Cliente") != $this->session->serchos['idPerfil']): ?>
				cliente = $("#cmbCliente").val() == 0;
			<?php endif; ?>
			if ( cliente ||
				$("#cmbTipoMesa").val() == 0 ||
				$("#txtFecha").val() == "" ||
				$("#txtHoraInicio").val() == "" ||
				$("#txtHoraFin").val() == "") {
				errorDialog("Faltan elementos en el formulario", "Llenar formulario");
			} else {
				$.ajax({
					url: base_url + "reservaciones/verificar",
					type: "POST",
					data: $("#frm-reservacion").serialize(),
					success: function (answ) {
						try {
							answ = JSON.parse(answ)
							if (answ.code != 1)
								errorDialog(answ.msg);
							$("#cantidad-sugerida").text((answ.cantidad || 0) + " disponibles");
						} catch ( e ) {
							console.error(e)
						}
					}
				})
			}
		}
</script>