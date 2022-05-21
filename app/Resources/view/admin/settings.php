		<div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="d-flex align-items-center">
							<h1 class="h3 fw-bold"><?php echo \KN\Helpers\Base::lang('base.settings'); ?></h1>
						</div>
						<p><?php echo $description; ?></p>
					</div>
					<div class="col-12">
						<form class="row g-2" data-kn-form id="roleAdd" method="post" action="<?php echo $this->url('management/roles/add'); ?>">
							<div class="form-loader">
								<div class="spinner-border text-light" role="status">
									<span class="visually-hidden"><?php echo \KN\Helpers\Base::lang('base.loading'); ?></span>
								</div>
							</div>
							<div class="col-12 form-info">
							</div>
							<?php

							echo '<small class="text-muted">
								' . \KN\Helpers\Base::lang('base.updated_at') . ': 
								<strong>' . date('d.m.Y H:i',  (int)$areas['last_updated_at']['value']) . '</strong>
							</small>';

							foreach ($areas as $name => $values) {

								$col = 'col-md-4 col-12';
								$text = \KN\Helpers\Base::lang('base.' . $name);
								
								if ($values['type'] == 'hidden') {

									echo '<input type="hidden" name="' . $name . '" value="' . $values['value'] . '">';

								} elseif ($values['type'] == 'select') {



								} elseif ($values['type'] == 'input') {

									echo '
									<div class="' . $col . '">
										<div class="form-floating">
											<input type="text" class="form-control" required name="' . $name . '" id="' . $name . '" placeholder="' . $text . '">
											<label for="' . $name . '">' . $text . '</label>
										</div>
									</div>';


								} elseif ($values['type'] == 'check') {

									echo '
									<div class="' . $col . '">
										<div class="form-check form-switch d-flex h-100 align-items-center">
											<input class="form-check-input" type="checkbox" role="switch" id="'.$name.'">
											<label class="form-check-label ms-2" for="'.$name.'"> ' . $text . '</label>
										</div>
									</div>';

								}

							}	?>
						</form>
					</div>
				</div>
			</div>
		</div>