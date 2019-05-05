<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class ReservacionesModelo extends MY_Model {

	public function __construct () {
		parent::__construct();
	}

	public function mesasOcupadas ($fecha, $hora) {
		$this->db->select("res.id_mesa");
		$this->db->where("res.fecha = DATE('".$fecha."')");
		$this->db->where("res.hora = DATE('".$hora."')");
		$idMesas = $this->db->get("reservaciones res");
		if ($idMesas->num_rows() > 0)
			return $idMesas->result_array();
		return false;
	}

	public function mesas ($reservacion) {
		$idMesas = $this->mesasOcupadas($reservacion['fecha'], $reservacion['hora']);
		if ($idMesas) {
			$this->db->select("mes.*");
			$this->db->join("reservaciones res", "res.id_mesa != mes.id");
			$this->db->where("mes.tipo_mesa", $tipoMesa);
			$this->db->where_not_in("mes.id", $idMesas);
			$mesas = $this->db->get("mesas mes");
			if ($mesas->num_rows() > 0)
				return $mesas->result_array();
		}
		return false;
	}

	public function reservar ($reservacion) {
		date_default_timezone_set('America/Mazatlan');
		$timestamp = new datetime();
		$this->db->set("id_hora", $reservacion['id_hora']);
		$this->db->set("id_cliente", $reservacion['id_cliente']);
		$this->db->set("fecha", $reservacion['fecha']);
		$this->db->set("log_date", $timestamp->format("Y-m-d"));
		$this->db->set("log_time", $timestamp->format("H:i:s"));
		$this->db->set("status", 1);
		$this->db->insert("reservaciones");
		if ($this->db->affected_rows())
			return $this->db->insert_id();
		return false;
	}

	public function getIdHorario ($reservacion) {
		$horarios = $this->getHorariosReservados($reservacion['fecha']);
		$this->db->select("hora.*");
		$this->db->where("hora_inicio = TIME('".$reservacion['hora_inicio']."')");
		$this->db->where("hora_fin = TIME('".$reservacion['hora_fin']."')");
		$this->db->join("mesas mesa", "mesa.id = hora.id_mesa");
		$this->db->where("mesa.status", 1);
		$this->db->where("mesa.tipo_mesa", $reservacion['tipo_mesa']);
		$this->db->where_not_in("hora.id", $horarios);
		$horario = $this->db->get("horas_mesas hora");
		if ($horario->num_rows() > 0)
			return $horario->row_array()['id'];
		return false;
	}

	public function puedoReservar ($reservacion) {
		$this->db->where("fecha = DATE('".$reservacion['fecha']."')");
		$this->db->where("hora_inicio = TIME('".$reservacion['hora_inicio']."')");
		$this->db->where("hora_fin = TIME('".$reservacion['hora_fin']."')");
		$this->db->where("id_hora", $reservacion['id_hora']);
		$reser = $this->db->get("listar_reservaciones");
		return ($reser->num_rows() == 0);
	}

	public function getHorariosMesa ($idMesa) {
		$this->db->where("id_mesa", $idMesa);
		$this->db->where("status", 1);
		$mesas = $this->db->get("horarios_reservaciones");
		if ($mesas->num_rows() > 0)
			return $mesas->result_array();
		return false;
	}

	public function horariosDisponibles ($fecha, $tipoMesa = 1) {
		$horarios = $this->getHorariosReservados($fecha);
		$this->db->select("DISTINCT hora.hora_inicio, hora.hora_fin", false);
		$this->db->join("mesas mesa", "mesa.id = hora.id_mesa");
		$this->db->where("mesa.tipo_mesa", $tipoMesa);
		$this->db->where("mesa.status", 1);
		$this->db->where_not_in("hora.id", $horarios);
		$this->db->order_by("hora.hora_inicio");
		$horarios = $this->db->get("horas_mesas hora");
		if ($horarios->num_rows() > 0)
			return $horarios->result_array();
		return false;
	}

	private function getHorariosReservados ($fecha) {
		$this->db->select("id_hora");
		$this->db->where("fecha = DATE('".$fecha."')");
		$this->db->where("status", 1);
		$horarios = $this->db->get("listar_reservaciones");
		if ($horarios->num_rows() > 0) {
			$ids = array();
			$horarios = $horarios->result_array();
			foreach ($horarios as $horario)
				$ids[] = $horario['id_hora'];
			return $ids;
		}
		return false;
	}
}