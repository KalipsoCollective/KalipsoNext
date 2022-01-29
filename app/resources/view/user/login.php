		<div class="container account-pages">
			<div class="row justify-content-center align-items-center">
				<div class="col-12 col-lg-3 col-md-4 col-sm-6">
					<div class="card kn-card">
						<div class="card-header">
							<?php echo self::lang('login'); ?>
						</div>
						<div class="card-body">
							<?php echo self::alert(); ?>
							<form method="post" action="<?php echo self::base('account/login'); ?>" data-vpjax>
								<?php echo self::createCSRF(); ?>
								<div class="form-floating mb-3">
									<input type="text" name="username" class="form-control" id="uN" placeholder="<?php echo self::lang('email_or_username'); ?>" required <?php echo self::inputValue('username'); ?>>
									<label for="uN"><?php echo self::lang('email_or_username'); ?></label>
								</div>
								<div class="form-floating mb-3">
									<input type="password" name="password" class="form-control" id="uP" placeholder="<?php echo self::lang('password'); ?>" required>
									<label for="uP"><?php echo self::lang('password'); ?></label>
								</div>
								<div class="d-grid">
									<button type="submit" class="btn btn-primary btn-block">
										<?php echo self::lang('sign_in'); ?> 
										<span class="mdi mdi-arrow-right"></span>
									</button>
								</div>
							</form>
						</div>
						<div class="card-footer d-flex justify-content-center align-items-center">
							<a href="<?php echo self::base('account/register'); ?>" class="btn btn-outline-primary btn-sm">
								<?php echo self::lang('register'); ?>
							</a>
							<small class="vr mx-2"></small>
							<a href="<?php echo self::base('account/recovery'); ?>" class="btn btn-outline-primary btn-sm">
								<?php echo self::lang('recovery_account'); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		