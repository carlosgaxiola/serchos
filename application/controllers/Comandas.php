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

	public function preparar ($idComanda) {
		$puedeAtender = $this->session->extempo['perfil'] == "Gerente" || $this->session->extempo['perfil'] == "Administrador" || $this->session->extempo['perfil'] == "Cocina";
		if (validarAcceso(true) and $puedeAtender) {
			$data['status'] = 2;
			$answ["code"] = $this->ComandasModelo->actualizar($data, $idComanda);
			if ($answ['code']) {
				$data['status'] = 3;
				$answ['code_detalle'] = $this->ComandasDetalleModelo->actualizar($data, $idComanda, 'id_comanda');
			}
			echo json_encode($answ);
		}
		else
			show_404();
	}

	public function listo ($idComanda, $idPlatillo) {
		$puedePreparar = $this->session->extempo['idPerfil'] == getIdPerfil("Administrador") ||
			$this->session->extempo['idPerfil'] == getIdPerfil("Cocina");
		if (validarAcceso(true) and $puedePreparar) {
			$data['status'] = 3;
			$where = array("id_comanda" => $idComanda, 'id_platillo' => $idPlatillo);
			echo json_encode(array("code" => $this->ComandasDetalleModelo->actualizar($data, $where)));
		}
		else
			show_404();
	}
	public function entregar ($idComanda) {
		$puedeEntregar = $this->seesion->extempo['perfil'] = getIdPerfil("Gerente") || 
			$this->session->extempo['perfil'] == getIdPerfil("Administrador") ||
			$this->session->extempo['perfil'] == getIdPerfil("Mesero");
		if (validarAcceso(true) and $puedeEntregar) {
			$data['status'] = 3;
			echo json_encode(array("code" => $this->ComandasModelo->actualizar($data, $idComanda)));
		}
	}

	public function pagar ($idComanda) {
		$puedePagar = $this->session->extempo['idPerfil'] == getIdPerfil("Gerente") ||
			$this->session->extempo['idPerfil'] == getIdPerfil("Administrador") ||
			$this->session->extempo['idPerfil'] == getIdPerfil("Caja");
		if (validarAcceso(true) and $puedePagar) {
			$data['status'] = 4; //Status comanda pagada
			echo json_encode(array("code" => $this->ComandasModelo->actualizar($data, $idComanda)));
		}
		else
			show_404();
	}

	private function formValidation ($esPlatillo = false) {
		if ($esPlatillo) {
			$this->form_validation->set_rules("platillo", "Platillo", "required");
			$this->form_validation->set_rules("cantidad", "Cantidad", "required|numeric");
			$this->form_validation->set_rules("precio", "Precio", "required|numeric");
			$this->form_validation->set_message("numeric", "El campo {field} debe ser solo números");
		}
		else {
			$this->form_validation->set_rules("observaciones", "Observaciones", "required|max_length[255]");
			$this->form_validation->set_message("max_length", "El campo {field} debe tener un máximo de {param} caracteres");
		}
		$this->form_validation->set_message("required", "El campo {field} es obligatorio");
		return $this->form_validation->run();
	}

	private function getFormErrors ($esPlatillo = false) {
		if ($esPlatillo) {
			return "txtPlatillo=".form_error("platillo")."&".
				"txtCantidad=".form_error("cantidad")."&".
				"txtPrecio=".form_error("precio");
		}
		else
			return "observaciones=".form_error("observaciones");
	}

	public function platillos () {
        if (validarAcceso(true)) {
        	$where = array("status" => 1);
            echo json_encode($this->PlatillosModelo->listar($where));
        }
        else
            show_404();
    }

    public function buscar ($nombrePlatillo = '') {
        if (validarAcceso(true)) {
        	$nombrePlatillo = implode(" ", explode("%20", $nombrePlatillo));
            echo json_encode($this->PlatillosModelo->like($nombrePlatillo));
        }
        else
            show_404();
    }

    public function addDetalle () {
    	if (validarAcceso(true)) {
    		$post = $this->input->post();
    		$res = array();
    		if ($this->formValidation(true)) {
    			$platillo = $this->PlatillosModelo->buscar(array("nombre" => $post['platillo']));
    			if (!$platillo) {
    				$res['code'] = 0;
    				$res['msg'] = "txtPlatillo=El platillo ingresado no existe&txtCantidad=&txtPrecio=";
    			}
    			else {
    				$detalle = array(
    					'id_comanda' => $post['idComanda'],
    					'id_platillo' => $platillo['id'],
    					'cantidad' => $post['cantidad'],
    					'precio' => $post['precio']
    				);
    				$this->ComandasDetalleModelo->trans_start();
    				$this->ComandasDetalleModelo->insertar($detalle);
    				if ($this->ComandasDetalleModelo->trans_end()) {
    					$res['code'] = 1;
    					$res['msg'] = $this->ComandasModelo->getTotal($post['idComanda']);
    				}
    				else {
    					$res['code'] = -1;
    					$res['msg'] = "No se pudo agregar el platillo";
    				}
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
}