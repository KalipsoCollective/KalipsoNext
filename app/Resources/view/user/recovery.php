		<div class="container account-pages">
			<div class="row justify-content-center align-items-center">
				<div class="col-12 col-lg-3 col-md-4 col-sm-6">
					<div class="card kn-card">
						<div class="card-header">
							<?php echo self::lang('recovery_account'); ?>
						</div>
						<div class="card-body">
							<?php echo self::alert(); ?>
							<form method="post" action="<?php echo self::base('account/recovery'); ?>" data-vpjax>
								<?php 
								echo self::createCSRF();
								if (isset(self::$request['parameters']['token']) !== false) {
								?>
									<input type="hidden" name="token" <?php echo self::inputValue('token'); ?>>
									<div class="form-floating mb-3">
										<input type="password" name="password" class="form-control" id="uP" placeholder="<?php echo self::lang('new_password'); ?>" required>
										<label for="uP"><?php echo self::lang('new_password'); ?></label>
									</div>
								<?php
								} else {
								?>
									<div class="form-floating mb-3">
										<input type="email" name="email" class="form-control" id="uM" placeholder="<?php echo self::lang('email'); ?>" required <?php echo self::inputValue('email'); ?>>
										<label for="uM"><?php echo self::lang('email'); ?></label>
									</div>
								<?php
								}	?>
								<div class="d-grid">
									<button type="submit" class="btn btn-primary btn-block">
										<?php echo self::lang('recovery_account'); ?> 
										<span class="mdi mdi-arrow-right"></span>
									</button>
								</div>
							</form>
						</div>
						<div class="card-footer d-flex justify-content-center align-items-center">
							<a href="<?php echo self::base('account/login'); ?>" class="btn btn-outline-primary btn-sm">
								<?php echo self::lang('login'); ?>
							</a>
							<small class="vr mx-2"></small>
							<a href="<?php echo self::base('account/register'); ?>" class="btn btn-outline-primary btn-sm">
								<?php echo self::lang('register'); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		