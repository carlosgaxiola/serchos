<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportesModelo extends MY_Model {

	public function __construct () {
		parent::__construct();
		$this->tabla = "usuarios";
		$this->view = "usuarios";
	}
	
	public function platillos ($fecha = '') {
		date_default_timezone_set('America/Mazatlan');
		$fecha = new datetime($fecha);
		$this->db->select("pla.*, SUM(cantidad) AS cantidad");
		$this->db->join("detalle_comandas det", "det.id_platillo = pla.id");
		$this->db->join("comandas com", "com.id = det.id_comanda");
		$this->db->where("com.fecha = DATE('".$fecha->format("Y-m-d")."')");
		$this->db->group_by("pla.id");
		$platillos = $this->db->get("platillos pla");
		if ($platillos->num_rows() > 0)
			return $platillos->result_array();
		return false;
	}

	public function comandas ($inicio = '', $fin = '') {
		date_default_timezone_set('America/Mazatlan');
		if (!empty($inicio) and !empty($fin)) {
			$inicio = new datetime($inicio);
			$this->db->where("fecha >= DATE('".$inicio->format("Y-m-d")."')");
			$fin = new datetime($fin);
			$this->db->where("fecha <= DATE('".$fin->format("Y-m-d")."')");
		}
		else if (!empty($inicio) and empty($fin)) {
			$inicio = new datetime($inicio);
			$this->db->where("fecha = DATE('".$inicio->format("Y-m-d")."')");
		}
		else {
			$this->db->where("fecha = DATE(NOW())");	
		}
		$this->db
			->group_start()
				->where("com.status", 3)
				->or_where("com.status", 0)
			->group_end();
		$this->db->join("usuarios usu", "usu.id = com.id_mesero");
		$this->db->join("mesas mesa", "mesa.id = com.id_mesa");
		$this->db->select("com.*");
		$this->db->select("CONCAT(usu.nombre, ' ', usu.paterno, ' ', usu.materno) AS mesero");
		$this->db->select("IF(mesa.tipo_mesa = 1, 'Mesa para 2', 'Mesa para 4') AS mesa");
		$comandas = $this->db->get("comandas com");
		if ($comandas->num_rows() > 0)
			return $comandas->result_array();
		return false;
	}
}