		<div class="container account-pages">
			<div class="row justify-content-center">
				<div class="col-12 col-md-9">
					<div class="row">
						<div class="col-12 col-lg-3">
							<div class="list-group kn-list-menu">
								<?php 

								$menuItems = [
									'/account' => [
										'icon'	=> 'mdi mdi-account',
										'name'	=> 'account',
									],
									'/account/profile' => [
										'icon'	=> 'mdi mdi-account-cog',
										'name'	=> 'profile',
									],
									'/account/sessions' => [
										'icon'	=> 'mdi mdi-account-lock-open',
										'name'	=> 'sessions',
									]
								];
								foreach ($menuItems as $slug => $data) {

									echo '
									<a 
										href="' . self::base($slug) . '" 
										class="list-group-item list-group-item-action' . (self::$request['request'] == $slug ? ' active' : '') . '">
										<i class="' . self::lang($data['icon']) . '"></i> ' . self::lang($data['name']) . '
									</a>';

								}	?>
							</div>
						</div>
						<div class="col-12 col-lg-9">
							<?php 
							echo self::alert();
							switch (self::$request['request']) {

								case '/account/profile':
									?>
									<form class="row g-2" method="post" action="<?php echo self::base('account/profile'); ?>" data-vpjax>
										<?php echo self::createCSRF(); ?>
										<div class="col-12 col-md-6">
											<div class="form-floating">
												<input type="text" name="f_name" class="form-control" id="fN" value="<?php echo self::userData('f_name'); ?>" placeholder="<?php echo self::lang('first_name'); ?>" required>
												<label for="fN"><?php echo self::lang('first_name'); ?></label>
											</div>
										</div>
										<div class="col-12 col-md-6">
											<div class="form-floating">
												<input type="text" name="l_name" class="form-control" id="lN" value="<?php echo self::userData('l_name'); ?>" placeholder="<?php echo self::lang('last_name'); ?>" required>
												<label for="lN"><?php echo self::lang('last_name'); ?></label>
											</div>
										</div>
										<div class="col-12 col-md-6">
											<div class="form-floating">
												<input type="text" name="u_name" class="form-control" id="uN" value="<?php echo self::userData('u_name'); ?>" placeholder="<?php echo self::lang('user_name'); ?>" required>
												<label for="uN"><?php echo self::lang('user_name'); ?></label>
											</div>
										</div>
										<div class="col-12 col-md-6">
											<div class="form-floating">
												<input type="email" name="email" class="form-control" id="eM" value="<?php echo self::userData('email'); ?>" placeholder="<?php echo self::lang('email'); ?>" required>
												<label for="eM"><?php echo self::lang('email'); ?></label>
											</div>
										</div>
										<div class="col-12 col-md-6">
											<div class="form-floating">
												<input type="date" name="b_date" class="form-control" id="bD" value="<?php echo self::userData('b_date'); ?>" placeholder="<?php echo self::lang('birth_date'); ?>" required>
												<label for="bD"><?php echo self::lang('birth_date'); ?></label>
											</div>
										</div>
										<div class="col-12">
											<hr class="bg-secondary">
										</div>
										<div class="col-12 col-md-6">
											<div class="form-floating">
												<input type="password" name="password" class="form-control" id="uP" placeholder="<?php echo self::lang('password'); ?>">
												<label for="uP"><?php echo self::lang('password'); ?></label>
											</div>
										</div>
										<div class="col-12">
											<button type="submit" class="btn btn-primary ms-auto d-flex"><?php echo self::lang('save'); ?></button>
										</div>
									</form>
									<?php
									break;

								case '/account/sessions':
									if (isset($sessions) AND count($sessions)) {
										echo '
										<div class="table-responsive">
											<table class="table table-sm table-bordered table-striped">
												<thead>
													<tr>
														<th></th>
														<th scope="col">'.self::lang('device').'</th>
														<th scope="col">'.self::lang('ip').'</th>
														<th scope="col">'.self::lang('last_action').'</th>
													</tr>
												</thead>
												<tbody>';

												foreach ($sessions as $session) {
													
													$device = self::userAgentDetails($session->header);
													echo '
													<tr'.(self::userData('auth_code') == $session->auth_code ? ' class="table-dark"' : '').'>
														<td>
															<i class="mdi mdi-circle'.(strtotime('-5 minutes') <= $session->last_action_date ? ' text-success' : ' text-muted').'"></i>
														</td>
														<td>
															<span title="'.$device['os'].'"><i class="'.$device['p_icon'].'"></i> '.$device['platform'].'</span> · 
															<span title="'.$device['version'].'"><i class="'.$device['b_icon'].'"></i> '.$device['browser'].'</span>
														</td>
														<td>'.$session->ip.'</td>
														<td>
															<span class="badge bg-secondary">'.$session->last_action_point.'</span> · 
															<small class="text-muted">'.date('d.m.Y H:i', $session->last_action_date).'</small></td>
													</tr>';

												}

										echo '	</tbody>
											</table>
										</div>';
									} else {
										echo '<p class="text-danger">'.self::lang('no_record_found').'</p>';
									}

									break;
								
								default:
									echo self::lang('welcome_account');
									break;
							}	?>
						</div>
					</div>
				</div>
			</div>
		</div>
		