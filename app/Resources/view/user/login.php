		<div class="wrap mt-5">
			<div class="container">
				<div class="row pt-5">
					<div class="col-12 col-lg-3 col-md-4 col-sm-6">
						<div class="card">
							<div class="card-header">
								<?php echo \KN\Helpers\Base::lang('base.login'); ?>
							</div>
							<div class="card-body">
								<?php echo \KN\Helpers\Base::alert(); ?>
								<form method="post" action="<?php echo \KN\Helpers\Base::base('auth/login'); ?>" data-vpjax>
									<?php echo \KN\Helpers\Base::createCSRF(); ?>
									<div class="form-floating mb-3">
										<input type="text" name="username" class="form-control" id="uN" placeholder="<?php echo \KN\Helpers\Base::lang('email_or_username'); ?>" required <?php echo \KN\Helpers\Base::inputValue('username'); ?>>
										<label for="uN"><?php echo \KN\Helpers\Base::lang('email_or_username'); ?></label>
									</div>
									<div class="form-floating mb-3">
										<input type="password" name="password" class="form-control" id="uP" placeholder="<?php echo \KN\Helpers\Base::lang('password'); ?>" required>
										<label for="uP"><?php echo \KN\Helpers\Base::lang('password'); ?></label>
									</div>
									<div class="d-grid">
										<button type="submit" class="btn btn-primary btn-block">
											<?php echo \KN\Helpers\Base::lang('sign_in'); ?> 
											<span class="mdi mdi-arrow-right"></span>
										</button>
									</div>
								</form>
							</div>
							<div class="card-footer d-flex justify-content-center align-items-center">
								<a href="<?php echo \KN\Helpers\Base::base('account/register'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo \KN\Helpers\Base::lang('register'); ?>
								</a>
								<small class="vr mx-2"></small>
								<a href="<?php echo \KN\Helpers\Base::base('account/recovery'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo \KN\Helpers\Base::lang('recovery_account'); ?>
								</a>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-3 col-md-4 col-sm-6">
						<div class="card">
							<div class="card-header">
								<?php echo \KN\Helpers\Base::lang('login'); ?>
							</div>
							<div class="card-body">
								<?php echo \KN\Helpers\Base::alert(); ?>
								<form method="post" action="<?php echo \KN\Helpers\Base::base('account/login'); ?>" data-vpjax>
									<?php echo \KN\Helpers\Base::createCSRF(); ?>
									<div class="form-floating mb-3">
										<input type="text" name="username" class="form-control" id="uN" placeholder="<?php echo \KN\Helpers\Base::lang('email_or_username'); ?>" required <?php echo \KN\Helpers\Base::inputValue('username'); ?>>
										<label for="uN"><?php echo \KN\Helpers\Base::lang('email_or_username'); ?></label>
									</div>
									<div class="form-floating mb-3">
										<input type="password" name="password" class="form-control" id="uP" placeholder="<?php echo \KN\Helpers\Base::lang('password'); ?>" required>
										<label for="uP"><?php echo \KN\Helpers\Base::lang('password'); ?></label>
									</div>
									<div class="d-grid">
										<button type="submit" class="btn btn-primary btn-block">
											<?php echo \KN\Helpers\Base::lang('sign_in'); ?> 
											<span class="mdi mdi-arrow-right"></span>
										</button>
									</div>
								</form>
							</div>
							<div class="card-footer d-flex justify-content-center align-items-center">
								<a href="<?php echo \KN\Helpers\Base::base('account/register'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo \KN\Helpers\Base::lang('register'); ?>
								</a>
								<small class="vr mx-2"></small>
								<a href="<?php echo \KN\Helpers\Base::base('account/recovery'); ?>" class="btn btn-outline-primary btn-sm">
									<?php echo \KN\Helpers\Base::lang('recovery_account'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		