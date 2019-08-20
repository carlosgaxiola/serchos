<script>
	$(document).ready( function () {
		<?php if ($this->session->flashdata("success")): ?>
			success('<?php echo $this->session->flashdata("success") ?>');
		<?php elseif ($this->session->flashdata("error")): ?>
			errorDialog('<?php echo $this->session->flashdata("error") ?>');
		<?php endif; ?>
	})
</script>