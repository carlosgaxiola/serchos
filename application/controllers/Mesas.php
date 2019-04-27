<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Mesas extends MY_Controller {

	public $modulo;

	public function __construct () {
		parent::__construct();
		$this->load->helper("global_functions_helper");
		$this->load->model("MesasModelo");
		$this->modulo = getModulo("mesas");
	}

	public function index () {
		if (validarAcceso()) {
			$data = array( 'titulo' => "Mesas" );
    		$this->load->view("mesas/mainVista", $data);
		}
		else
			show_404();
	}

	public function data () {
		if (validarAcceso(true)) {
			echo json_encode($this->MesasModelo->listar());
		}
		else
			show_404();
	}

	public function add () {
		if (validarAcceso(true)) {
			$response = array();
			$post = $this->input->post();
			$fecha = DateTime::createFromFormat("d/m/Y", $post["txtFecha"]);
			if ($this->validarForm()) {
				$mesa = array(
					'tipo_mesa' => $post['cmbTipoMesa'],
					'status' => $post['cmbStatus']
				);
				$idMesa = $this->MesasModelo->insertar($mesa);
				if ($idMesa) {
					$response['code'] = 1;
					$response['msg'] = $idMesa;
				}
				else {
					$response['code'] = -1;
					$response['msg'] = "Error al crear la mesa";
				}
			}
			else {
				$response['msg'] = $this->getFormErrors();
				$response['code'] = 0;
			}
			echo json_encode($response);
		}
		else
			show_404();
	}

	public function edit () {
		if (validarAcceso(true)) {
			$post = $this->input->post();
			if ($this->validarForm()) {
				$mesa = array(
					'tipo_mesa' => $post['cmbTipoMesa'],
					'status' => $post['cmbStatus']
				);
				$this->MesasModelo->trans_start();
				if ($this->MesasModelo->actualizar($mesa, $post['idMesa'])) {
					if ($this->hayMinimo(4, 1)) { // Minimo 4 para 2 personas
						if ($this->hayMinimo(6, 2)) { //Minimo 6 para 4 personas
							$response['code'] = 1;
							$response['msg'] = $post['idMesa'];
							$this->MesasModelo->trans_commit();
						}
						else {
							$response['code'] = -3;
							$response['msg'] = "Debe haber mínimo <strong>6 mesas </storng> para <strong>4 personas</strong>";
							$this->MesasModelo->trans_rollback();
						}
					}
					else {
						$response['code'] = -2;
						$response['msg'] = "Debe haber mínimo <strong>4 mesas </storng> para <strong>2 personas</strong>";
						$this->MesasModelo->trans_rollback();
					}
					$this->MesasModelo->trans_end();
				}
				else {
					$response['code'] = -1;
					$response['msg'] = "Error al actualizar la mesa <strong>".$post['idMesa']."</strong>";
				}
			}
			else {
				$response['code'] = 0;
				$response['msg'] = $this->getFormErrors();
			}
			echo json_encode($response);
		}
		else
			show_404();	
	}

	public function toggle () {
		if (validarAcceso(true)) {
			$post = $this->input->post();
            $where = array("id" => $post["idMesa"]);
            $data = array("status" => $post["status"]);
            if ($post["status"] != 0 )
                $res['code'] = (int)$this->MesasModelo->actualizar($data, $where);
            else
                $res['code'] = 0;
            echo json_encode($res);
		}
		else
			show_404();
	}

	private function validarForm () {
		$this->form_validation->set_rules("cmbTipoMesa", "Tipo de Mesa", "required|is_natural_no_zero");
		$this->form_validation->set_rules("cmbStatus", "Estado de la mesa", "required|is_natural");
		$this->form_validation->set_message("required", "Este campo es obligatario");
		$this->form_validation->set_message("is_natural_no_zero", "Seleccione una opción válida");
		$this->form_validation->set_message("is_natural", "Seleccione una opción válida");
		return $this->form_validation->run();
	}

	private function getFormErrors () {
		return "cmbTipoMesa=".form_error("cmbTipoMesa")."&".
			"cmbStatus=".form_error("cmbStatus");
	}

	private function hayMinimo ($cantidad, $tipoMesa = 0) {
		$total = $this->MesasModelo->totalMesas($tipoMesa);
		if ($total) 
			return $total >= $cantidad;
		return false;	
	}
}