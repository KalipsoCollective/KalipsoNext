		<div class="wrap">
			<div class="container">
				<div class="row justify-content-center align-items-center">
					<div class="col-12 col-xl-4 col-lg-5 col-md-6">
						<div class="card shadow shadow-sm">
							<div class="card-header">
								<h1 class="fw-bolder h6 m-0 text-center"><?php echo \KN\Helpers\Base::lang('base.register'); ?></h1>
							</div>
							<div class="card-body">
								<?php echo \KN\Helpers\Base::alert($this->response->alerts); ?>
								<form method="post" action="<?php echo $this->url('auth/register'); ?>" data-vpjax>
									<?php echo \KN\Helpers\Base::createCSRF(); ?>
									<div class="form-floating mb-3">
										<input type="text" name="email" class="form-control" id="email" placeholder="<?php echo KN\Helpers\Base::lang('base.email'); ?>" required <?php echo KN\Helpers\Base::inputValue('email', $this->request->params); ?>>
										<label for="email"><?php echo KN\Helpers\Base::lang('base.email'); ?></label>
									</div>
									<div class="form-floating mb-3">
										<input type="text" name="username" class="form-control" id="username" placeholder="<?php echo KN\Helpers\Base::lang('base.username'); ?>" required <?php echo KN\Helpers\Base::inputValue('username', $this->request->params); ?>>
										<label for="username"><?php echo KN\Helpers\Base::lang('base.username'); ?></label>
									</div>
									<div class="form-floating mb-3">
										<input type="text" name="name" class="form-control" id="name" placeholder="<?php echo KN\Helpers\Base::lang('base.name'); ?>" required <?php echo KN\Helpers\Base::inputValue('name', $this->request->params); ?>>
										<label for="name"><?php echo KN\Helpers\Base::lang('base.name'); ?></label>
									</div>
									<div class="form-floating mb-3">
										<input type="text" name="surname" class="form-control" id="surname" placeholder="<?php echo KN\Helpers\Base::lang('base.surname'); ?>" required <?php echo KN\Helpers\Base::inputValue('surname', $this->request->params); ?>>
										<label for="surname"><?php echo KN\Helpers\Base::lang('base.surname'); ?></label>
									</div>
									<div class="form-floating mb-3">
										<input type="password" name="password" class="form-control" id="uP" placeholder="<?php echo KN\Helpers\Base::lang('base.password'); ?>" required>
										<label for="uP"><?php echo KN\Helpers\Base::lang('base.password'); ?></label>
									</div>
									<div class="d-grid">
										<button type="submit" class="btn btn-primary">
											<?php echo \KN\Helpers\Base::lang('base.register'); ?> 
											<span class="mdi mdi-arrow-right"></span>
										</button>
									</div>
								</form>
							</div>
							<div class="card-footer d-flex justify-content-center align-items-center">
								<a href="<?php echo $this->url('auth/login'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo \KN\Helpers\Base::lang('base.login'); ?>
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