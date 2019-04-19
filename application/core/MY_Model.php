<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function listar ($tabla, $extras = null, $status = -1) {	
		if ($extras != null)
			$this->db->select(implode(",", $extras));
		if ($status != -1)
			$this->db->where($tabla.".status", $status);
		$this->db->select($tabla.".*");
		$res = $this->db->get($tabla);		
		if ($res->num_rows() > 0)
			return $res->result_array();
		return false;
	}

	public function insertar ($tabla, $data) {
		$this->db->insert($tabla, $data);		
		if ($this->db->affected_rows() > 0)
			return $this->db->insert_id();
		return false;
	}

	public function actualizar ($tabla, $id, $data, $campo = 'id') {
		if (is_array($id))
			foreach ($id as $index => $value)
				$this->db->where($index, $value);			
		else
			$this->db->where($campo, $id);
		$this->db->update($tabla, $data);
		return ($this->db->affected_rows() > 0);		
	}

	public function buscar ($tabla, $valor, $campo = 'id') {
		if (is_array($valor))
			foreach ($valor as $index => $value)
				$this->db->where($index, $value);
		else
			$this->db->where($campo, $valor);			
		$res = $this->db->get($tabla);		
		if ($res->num_rows() == 1)
			return $res->row_array();
		else if ($res->num_rows() > 1)
			return $res->result_array();
		return false;
	}

	public function alternar ($tabla, $id, $status) {
		$this->db->where("id", $id);
		$this->db->set("status", $status != 1);
		$this->db->update($tabla);
		return ($this->db->affected_rows() > 0);
	}

	public function join ($tabla, $join, $tipo = 0) {
		if ($tipo != 0)	$this->db->join($tabla, $join, $tipo);
		else $this->db->join($tabla, $join);
		return $this;
	}

	public function borrar ($tabla, $valor, $campo = 'id') {
		$this->db->where($campo, $valor);
		$this->db->delete($tabla);
		return ($this->db->affected_rows() > 0);
	}
}
