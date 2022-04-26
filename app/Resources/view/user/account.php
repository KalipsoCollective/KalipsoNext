		<div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-12 col-lg-3">
						<div class="list-group shadow mb-3 sticky-top">
							<a class="list-group-item list-group-item-action<?php echo $this->currentLink('/auth'); ?>" href="<?php echo $this->url('/auth'); ?>">
								<i class="ti ti-user"></i>  <?php echo KN\Helpers\Base::lang('base.account'); ?>
							</a>
							<?php
							foreach ($steps as $step => $details) {

								echo '
								<a class="list-group-item list-group-item-action'.$this->currentLink('/auth/' . $step).'" href="'.$this->url('/auth/' . $step).'">
									<i class="' . $details['icon'] . '"></i>  '.KN\Helpers\Base::lang($details['lang']).'
								</a>';
							}	?>
							<a class="list-group-item list-group-item-action list-group-item-danger" href="<?php echo $this->url('/auth/logout'); ?>">
								<i class="ti ti-power"></i>  <?php echo KN\Helpers\Base::lang('base.logout'); ?>
							</a>
						</div>
					</div>
					<div class="col-12 col-lg-9">
						<?php echo KN\Helpers\Base::alert(); ?>
						<h1><?php echo $head; ?></h1>
						<h2 class="h4"><?php echo $description; ?></h2>
						<?php echo $output; ?>
					</div>
				</div>
			</div>
		</div>
		