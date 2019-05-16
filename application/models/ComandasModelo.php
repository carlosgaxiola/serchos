<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ComandasModelo extends MY_Model {

	public function __construct () {
		parent::__construct();
		$this->tabla = "comandas";
		$this->view = "listar_comandas";
	}

	public function getComandas ($fecha) {
		$this->db->where("fecha", $fecha);
		$this->db->order_by("hora");
		$comandas = $this->db->get("comandas");
		if ($comandas->num_rows() > 0)  {
			$comandas = $comandas->result_array();
			foreach ($comandas as &$comanda) {
				$comanda['detalles'] = $this->getDetallesComanda($comanda['id']);
			}
			return $comandas;
		}
		return false;
	}

	public function getDetallesComanda ($idComanda) {
		$this->db->select("det.*, pla.nombre AS platillo");
		$this->db->join("platillos pla", "pla.id = det.id_platillo");
		$this->db->where("id_comanda", $idComanda);
		$detalles = $this->db->get("detalle_comandas det");
		if ($detalles->num_rows() > 0)
			return $detalles->result_array();
		return false;
	}
 
	public function funcion () {
		$this->db->query("INSERT INTO comandas SET id_mesero = 3, id_mesa = 1, fecha = NOW(), hora = NOW(), STATUS = 1, observaciones = 'Sin observaciones'");
		$idComanda = $this->db->insert_id();
		$precio1 = $this->db->query("SELECT precio FROM platillos WHERE id = 1")->row()->precio;
		$precio2 = $this->db->query("SELECT precio FROM platillos WHERE id = 2")->row()->precio;
		$this->db->query("INSERT INTO detalle_comandas SET id_comanda = $idComanda, id_platillo = 1, precio = $precio1, cantidad = 1");
		$this->db->query("INSERT INTO detalle_comandas SET id_comanda = $idComanda, id_platillo = 2, precio = $precio2, cantidad = 2");
		$total = $this->getTotal($idComanda);
		$this->db->query("UPDATE comandas SET total = $total WHERE id = $idComanda");
		echo "finado";
	}

	public function getTotal ($idComanda) {
		$this->db->where("id_comanda", $idComanda);
		$detalles = $this->db->get("detalle_comandas");
		if ($detalles->num_rows() > 0) {
			$total = 0.0;
			foreach ($detalles->result_array() as $detalle) {
				$total += $detalle['precio'] * $detalle['cantidad'];
			}
			return $total;
		}
		return false;
	}

	public function rechazar ($idComanda) {
		if (!empty($idComanda)) {
			$this->db->where("id", $idComanda);
			$this->db->set("status", 0); //Status comanda rechazada
			$this->db->update($this->tabla);
			return "1";
		}
		return "0";
	}
}