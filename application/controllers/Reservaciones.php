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
		date_default_timezone_set("America/Mazatlan");
		$this->form_validation->set_message("required", "Este campo es obligatorio");
	}

	public function index () {
		if (!validarAcceso()) {
			redirect(base_url());
		} else {
			$where['fecha'] = DaTeTime::createFromFormat("d/m/Y", $this->input->post("txtFecha"));
			if (is_bool($where['fecha']))
				$where['fecha'] = new datetime();
			if (getIdPerfil("Cliente") == $this->session->serchos['idPerfil'])
				$where['id_cliente'] = $this->session->serchos['idUsuario'];
			$dia = $where['fecha']->format("d");
			$mes = $where['fecha']->format("m");
			$año = $where['fecha']->format("Y");
			$where['fecha'] = $where['fecha']->format("Y-m-d");
			$data = array( 
				'dia' => $dia,
				'mes' => $mes,
				'año' => $año,
				'titulo' => "Reservaciones",
				'reservaciones' => $this->ReservacionesModelo->listar($where)
			);	
			$this->load->view("reservaciones/mainVista", $data);
		}
	}

	public function ver ($idReservacion = null) {
		if (!validarAcceso()) {
			redirect(base_url());
		} else if (($reservacion = $this->esReserValida($idReservacion)) === false) {
			$this->session->set_flashdata("error", "La reservación no es válida.");
			redirect(base_url("index.php/reservaciones"));
		} else {
			$fecha = DaTeTime::createFromFormat("d/m/Y", $this->input->post("txtFecha"));
			if (is_bool($fecha))
				$fecha = new datetime();
			$data = array(
				'titulo' => "Reservaciones",
				'reservacion' => $reservacion,
				'metodo' => $idReservacion? "actualizar" : "insertar"
			);
			if (getIdPerfil("Cliente") != $this->session->serchos['idPerfil'])
				$data['clientes'] = $this->UsuariosModelo->clientes();
			$this->load->view("reservaciones/formVista", $data);
		}
	}

	public function esReserValida ($idReservacion) {
		if ($idReservacion == null)
			return true;
		$where['id'] = $idReservacion;
		$res = $this->ReservacionesModelo->buscar($where);
		if ($res !== false)
			return $res;
		return false;
	}

	public function insertar () {
		if (!validarAcceso()) {
			redirect(base_url());
		} else if (!$this->formValidation()) {
			$this->session->set_flashdata("error", "Corregir los errores de formulario.");
			redirect(base_url("index.php/reservaciones/ver"));
		} else if (!$this->isValidReser()) {
			$this->session->set_flashdata("error", "No hay mas reservaciones disponibles.".
				"<br>Pruebe cambiar el tipo de mesa ó la hora.");
			redirect(base_url("index.php/reservaciones/ver"));
		} else {
			$fecha = DateTime::createFromFormat("d/m/Y", $post['txtFecha']);
			$post = $this->input->post();
			$reservacion = array(
				'tipo_mesa' => $post['cmbTipoMesa'],
				'fecha' => $post['txtFecha'],
				'hora' => $post['cmbHora']
			);
			if ($this->session->serchos['idPerfil'] != getIdPerfil("Cliente"))
				$reservacion['id_cliente'] = $post['idCliente'];
			else
				$reservacion['id_cliente'] = $this->session->serchos['idUsuario'];
			$this->setFechaHora($reservacion);
			$idHorario = $this->ReservacionesModelo->getIdHorario($reservacion);
			if ($idHorario) {
				$reservacion['id_hora'] = $idHorario;
				$idReservacion = $this->ReservacionesModelo->reservar($reservacion);
				if ($idReservacion) {
					if (isset($post['txtCliente']) and !empty($post['txtCliente'])) {
						$cliente = $post['txtCliente'];
					}
					else {
						$cliente = $this->session->serchos['nombre']." ".
							$this->session->serchos['paterno']." ".
							$this->session->serchos['materno'];
					}
					$res['msgSuccess'] = "Reservación exitosa"
						."<br>Reservación hecha a nombre de <strong>".$cliente."</strong>"
						."<br>Número de reservación: <strong>".$idReservacion."</strong>";
					$res['msg'] = $this->getFormErrors();
					$res['code'] = 1;
					$res['res'] = $this->ReservacionesModelo->buscar(array("id" => $idReservacion));
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
	}

	public function actualizar () {
		if (validarAcceso(true)) {
			$res = array();
			$post = $this->input->post();
			$fecha = DateTime::createFromFormat("d/m/Y", $post['txtFecha']);
			if ($this->formValidation()) {
				$reservacion = array(
					'id' => $post['idReservacion'],
					'tipo_mesa' => $post['cmbTipoMesa'],
					'fecha' => $post['txtFecha'],
					'hora' => $post['cmbHora']
				);
				if ($this->session->serchos['idPerfil'] != getIdPerfil("Cliente"))
					$reservacion['id_cliente'] = $post['idCliente'];
				else
					$reservacion['id_cliente'] = $this->session->serchos['idUsuario'];
				$this->setFechaHora($reservacion);
				$idHorario = $this->ReservacionesModelo->getIdHorario($reservacion);
				if ($idHorario) {
					$reservacion['id_hora'] = $idHorario;
					if ($this->ReservacionesModelo->editar($reservacion)) {
						$res['msgSuccess'] = "Reservación modificada"
							."<br>Reservación hecha a nombre de <strong>".$post['txtCliente']."</strong>"
							."<br>Número de reservación: <strong>".$post['idReservacion']."</strong>";
						$res['msg'] = $this->getFormErrors();
						$res['code'] = 1;
						$res['res'] = $this->ReservacionesModelo->buscar(array("id" => $post['idReservacion']));
					}
					else {
						$res['msg'] = "Error al modificar la reservacion.<br>Intente más tarde";
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
			redirect(base_url());
	}

	private function formValidation () {
		$idPerfil = $this->session->serchos['idPerfil'];
		if ($idPerfil != getIdPerfil("Cliente"))
			$this->form_validation->set_rules("cmbCliente", "Cliente", "required|callback_validarCliente");
		$this->form_validation->set_rules("cmbTipoMesa", "Tipo de mesa", "required|callback_validarMesa");
		$this->form_validation->set_rules("txtFecha", "Fecha", "required|callback_validarFecha");
		$this->form_validation->set_rules("txtHoraInicio", "Hora Inicio", "required|callback_validarHora");
		$this->form_validation->set_rules("txtHoraFin", "Hora Fin", "required|callback_validarHora");
		return $this->form_validation->run();
	}

	public function validarFecha ($fecha) {
		$fecha = DateTime::createFromFormat("d/m/Y", $fecha);
		$valores = explode('/', $fecha->format("d/m/Y"));
		if(count($valores) == 3 && checkdate($valores[1], $valores[0], $valores[2])){
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

	public function validarCliente ($idCliente) {
		$where['id'] = $idCliente;
		$valid = $this->UsuariosModelo->buscar($where);
		if (!$valid)
			$this->form_validation->set_message("validarCliente", "El cliente ingresado no es válido.");
		return $valid;
	}

	public function validarHora ($idTipoHora) {
		$valid = $this->ReservacionesModelo->isTipoHora($idTipoHora);
		if ($valid === false)
			$this->form_validation->set_message("validarTipoHora", "La hora seleccionada no es válida.");
		return $valid;
	}

	public function isValidReser () {
		$idTipoHora = $this->input->post("idTipoHora");
		$idTipoMesa = $this->input->post("idTipoMesa");
		$fecha = DaTeTime::createFromFormat("d/m/Y", $this->input->post("txtFecha"));
		if (is_bool($fecha))
			$fecha = new datetime();
		$fecha = $fecha->format("Y-m-d");
		return $this->ReservacionesModelo->puedoReservar($idTipoMesa, $idTipoHora, $fecha);
	}

	public function cancelar ($idReservacion) {
		if (validarAcceso(true))  {
			$where['id'] = $idReservacion;
			$data['status'] = 0;
			if ($this->ReservacionesModelo->actualizar($data, $where)) {
				$res['code'] = 1;
				$res['msg'] = "Reservación cancelada";
			}
			else {
				$res['code'] = 0;
				$res['msg'] = "No se pudo cancelar la reservación";
			}
			echo json_encode($res);
		}
		else
			redirect(base_url());
	}

	public function verificar () {
		if (!validarAcceso(true)) {
			echo json_encode(array("code" => 0, "msg" => "Acceso denegado"));
		} else if (!$this->formValidation()) {
			echo json_encode(array("code" => -1, "msg" => "Errores de formulario."));
		} else {
			$fecha = DateTime::createFromFormat("d/m/Y", $this->input->post("txtFecha"));
			if (is_bool($fecha))
				$fecha = new datetime();
			$horaInicio = DateTime::createFromFormat("H:i", $this->input->post("txtHoraInicio"));
			if (is_bool($horaInicio))
				$horaInicio = new datetime();
			$horaFin = DateTime::createFromFormat("H:i", $this->input->post("txtHoraFin"));
			if (is_bool($horaFin))
				$horaFin = new datetime();
			$data = array(
				'idMesa' => $this->input->post("cmbTipoMesa"),
				'horaInicio' => $horaInicio->format("H:i:s"),
				'horaFin' => $horaFin->format("H:i:s"),
				'fecha' => $fecha->format("Y-m-d")
			);
			$cantidad = $this->ReservacionesModelo->mesasDisponibles($data);
			echo json_encode(array("code" => 1, 'cantidad' => $cantidad));
		}
	}
}