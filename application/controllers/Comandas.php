<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Comandas extends MY_Controller {

	public $modulo; 

	public function __construct () {
		parent::__construct();
		$this->load->helper("global_functions_helper");
		$this->load->model("ComandasModelo");
		$this->load->model("ComandasDetalleModelo");
		$this->load->model("PlatillosModelo");
		$this->modulo = getModulo("comandas");
	}

	public function index () {
		if (validarAcceso()) {
			$data = array( 'titulo' => "Comandas" );
			$this->load->view("comandas/mainVista", $data);
		}
		else
			show_404();
	}


	public function data () {
		if (validarAcceso(true)) {
			$post = $this->input->post();
			echo json_encode($this->ComandasModelo->getComandas($post['fecha']));
		}
		else {
			show_404();
		}
	}

	public function funcion () {
		echo $this->ComandasModelo->funcion();
	}

	public function actualizar () {
		$puedeEditar = $this->session->extempo['perfil'] == "Gerente" || $this->session->extempo['perfil'] == "Administrador";
		if (validarAcceso(true) and $puedeEditar) {
			$post = $this->input->post();
			$res = array();
			if ($this->formValidation()) {
				$comanda = array(
					'observaciones' => $post['observaciones'],
					'total' => $post['total']
				);
				$this->ComandasModelo->trans_start();
				$this->ComandasModelo->actualizar($comanda, $post['id']);
				$this->ComandasDetalleModelo->borrarDetalles($post['id']);
				foreach ($post['detalles'] as $detalle) {
					$nuevoDetalle = array(
						'id_comanda' => $post['id'],
						'id_platillo' => $detalle['id'],
						'cantidad' => $detalle['cantidad'],
						'precio' => $detalle['precio']
					);
					$this->ComandasDetalleModelo->insertar($nuevoDetalle);
				}
				if ($this->ComandasModelo->trans_status()) {
					$res['code'] = 1;
					$res['msg'] = "Comanda actualizada";
					$this->ComandasModelo->trans_commit();
				}
				else {
					$res['code'] = -1;
					$res['msg'] = "No se pudo actualizar la comanda";
					$this->ComandasModelo->trans_rollback();
				}
			}
			else {
				$res['code'] = 0;
				$res['msg'] = $this->getFormErrors();
			}
			echo json_encode($res);
		}
		else
			show_404();
	}

	public function rechazar ($idComanda) {
		$puedeRechazar = $this->session->extempo['perfil'] == "Gerente" || $this->session->extempo['perfil'] == "Administrador";
		if (validarAcceso(true) and $puedeRechazar) {
			echo json_encode(array("code" => $this->ComandasModelo->rechazar($idComanda)));
		}
		else
			show_404();
	}

	public function atender ($idComanda) {
		$puedeAtender = $this->session->extempo['perfil'] == "Gerente" || $this->session->extempo['perfil'] == "Administrador" || $this->session->extempo['perfil'] == "Cocina";
		if (validarAcceso(true) and $puedeAtender) {
			$data = array("status" => 2);
			echo json_encode(array("code" => $this->ComandasModelo->actualizar($data, $idComanda)));
		}
		else
			show_404();
	}

	public function pagar ($idComanda) {
		$puedePagar = $this->session->extempo['perfil'] == "Gerente" || $this->session->extempo['perfil'] == "Administrador" || $this->session->extempo['perfil'] == "Caja";
		if (validarAcceso(true) and $puedePagar) {
			$data = array("status" => 3); //Status comanda pagada
			echo json_encode(array("code" => $this->ComandasModelo->actualizar($data, $idComanda)));
		}
		else
			show_404();
	}

	private function formValidation () {
		$this->form_validation->set_rules("observaciones", "Observaciones", "required");
		$this->form_validation->set_rules("observaciones", "Observaciones", "required");
		$this->form_validation->set_rules("observaciones", "Observaciones", "required");
		$this->form_validation->set_rules("observaciones", "Observaciones", "required");
		$this->form_validation->set_message("required", "El campo {field} es obligatorio");
		return $this->form_validation->run();
	}

	private function getFormErrors () {
		return "observaciones=".form_error("observaciones");
	}

	public function platillos () {
        if (validarAcceso(true))
            echo json_encode($this->PlatillosModelo->listar());
        else
            show_404();
    }

    public function buscar ($nombrePlatillo = '') {
        if (validarAcceso(true))
            echo json_encode($this->PlatillosModelo->like($nombrePlatillo));
        else
            show_404();
    }
}