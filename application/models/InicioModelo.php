<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class InicioModelo extends MY_Model {

	public function __construct () {
		parent::__construct();
	}

	public function login ($usuario, $contra) {		
		$this->db->where("usuario", $usuario);
		$this->db->where("contra", $contra);
		$this->db->where("status", 1);		
		$registro = $this->db->get("listar_usuarios");
		if ($registro->num_rows() > 0)
			return $registro->row_array();
		return false;
	}
}