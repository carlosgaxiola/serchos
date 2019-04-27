<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ComandasDetalleModelo extends MY_Model {

	public function __construct () {
		parent::__construct();
		$this->tabla = "detalle_comandas";
		$this->view = "listar_detalles_comandas";
	}

	public function borrarDetalles ($idComanda) {
		$this->db->where("id_comanda", $idComanda);
		$this->db->delete($this->tabla);
	}
}