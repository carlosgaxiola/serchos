<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {

	private $modulo;
	private $nombre = "Inicio";

	public function __construct () {
		parent::__construct();
		$this->load->helper("global_functions_helper");
		$this->load->model("InicioModelo");
		$this->modulo = $this->InicioModelo->buscar("modulos", $this->nombre, "nombre");
		$this->form_validation->set_error_delimiters("", "");
	}

	public function index () {
		$data = array(
			'titulo' => "Inicio",
			'actual' => $this->modulo
		);
		if (isset($this->session->extempo) and $this->session->extempo['login']) {
			$this->load->view("inicio/inicio_vista", $data);
		}
		else {
			$extempo = array(
				'id_perfil' => 0,
				'login' => false
			);			
			$data['titulo'] = "Login";
			$this->session->set_userdata(array('extempo' => $extempo));
			$this->load->view("inicio/login_vista", $data);
		}
	}

	public function login () {
		$post = $this->input->post();
		$respuesta['code'] = 1;
		$this->form_validation->set_rules("usuario", "usuario", "required|trim");
		$this->form_validation->set_rules("contra", "contra", "required");
		$this->form_validation->set_message("required", "r-{field}");
		if ($this->form_validation->run()) {
			$usuario = $this->InicioModelo->login($post['usuario'], $post['contra']);
			if (!$usuario) {
				$respuesta['code'] = 0;
				$respuesta['msg'] = "incorrecto";
			}
			else {
				$extempo = array(
					'nombre' => $usuario["nombre"],
					'id_usuario' => $usuario["id"],
					'paterno' => $usuario['paterno'],
					'materno' => $usuario['materno'],
					'usuario' => $usuario['usuario'],
					'contra' => $usuario['contra'],
					'id_perfil' => $usuario['id_perfil'],
					'perfil' => $usuario['perfil'],					
					'create_at' => $usuario['create_at'],
					'status' => $usuario['status'],
					'login' => true
				);
				$this->session->set_userdata(array('extempo' => $extempo));
				$respuesta['msg'] = "correcto";
			}
		}
		else {
			$respuesta['code'] = -1;
			$respuesta['msg'] = form_error('usuario')."&".form_error('contra');
		}
		echo json_encode($respuesta);
	}	

	public function logout () {
		$this->session->sess_destroy();
		header("Location: ".base_url("inicio"));
	}	
}