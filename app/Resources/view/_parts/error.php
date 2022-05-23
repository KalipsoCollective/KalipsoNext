<div class="wrap sandbox">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<?php
				if ((int)$error === 503) {
					?>
					<h1><?php echo \KN\Helpers\Base::lang('base.maintenance_mode'); ?></h1>
					<h2 class="h4"><?php echo $output ? $output[\KN\Helpers\Base::lang('lang.code')] : \KN\Helpers\Base::lang('base.maintenance_mode_desc'); ?></h2>
					<?php
				} else {
					?>
					<h1><?php echo $error; ?></h1>
					<h2 class="h4"><?php echo $output; ?></h2>
					<?php
				}
				if ((int)$error !== 403 AND (int)$error !== 503) { ?>
					<a class="btn btn-light" href="<?php echo $this->url('/') ?>">
						<i class="ti ti-arrow-left"></i> <?php echo KN\Helpers\Base::lang('base.go_to_home'); ?>
					</a>
				<?php
				}	?>
			</div>
		</div>
	</div>
</div>