$(document).ready( function () {
	$("#btn-add").click( function () {
		toggleMain()
	})

	$("#btn-close").click( function () {
		toggleMain()
	})

	var tablaId = $("table:eq(0)").prop("id")

	$("#" + tablaId).delegate(".btn-edit-log", "click", function () {
		let modulo = getTableLog(this)		
		if (modulo != undefined) {			
			toggleMain()
			setFormLog(modulo)
		}
	})

	$("#" + tablaId).delegate(".btn-toggle-log", "click", function () {
		toggleLog(this)
	})
})

function toggleMain () {
	var $formulario = $("#formulario"),
		$tabla = $("#tabla")
	if ($tabla.css("display") === "none") {
		$tabla.show()
		$formulario.hide()
		clearFormData()
	}
	else {
		$tabla.hide()
		$formulario.show()
	}
}