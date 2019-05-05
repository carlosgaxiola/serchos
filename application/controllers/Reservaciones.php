<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Reservaciones extends MY_Controller {

	public $modulo; 

	public function __construct () {
		parent::__construct();
		$this->load->helper("global_functions_helper");
		$this->load->model("ReservacionesModelo");
		$this->load->model("UsuariosModelo");
		$this->modulo = getModulo("reservaciones");
	}

	public function index () {
		if (validarAcceso()) {
			$where = array("id_perfil" => getIdPerfil("Cliente"));
			$data = array( 
				'titulo' => "Reservaciones",
				'clientes' => $this->UsuariosModelo->listar($where)
			);
			$this->load->view("reservaciones/mainVista", $data);
		}
		else
			show_404();
	}

	public function add () {
		if (validarAcceso(true)) {
			$res = array();
			$post = $this->input->post();
			$fecha = DateTime::createFromFormat("d/m/Y", $post['txtFecha']);
			if ($this->formValidation()) {
				$reservacion = array(
					'tipo_mesa' => $post['cmbTipoMesa'],
					'fecha' => $post['txtFecha'],
					'hora' => $post['cmbHora'],
					'id_cliente' => $post['idCliente']
				);
				$this->setFechaHora($reservacion);
				$idHorario = $this->ReservacionesModelo->getIdHorario($reservacion);
				if ($idHorario) {
					$reservacion['id_hora'] = $idHorario;
					$idReservacion = $this->ReservacionesModelo->reservar($reservacion);
					if ($idReservacion) {
						$res['msgSuccess'] = "Reservación exitosa"
							."<br>Reservacion hecha a nombre de <strong>".$post['txtCliente']."</strong>"
							."<br>Número de reservación: <strong>".$idReservacion."</strong>";
						$res['msg'] = $this->getFormErrors();
						$res['code'] = 1;
						$res['idReservacion'] = $idReservacion;
					}
					else {
						$res['msg'] = "Error al hacer la reservacion.<br>Intente más tarde";
						$res['code'] = -1;
					}
				}
				else {
					$res['code'] = -1;
					$res['msg'] = "No hay mesas disponibles<br>Seleccione otro horario o fecha";
				}
			}
			else {
				$res['msg'] = $this->getFormErrors();
				$res['code'] = 0;
			}
			echo json_encode($res);
		}
		else 
			show_404();
	}

	private function formValidation ($esCliente = false) {
		if ($esCliente) {
			$this->form_validation->set_rules("txtNombre", "Nombre(s)", "trim|required|max_length[45]");
	    	$this->form_validation->set_rules("txtPaterno", "Apellido Paterno",  "trim|required|max_length[45]");
	    	$this->form_validation->set_rules("txtMaterno", "Apellido Materno", "trim|required|max_length[45]");
	    	$this->form_validation->set_rules("txtUsuario", "Usuario", "trim|required|max_length[45]|is_unique[usuarios.usuario]");
	    	$this->form_validation->set_rules("txtContra", "Contraseña", "required");
	    	$this->form_validation->set_rules("txtConfContra", "Repetir Contraseña", "required|matches[txtContra]");
	    	$this->form_validation->set_message("required", "El campo {field} es obligatorio");
	    	$this->form_validation->set_message("is_unique", "El campo {field} ya existe");
	    	$this->form_validation->set_message("matches", "El campo {field} debe coincidir con {param}");
	    	$this->form_validation->set_message("max_length", "Máximo {param} caracteres");
		}
		else {
			$idPerfil = $this->session->extempo['idPerfil'];
			if ($idPerfil != getIdPerfil("Cliente"))
				$this->form_validation->set_rules("txtCliente", "Cliente", "required");
			$this->form_validation->set_rules("cmbTipoMesa", "Tipo de mesa", "required|is_natural_no_zero");
			$this->form_validation->set_rules("txtFecha", "Fecha", "required|callback_validarFecha");
			$this->form_validation->set_rules("cmbHora", "Hora", "required|in_list[1,2,3,4,5]");
			$this->form_validation->set_message("required", "Este campo es obligatorio");
			$this->form_validation->set_message("in_list", "Selecione una hora");
			$this->form_validation->set_message("is_natural_no_zero", "Selecciona un tipo de mesa");
		}
		return $this->form_validation->run();
	}

	private function getFormErrors ($esCliente = false) {
		if ($esCliente) {
			return "txtNombre=".form_error("txtNombre")."&".
	    		"txtPaterno=".form_error("txtPaterno")."&".
	    		"txtMaterno=".form_error("txtMaterno")."&".
	    		"txtUsuario=".form_error("txtUsuario")."&".
	    		"txtContra=".form_error("txtContra")."&".
	    		"txtConfContra=".form_error("txtConfContra");
		}
		else {
			return "txtFecha=".form_error("txtFecha")."&".
				"cmbHora=".form_error("cmbHora")."&".
				"txtCliente=".form_error("txtCliente")."&".
				"cmbTipoMesa=".form_error("cmbTipoMesa");
		}
	}

	public function validarFecha ($fecha) {
		$fecha = DateTime::createFromFormat("d/m/Y", $fecha);
		if ($fecha->format("N") != 7) {
			$this->form_validation->set_message("validarFecha", "Solo se puede reservar los domingos");
			return false;
		}
		$valores = explode('/', $fecha->format("d/m/Y"));
		if(count($valores) == 3 && checkdate($valores[1], $valores[0], $valores[2])){
			date_default_timezone_set('America/Mazatlan');
			$fechaActual = new datetime();
			$fechaActual = strtotime($fechaActual->format("Y-m-d"));
			$fecha = strtotime($fecha->format("Y-m-d"));
			if ($fecha < $fechaActual) {
				$this->form_validation->set_message("validarFecha", "La fecha no puede ser menor a la actual");
				return false;
			}
			return true;
	    }
	    $this->form_validation->set_message("validarFecha", "La fecha no es válida");
		return false;
	}

	public function validarHora ($hora) {
		$horaAbierto = strtotime("09:00");
		$horaCerrado = strtotime("19:00");
		$hora = strtotime($hora);
		if ($hora < $horaAbierto || $hora > $horaCerrado) {
			$this->form_validation->set_message("validarHora", "La hora elegida no es válida");
			return false;
		}
		return true;
	}

	private function setFechaHora (&$reservacion) {
		switch ($reservacion['hora']) {
			case '1':
				$reservacion['hora_inicio'] = "09:00:00";
				$reservacion['hora_fin'] = "11:00:00";
				break;
			case '2':
				$reservacion['hora_inicio'] = "11:00:00";
				$reservacion['hora_fin'] = "13:00:00";
				break;
			case '3':
				$reservacion['hora_inicio'] = "13:00:00";
				$reservacion['hora_fin'] = "15:00:00";
				break;
			case '4':
				$reservacion['hora_inicio'] = "15:00:00";
				$reservacion['hora_fin'] = "17:00:00";
				break;
			case '5':
				$reservacion['hora_inicio'] = "17:00:00";
				$reservacion['hora_fin'] = "19:00:00";
				break;
		}
		$fecha = DateTime::createFromFormat("d/m/Y", $reservacion['fecha']);
		$reservacion['fecha'] = $fecha->format("Y-m-d");
	}

	private function puedoReservar ($reservacion) {
		return $this->ReservacionesModelo->puedoReservar($reservacion);
	}

	public function addCliente () {
		if (validarAcceso(true)) {
			$post = $this->input->post();
			if ($this->formValidation(true)) {
				$cliente = array(
					'nombre' => $post['txtNombre'],
					'paterno' => $post['txtPaterno'],
					'materno' => $post['txtMaterno'],
					'usuario' => $post['txtUsuario'],
					'contra' => $post['txtContra'],
					'id_perfil' => getIdPerfil("Cliente")
				);
				$idUsaurio = $this->UsuariosModelo->insertar($cliente);
				if ($idUsaurio) {
					$res['code'] = 1;
					$res['msg'] = $idUsaurio;
				}
				else {
					$res['code'] = -1;
					$res['msg'] = "No se pudo registrar el cliente";
				}
			}
			else {
				$res['code'] = 0;
				$res['msg'] = $this->getFormErrors(true);
			}
			echo json_encode($res);
		}
		else
			show_404();
	}

	public function horarios () {
		if (validarAcceso(true)) {
			$post = $this->input->post();
			if ($res['code'] = $this->formValidation()) {
				$tipoMesa = $post['cmbTipoMesa'];
				$fecha = DaTeTime::createFromFormat("d/m/Y", $post['txtFecha']);
				$res['data'] = $this->ReservacionesModelo->horariosDisponibles($fecha->format("Y-m-d"), $tipoMesa);
			}
			$res['msg'] = $this->getFormErrors();
			echo json_encode($res);
		}
		else
			show_404();
	}
}