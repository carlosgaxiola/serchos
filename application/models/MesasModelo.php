<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MesasModelo extends MY_Model {

	public function __construct () {
		parent::__construct();
		$this->tabla = "mesas";
		$this->view = "mesas";
	}

	public function totalMesas ($tipo = 0) {
		if ($tipo != 0)
			$this->db->where("tipo_mesa", $tipo);
		$this->db->where("status > ", 0);
		$this->db->select("COUNT(*) AS total");
		$res = $this->db->get($this->view);
		if ($res->num_rows())
			return $res->row()->total;
		return false;
	}
}