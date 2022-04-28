		<div class="wrap">
			<div class="container">
				<div class="row justify-content-center align-items-center">
					<div class="col-12 col-xl-4 col-lg-5 col-md-6">
						<div class="card shadow shadow-sm">
							<div class="card-header">
								<h1 class="fw-bolder h6 m-0 text-center"><?php echo KN\Helpers\Base::lang('base.recovery_account'); ?></h1>
							</div>
							<div class="card-body">
								<?php echo \KN\Helpers\Base::alert($this->response->alerts); ?>
								<form method="post" action="<?php echo $this->url('auth/recovery'); ?>" data-vpjax>
									<?php 
									echo KN\Helpers\Base::createCSRF();
									if ($step == 2) {
										?>
										<input type="hidden" name="token" value="<?php echo $token; ?>" />
										<div class="form-floating mb-3">
											<input type="password" name="password" class="form-control" id="password" placeholder="<?php echo KN\Helpers\Base::lang('base.new_password'); ?>" required>
											<label for="password"><?php echo KN\Helpers\Base::lang('base.new_password'); ?></label>
										</div>
										<div class="d-grid">
											<button type="submit" class="btn btn-primary btn-block">
												<?php echo KN\Helpers\Base::lang('base.change_password'); ?> 
											</button>
										</div>
										<?php
									} else {
										?>
										<div class="form-floating mb-3">
											<input type="email" name="email" class="form-control" id="email" placeholder="<?php echo KN\Helpers\Base::lang('base.email'); ?>" required <?php echo KN\Helpers\Base::inputValue('email', $this->request->params); ?>>
											<label for="email"><?php echo KN\Helpers\Base::lang('base.email'); ?></label>
										</div>
										<div class="d-grid">
											<button type="submit" class="btn btn-primary btn-block">
												<?php echo KN\Helpers\Base::lang('base.recovery_account'); ?> 
											</button>
										</div>
										<?php
									}	?> 
								</form>
							</div>
							<div class="card-footer d-flex justify-content-center align-items-center">
								<a href="<?php echo $this->url('auth/login'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo KN\Helpers\Base::lang('base.login'); ?>
								</a>
								<small class="vr mx-2"></small>
								<a href="<?php echo $this->url('auth/register'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo KN\Helpers\Base::lang('base.register'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		