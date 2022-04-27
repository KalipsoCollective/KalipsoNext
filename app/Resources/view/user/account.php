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
						<?php echo KN\Helpers\Base::alert($this->response->alerts); ?>
						<h1><?php echo $head; ?></h1>
						<h2 class="h4"><?php echo $description; ?></h2>
						<?php 
						if ($action == 'sessions' AND is_array($output)) {

							echo '
							<div class="table-responsive">
								<table class="table table-striped table-hover table-bordered table-sm">
									<thead>
										<tr>
											<th scope="col">'.KN\Helpers\Base::lang('base.device').'</th>
											<th scope="col">'.KN\Helpers\Base::lang('base.ip').'</th>
											<th scope="col">'.KN\Helpers\Base::lang('base.last_action_point').'</th>
											<th scope="col">'.KN\Helpers\Base::lang('base.last_action_date').'</th>
											<th scope="col">'.KN\Helpers\Base::lang('base.action').'</th>
										</tr>
									</thead>
									<tbody>';

								foreach ($output as $out) {

									$current = $auth_code == $out->auth_code ? true : false;

									echo '
									<tr'.($current ? ' class="table-dark"' : '').'>
										<td>
											<i title="' . $out->device['os'] . '" class="' . $out->device['p_icon'] . '"></i> 
											<i title="' . $out->device['browser'] . ' ' . $out->device['version'] . '" class="' . $out->device['b_icon'] . '"></i>
										</td>
										<td>'.$out->ip.'</td>
										<td>'.$out->last_action_point.'</td>
										<td>'.date('d.m.Y H:i:s', (int)$out->last_action_date).'</td>
										<td>
											'.($current ? '
											<a class="btn btn-danger w-100 btn-sm" href="'.$this->url('/auth/logout').'">
												<i class="ti ti-power"></i> '.KN\Helpers\Base::lang('base.logout').'
											</a>' : '
											<a class="btn btn-danger w-100 btn-sm" href="'.$this->url('/auth/sessions').'?terminate='.$out->id.'">
												<i class="ti ti-power"></i> '.KN\Helpers\Base::lang('base.terminate').'
											</a>').'
										</td>
									</tr>';
								}

							echo '	</tbody>
								</table>
							</div>';

						} elseif ($action == 'profile') {
							?>
							<form method="post" class="row g-3 my-3" action="<?php echo $this->url('auth/profile'); ?>" data-vpjax>
								<?php echo \KN\Helpers\Base::createCSRF(); ?>
								<div class="col-12 col-md-6">
									<div class="form-floating">
										<input type="text" name="f_name" class="form-control" id="f_name" placeholder="<?php echo \KN\Helpers\Base::lang('base.name'); ?>" required <?php echo \KN\Helpers\Base::inputValue('f_name', $output); ?>>
										<label for="f_name"><?php echo \KN\Helpers\Base::lang('base.name'); ?></label>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-floating">
										<input type="text" name="l_name" class="form-control" id="l_name" placeholder="<?php echo \KN\Helpers\Base::lang('base.surname'); ?>" required <?php echo \KN\Helpers\Base::inputValue('l_name', $output); ?>>
										<label for="l_name"><?php echo \KN\Helpers\Base::lang('base.surname'); ?></label>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-floating">
										<input type="date" name="b_date" class="form-control" id="b_date" placeholder="<?php echo \KN\Helpers\Base::lang('base.birth_date'); ?>" required <?php echo \KN\Helpers\Base::inputValue('b_date', $output, 'date'); ?>>
										<label for="b_date"><?php echo \KN\Helpers\Base::lang('base.birth_date'); ?></label>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-floating">
										<input type="email" name="email" class="form-control" id="email" placeholder="<?php echo \KN\Helpers\Base::lang('base.email'); ?>" required <?php echo \KN\Helpers\Base::inputValue('email', $output); ?>>
										<label for="email"><?php echo \KN\Helpers\Base::lang('base.email'); ?></label>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-floating">
										<input type="password" name="password" class="form-control" id="password" placeholder="<?php echo \KN\Helpers\Base::lang('base.password'); ?>">
										<label for="password"><?php echo \KN\Helpers\Base::lang('base.password'); ?></label>
									</div>
								</div>
								<div class="col-12">
									<button type="submit" class="btn btn-primary d-flex ms-auto">
										<?php echo \KN\Helpers\Base::lang('base.update'); ?> 
										<span class="mdi mdi-arrow-right"></span>
									</button>
								</div>
							</form>
							<?php
						} else {
							echo $output; 
						}	?>
					</div>
				</div>
			</div>
		</div>
		