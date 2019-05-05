<?php $this->load->view("global/header") ?>
<?php $this->load->view("global/navbar") ?>
<?php $this->load->view("global/aside") ?>
<main class="content-wrapper">
	<section class="content" id="tbl">
		<?php $this->load->view("reservaciones/tablaVista") ?>
	</section>
	<section class="content" id="frm" hidden>
		<?php $this->load->view("reservaciones/formVista") ?>
	</section>
</main>
<?php $this->load->view("global/footer") ?>
<?php $this->load->view("reservaciones/scriptJS") ?>