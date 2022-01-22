		<div class="container account-pages">
			<div class="row justify-content-center align-items-center">
				<div class="col-12 col-lg-3 col-md-4 col-sm-6">
					<div class="card">
						<div class="card-header">
							<?php echo self::lang('login'); ?>
						</div>
						<div class="card-body">
							<form method="post" action="<?php echo self::base('account/login'); ?>">
								<?php echo self::createCSRF(); ?>
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="uN" placeholder="<?php echo self::lang('email_or_username'); ?>" required>
									<label for="uN"><?php echo self::lang('email_or_username'); ?></label>
								</div>
								<div class="form-floating mb-3">
									<input type="password" class="form-control" id="uP" placeholder="<?php echo self::lang('password'); ?>" required>
									<label for="uP"><?php echo self::lang('password'); ?></label>
								</div>
								<div class="d-grid">
									<button type="submit" class="btn btn-primary btn-block"><?php echo self::lang('sign_in'); ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		