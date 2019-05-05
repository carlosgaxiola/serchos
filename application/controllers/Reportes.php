<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Reportes extends MY_Controller {

	public $modulo; 

	public function __construct () {
		parent::__construct();
		$this->load->helper("global_functions_helper");
		$this->load->model("ReportesModelo");
		$this->modulo = getModulo("Historial de platillos");
	}

	public function platillos () {
		if (validarAcceso(true)) {
			$fecha = DateTime::createFromFormat("d/m/Y", $this->input->get("fecha"));
			$fecha = $fecha->format("Y-m-d");
			echo json_encode($this->ReportesModelo->platillos($fecha));
		}
		else if (validarAcceso()) {
			$data = array(
				'titulo' => 'Historial del platillos',
				'platillos' => $this->ReportesModelo->platillos()
			);
			$moduloPadre = getModulo("Restaurante");
	        $this->session->set_tempdata("idModuloPadreActual", $moduloPadre['id'] , 60);
	        $this->session->set_tempdata("idModuloActual", $this->modulo['id'] , 60);
			$this->load->view("reportes/platillos/mainVista", $data);
		}
		else
			show_404();
	}

	public function diario () {
		if (validarAcceso(true)) {
			$fecha = DateTime::createFromFormat("d/m/Y", $this->input->get("fecha"));
			echo json_encode($this->ReportesModelo->comandas($fecha->format("Y-m-d")));
		}
		else if (validarAcceso()) {
			$data = array(
				'titulo' => 'Reporte diario',
				'comandas' => $this->ReportesModelo->comandas()
			);
			$moduloPadre = getModulo("Reportes");
		    $this->session->set_tempdata("idModuloPadreActual", $moduloPadre['id'] , 60);
		    $modulo = getModulo("Reporte Diario");
		    $this->session->set_tempdata("idModuloActual", $modulo['id'] , 60);
			$this->load->view("reportes/comandas/mainVista", $data);
		}
		else
			show_404();
	}

	public function caja () {
		if (validarAcceso(true)) {
			$fecha = DateTime::createFromFormat("d/m/Y", $this->input->get("fecha"));
			echo json_encode($this->ReportesModelo->comandas($fecha->format("Y-m-d")));
		}
		else if (validarAcceso()) {
			$data = array(
				'titulo' => 'Historial de caja',
				'comandas' => $this->ReportesModelo->comandas()
			);
			$moduloPadre = getModulo("Restaurante");
		    $this->session->set_tempdata("idModuloPadreActual", $moduloPadre['id'] , 60);
		    $modulo = getModulo("Historial de caja");
		    $this->session->set_tempdata("idModuloActual", $modulo['id'] , 60);
			$this->load->view("reportes/comandas/mainVista", $data);
		}
		else
			show_404();
	}

	public function rango () {
		if (validarAcceso(true)) {
			$inicio = DateTime::createFromFormat("d/m/Y", $this->input->get("inicio"));
			$fin = DateTime::createFromFormat("d/m/Y", $this->input->get("fin"));
			echo json_encode($this->ReportesModelo
				->comandas($inicio->format("Y-m-d"), $fin->format("Y-m-d")));
		}
		else if (validarAcceso()) {
			$data = array(
				'titulo' => 'Reporte por rango',
				'comandas' => $this->ReportesModelo->comandas(),
				'rango' => true
			);
			$moduloPadre = getModulo("Reportes");
		    $this->session->set_tempdata("idModuloPadreActual", $moduloPadre['id'] , 60);
		    $modulo = getModulo("Reporte de rango de fechas");
		    $this->session->set_tempdata("idModuloActual", $modulo['id'] , 60);
			$this->load->view("reportes/comandas/mainVista", $data);
		}
		else
			show_404();
	}

	public function generar ($tipo) {
		if (validarAcceso()) {
			$this->load->library("pdf");
			$tipo = strtolower($tipo);
			$get = $this->input->get();
			switch ($tipo) {
				case "platillos":
					$fecha = DateTime::createFromFormat("d/m/Y", $get["fecha"]);
					$header = array("#", "Nombre", "Cantidad", "Precio", "Subtotal");
					$pdf = new PDF();
					$pdf->SetFont('Arial','',14);
					$pdf->AddPage();
					$pdf->Cell(40, 10, 'Reporte de platillos de la fecha: '.$fecha->format("d/m/Y"), 0, 1);
					$data = $this->ReportesModelo->platillos($fecha->format("Y-m-d"));
					$total = 0;
					foreach ($data as $platillo)
						$total += $platillo['cantidad'] * $platillo['precio'];
					$pdf->tablaPlatillos($header, $data);
					$pdf->Cell(170, 10, 'Total: '.$total, 0, 1, "R");
					$pdf->Output();
					break;
				case "diario":
					$fecha = DateTime::createFromFormat("d/m/Y", $get["fecha"]);
					$header = array("#", "Mesa", "Total", "Fecha", "Hora", "Estado");
					$pdf = new PDF();
					$pdf->SetFont('Arial','', 12);
					$pdf->AddPage();
					$pdf->Cell(40, 10, 'Reporte diario de la fecha: '.$fecha->format("d/m/Y"), 0, 1);
					$data = $this->ReportesModelo->comandas($fecha->format("Y-m-d"));
					$totalPagadas = 0;
					$totalRechazadas = 0;
					foreach ($data as $platillo) {
						if ($platillo['status'] == 3)
							$totalPagadas += $platillo['total'];
						else
							$totalRechazadas += $platillo['total'];
					}
					$pdf->tablaComandas($header, $data);
					$pdf->Cell(60, 10, 'Total Pagadas: ', 0, 0, "R");
					$pdf->SetFont('Arial', 'B', 12);
					$pdf->Cell(10, 10, $totalPagadas, 0, 0, "R");
					$pdf->SetFont('Arial', '', 12);

					$pdf->Cell(40, 10, 'Total Rechazadas: ', 0, 0, "R");
					$pdf->SetFont('Arial', 'B', 12);
					$pdf->Cell(10, 10, $totalRechazadas, 0, 0, "R");
					$pdf->Output();
					break;
				case "rango":
					$inicio = DateTime::createFromFormat("d/m/Y", $get["inicio"]);
					$fin = DateTime::createFromFormat("d/m/Y", $get["fin"]);
					$header = array("#", "Mesa", "Total", "Fecha", "Hora", "Estado");
					$pdf = new PDF();
					$pdf->SetFont('Arial','', 12);
					$pdf->AddPage();
					$pdf->Cell(40, 10, 'Reporte rango de fechas', 0, 1);
					$pdf->SetFont('Arial', 'B', 12);
					$pdf->Cell(40, 10, 'Inicio: '.$inicio->format("d/m/Y"), 0, 0);
					$pdf->Cell(40, 10, 'Fin: '.$fin->format("d/m/Y"), 0, 1);
					$data = $this->ReportesModelo
						->comandas($inicio->format("Y-m-d"), $fin->format("Y-m-d"));
					$totalPagadas = 0;
					$totalRechazadas = 0;
					foreach ($data as $platillo) {
						if ($platillo['status'] == 3)
							$totalPagadas += $platillo['total'];
						else
							$totalRechazadas += $platillo['total'];
					}
					$pdf->tablaComandas($header, $data);
					$pdf->Cell(90, 10, 'Total Pagadas: ', 0, 0, "R");
					$pdf->SetFont('Arial', 'B', 12);
					$pdf->Cell(10, 10, $totalPagadas, 0, 0, "R");
					$pdf->SetFont('Arial', '', 12);
					$pdf->Cell(40, 10, 'Total Rechazadas: ', 0, 0, "R");
					$pdf->SetFont('Arial', 'B', 12);
					$pdf->Cell(10, 10, $totalRechazadas, 0, 0, "R");
					$pdf->Output();
					break;
			}
			// pdf($this->load->view("platillas/".$tipo, $data, true), "i", "Platillos", "PDF Generado");
		}
		else
			show_404();
	}
}