<?php
define("BASEPATH") OR exit("No direct script access allowed");

class Platillos extends MY_Controller {

    public function __construct () {
        $this->vista = "platillos/mainVista";
        $this->modelo = "PlatillosModelo";
        $this->modulo = "Platillos";
        $this->form = "platillos/formVista";
        $this->tabla = "platillos/tablaVista";
        parent::__construct();
        
    }

    public function index () {
        if (parent::validarAcceso()) {
            $data = array(
                'titulo' => $this->modulo['nombre'],
                'data' => $this->PlatillosModelo->listar()
            )
        }
    }
}