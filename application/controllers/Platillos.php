<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Platillos extends CI_Controller {
    
    public $modulo = "Platillos";
    public $form = "platillos/formVista";
    public $tabla = "platillos/tablaVista";
    public $script = "platillos/scriptJS";

    public function __construct () {
        parent::__construct();
        $this->load->model("PlatillosModelo");
        $this->load->helper("global_functions_helper");
        $this->modulo = getModulo($this->modulo);
        $moduloPadre = getModulo("Restaurante");
        $this->session->set_tempdata("idModuloPadreActual", $moduloPadre['id'] , 60);
        $this->session->set_tempdata("idModuloActual", $this->modulo['id'] , 60);
    }

    public function index () {
        if (validarAcceso()) {
            $data = array(
                'titulo' => $this->modulo['nombre'],
                'platillos' => $this->PlatillosModelo->listar()
            );
            $this->load->view("platillos/mainVista", $data);
        }
        else
            show_404();
    }

    public function add () {
        if (validarAcceso() and $this->input->is_ajax_request()) {
            $post = $this->input->post();
            $fecha = DateTime::createFromFormat("d/m/Y", $post['fecha']);
            $response = array();
            if ($this->form_validation()) {
                $platillo = array(
                    'nombre' => $post['txtNombre'],
                    'precio' => $post['txtPrecio'],
                    'status' => 1,
                    'create_at' => $fecha->format("Y-m-d")
                );
                $idPlatillo = $this->PlatillosModelo->insertar($platillo);
                if ($idPlatillo) {
                    $response['code'] = 1;
                    $response['msg'] = $idPlatillo;
                }
                else {
                    $response['code'] = -1;
                    $response['msg'] = "No se pudo guardar el platillo";
                }
                echo json_encode($response);
            }
            else {
                $response['code'] = 0;
                $response['msg'] = $this->get_erorr_message();
                echo json_encode($response);
            }
        }
        else
            show_404();
    }

    public function edit () {
        if (validarAcceso() and $this->input->is_ajax_request()) {
            $post = $this->input->post();
            $response = array();
            if ($this->form_validation($post['idPlatillo'])) {
                $platillo = array(
                    'nombre' => $post['txtNombre'],
                    'precio' => $post['txtPrecio']
                );
                $where = array("id" => $post['idPlatillo']);
                if ($this->PlatillosModelo->actualizar($platillo, $where)) {
                    $response['code'] = 1;
                    $response['msg'] = $post['idPlatillo'];
                }
                else {
                    $response['code'] = -1;
                    $response['msg'] = "No se pudo actualizar el platillo";
                }
                echo json_encode($response);
            }
            else {
                $response['code'] = 0;
                $response['msg'] = $this->get_erorr_message();
                echo json_encode($response);
            }
        }
        else
            show_404();
    }

    public function toggle () {
        if (validarAcceso() and $this->input->is_ajax_request()) {
            $post = $this->input->post();
            $where = array("id" => $post["idPlatillo"]);
            $data = array("status" => $post["status"]);
            if ($post["status"] == 0 and !$this->hayMasDe(5))
                $res['code'] = -1;
            else
                $res['code'] = (int)$this->PlatillosModelo->actualizar($data, $where);
            echo json_encode($res);
        }
        else
            show_404();
    }

    public function data ($id = '') {
        if (validarAcceso() and $this->input->is_ajax_request()) {
            if (empty($id))
                echo json_encode($this->PlatillosModelo->listar());
            else {
                $where = array("id" => $id);
                echo json_encode($this->PlatillosModelo->listar($where));
            }
        }
        else
            show_404();
    }

    private function form_validation ($idPlatillo = 0) {
        $uNombre = "|is_unique[platillos.nombre]";
        if ($idPlatillo != 0) {
            $platillo = $this->PlatillosModelo->buscar(array("id" => $idPlatillo));
            if ($platillo) {
                $nombre = $this->input->post("txtNombre");
                if (strcmp($nombre, $platillo['nombre']) == 0)
                    $uNombre = "";
            }
        }
        $this->form_validation->set_rules("txtNombre", "Nombre", "trim|required".$uNombre);
        $this->form_validation->set_rules("txtPrecio", "Precio", "trim|required|numeric");
        $this->form_validation->set_message("required", "El campo {field} es obligatorio");
        $this->form_validation->set_message("numeric", "El campo {field} solo puede contener caracteres numericos");
        $this->form_validation->set_message("is_unique", "El {field} ingresado ya existe");
        return $this->form_validation->run();
    }

    private function get_erorr_message () {
        return "txtNombre=".form_error("txtNombre")."&".
            "txtPrecio=".form_error("txtPrecio");
    }
    
    private function hayMasDe ($cantidad) {
        $total = $this->PlatillosModelo->totalPlatillos();
        return $total > $cantidad;
    }

    public function buscar ($nombrePlatillo = '') {
        if (validarAcceso(true)) {            
            echo json_encode($this->PlatillosModelo->like($nombrePlatillo));
        }
        else
            show_404();
    }
}