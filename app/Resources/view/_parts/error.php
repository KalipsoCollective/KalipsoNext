<div class="wrap sandbox">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h1><?php echo $error; ?></h1>
				<h2 class="h4"><?php echo $output; ?></h2>
				<?php
				if ((int)$error !== 403) { ?>
					<a class="btn btn-light" href="<?php echo $this->url('/') ?>">
						<i class="ti ti-arrow-left"></i> <?php echo KN\Helpers\Base::lang('base.go_to_home'); ?>
					</a>
				<?php
				}	?>
			</div>
		</div>
	</div>
</div>