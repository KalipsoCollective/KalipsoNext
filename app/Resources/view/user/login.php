		<div class="wrap">
			<div class="container">
				<div class="row justify-content-center align-items-center">
					<div class="col-12 col-xl-4 col-lg-5 col-md-6">
						<div class="card shadow shadow-sm">
							<div class="card-header">
								<h1 class="fw-bolder h6 m-0 text-center"><?php echo \KN\Helpers\Base::lang('base.login'); ?></h1>
							</div>
							<div class="card-body">
								<?php echo \KN\Helpers\Base::alert($this->response->alerts); ?>
								<form method="post" action="<?php echo $this->url('auth/login'); ?>" data-vpjax>
									<?php echo \KN\Helpers\Base::createCSRF(); ?>
									<div class="form-floating mb-3">
										<input type="text" name="username" class="form-control" id="username" placeholder="<?php echo \KN\Helpers\Base::lang('base.email_or_username'); ?>" required <?php echo \KN\Helpers\Base::inputValue('username', $this->request->params); ?>>
										<label for="username"><?php echo \KN\Helpers\Base::lang('base.email_or_username'); ?></label>
									</div>
									<div class="form-floating mb-3">
										<input type="password" name="password" class="form-control" id="password" placeholder="<?php echo \KN\Helpers\Base::lang('base.password'); ?>" required>
										<label for="password"><?php echo \KN\Helpers\Base::lang('base.password'); ?></label>
									</div>
									<div class="d-grid">
										<button type="submit" class="btn btn-primary">
											<?php echo \KN\Helpers\Base::lang('base.login'); ?> 
										</button>
									</div>
								</form>
							</div>
							<div class="card-footer d-flex justify-content-center align-items-center">
								<a href="<?php echo $this->url('auth/register'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo \KN\Helpers\Base::lang('base.register'); ?>
								</a>
								<small class="vr mx-2"></small>
								<a href="<?php echo $this->url('auth/recovery'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo \KN\Helpers\Base::lang('base.recovery_account'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		