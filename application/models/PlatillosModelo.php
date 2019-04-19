<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class PlatillosModelo extends MY_Model {    

	public function __construct () {
        parent::__construct();
        $this->tabla = "platillos";
        $this->view = "platillos";
    }
}