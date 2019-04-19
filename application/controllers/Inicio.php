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
		$this->form_validation->set_rules("txtUsuario", "Usuario", "required|trim");
		$this->form_validation->set_rules("txtContra", "Contraseña", "required");
		$this->form_validation->set_message("required", "El campo %s es obligatoro");
		if ($this->form_validation->run()) {
			$usuario = $this->InicioModelo->login($this->input->post("txtUsuario"), $this->input->post("txtContra"));
			if (!$usuario) {
				$this->session->set_flashdata("error", "El usuario y/o contraseña son incorrectos");
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
			}
		}
		redirect(base_url());
	}	

	public function logout () {
		$this->session->sess_destroy();
		header("Location: ".base_url("inicio"));
	}
}