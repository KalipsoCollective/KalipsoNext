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
						<?php 

						if (is_array($output)) {

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

						} else {
							echo $output; 
						}	?>
					</div>
				</div>
			</div>
		</div>
		