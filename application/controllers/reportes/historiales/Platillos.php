<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Platillos extends MY_Controller {

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
			
		}
		else
			show_404();
	}
}