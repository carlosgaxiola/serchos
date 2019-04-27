<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Usuarios extends MY_Controller {
    
    public $modulo = "Usuarios";
    private $registros;
    public $tipo;

    public function __construct () {
    	parent::__construct();
    	$this->load->helper("global_functions_helper");
    	$this->modulo = getModulo($this->modulo);
    	$this->load->model("UsuariosModelo");
    	$this->load->model("PerfilesModelo");
    }

    public function index ($tipoUsuario) {
    	if (validarAcceso()) {  
    		$this->tipo = $tipoUsuario;
    		$perfil['singular'] = $this->singleTipoUsuario($tipoUsuario);
    		$perfil['plural'] = $tipoUsuario;
    		$this->registros = $this->UsuariosModelo->$tipoUsuario();
    		$data = array(
    			'titulo' => ucfirst($tipoUsuario),
    			'perfil' => $perfil,
    			'idPerfil' => getIdPerfil($perfil['singular'])
    		);
    		$this->load->view("usuarios/mainVista", $data);
    	}
    	else
    		show_404();
    }

    private function singleTipoUsuario ($pluralTipoUsuario) {
    	if (strcmp($pluralTipoUsuario, "meseros") == 0)
    		return "mesero";
    	else if (strcmp($pluralTipoUsuario, "cocineros") == 0)
    		return "cocina";
    	else if (strcmp($pluralTipoUsuario, "gerentes") == 0)
    		return "gerente";
    	else if (strcmp($pluralTipoUsuario, "recepcionistas") == 0)
    		return "recepcionista";
    	else if (strcmp($pluralTipoUsuario, "cajeros") == 0)
    		return "cajero";
    	else if (strcmp($pluralTipoUsuario, "clientes") == 0)
    		return "cliente";
    	else 
    		return "usuario";
    }

    public function data ($tipoUsuario, $id = '') {
    	if (validarAcceso() and $this->input->is_ajax_request()) {
			switch ($tipoUsuario) {
				case 'meseros':
					$registros = $this->UsuariosModelo->meseros($id);
					break;
				case 'cajeros':
					$registros = $this->UsuariosModelo->cajeros($id);
					break;
				case "clientes":
					$registros = $this->UsuariosModelo->clientes($id);
					break;
				case "gerentes":
					$registros = $this->UsuariosModelo->gerentes($id);
					break;
				case "recepcionistas":
					$registros = $this->UsuariosModelo->recepcionistas($id);
					break;
				case "cocineros":
					$registros = $this->UsuariosModelo->cocineros($id);
					break;
				default:
					$registros = false;
					break;
			}
			echo json_encode($registros);
    	}
    	else
    		show_404();
    }

    private function form_validation ($idUsuario = 0) {
    	$uUsuario = "|is_unique[usuarios.usuario]";
    	$contraRules = "";
    	$confContraRules = "";
    	if ($idUsuario != 0) {
    		$usuario = $this->UsuariosModelo->buscar(array("id" => $idUsuario));
    		if ($usuario and strcmp($this->input->post("txtUsuario"), $usuario['usuario']) == 0) {
    			$uUsuario = "";
    		}
    	}
    	$this->form_validation->set_rules("txtNombre", "Nombre(s)", "trim|required|max_length[45]");
    	$this->form_validation->set_rules("txtPaterno", "Apellido Paterno",  "trim|required|max_length[45]");
    	$this->form_validation->set_rules("txtMaterno", "Apellido Materno", "trim|required|max_length[45]");
    	$this->form_validation->set_rules("txtUsuario", "Usuario", "trim|required|max_length[45]".$uUsuario);
    	$this->form_validation->set_rules("txtContra", "Contraseña", "required");
    	$this->form_validation->set_rules("txtConfContra", "Repetir Contraseña", "required|matches[txtContra]");
    	$this->form_validation->set_rules("txtFecha", "Fecha", "required");
    	$this->form_validation->set_message("required", "El campo {field} es obligatorio");
    	$this->form_validation->set_message("is_unique", "El campo {field} ya existe");
    	$this->form_validation->set_message("matches", "El campo {field} debe coincidir con {param}");
    	$this->form_validation->set_message("max_length", "Máximo {param} caracteres");
    	return $this->form_validation->run();
    }

    public function add () {
    	if (validarAcceso() and $this->input->is_ajax_request()) {
    		$post = $this->input->post();
    		$fecha = DateTime::createFromFormat("d/m/Y", $post['txtFecha']);
    		$response = array();
    		if ($this->form_validation()) {
    			$usuario = array(
    				'nombre' => $post['txtNombre'],
    				'paterno' => $post['txtPaterno'],
    				'materno' => $post['txtMaterno'],
    				'usuario' => $post['txtUsuario'],
    				'create_at' => $fecha->format("Y-m-d"),
    				'status' => 1,
    				'contra' => $post['txtContra'],
    				'id_perfil' => $post['idPerfil']
    			);
    			$idUsuario = $this->UsuariosModelo->insertar($usuario);
    			if ($idUsuario) {
    				$response['code'] = 1;
    				$response['msg'] = $idUsuario;
    			}
    			else {
    				$response['code'] = -1;
    				$perfil = $this->PerfilesModelo->buscar(array("nombre" => $post['idPerfil']));
    				$response['msg'] = "No se pudo registrar al ".$perfil['nombre'];
    			}
    		}
    		else {
    			$response['code'] = 0;
    			$response['msg'] = $this->get_form_errors();
    		}
    		echo json_encode($response);
    	}
    	else
    		show_404();
    }

    public function edit () {
    	if (validarAcceso() and $this->input->is_ajax_request()) {
    		$post = $this->input->post();
    		$response = array();
    		if (!empty($post['idUsuario']) and $this->form_validation($post['idUsuario'])) {
    			$usuario = array(
    				'nombre' => $post['txtNombre'],
    				'paterno' => $post['txtPaterno'],
    				'materno' => $post['txtMaterno'],
    				'usuario' => $post['txtUsuario']
    			);
    			if ($post['idUsuario'] != 0 and isset($post['txtContra']) and !empty($post['txtContra'])) {
    				$usuario['contra'] = $post['txtContra'];
    			}
    			$where = array("id" => $post['idUsuario']);
    			if ($this->UsuariosModelo->actualizar($usuario, $where)) {
    				$response['code'] = 1;
    				$response['msg'] = $post['idUsuario'];
    			}
    			else {
    				$response['code'] = -1;
    				$response['msg'] = "No se pudo actualizar el usuario";
    			}
    		}
    		else {
    			$response['code'] = 0;
    			$response['msg'] = $this->get_form_errors();
    		}
    		echo json_encode($response);
    	}
    	else {
    		show_404();
    	}
    }

    private function get_form_errors () {
    	return "txtNombre=".form_error("txtNombre")."&".
    		"txtPaterno=".form_error("txtPaterno")."&".
    		"txtMaterno=".form_error("txtMaterno")."&".
    		"txtUsuario=".form_error("txtUsuario")."&".
    		"txtContra=".form_error("txtContra")."&".
    		"txtConfContra=".form_error("txtConfContra");
    }

}