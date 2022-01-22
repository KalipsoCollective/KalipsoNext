		<div class="container account-pages">
			<div class="row justify-content-center align-items-center">
				<div class="col-12 col-lg-3 col-md-4 col-sm-6">
					<div class="card">
						<div class="card-header">
							<?php echo self::lang('login'); ?>
						</div>
						<div class="card-body">
							<form method="post" action="<?php echo self::base('account/login'); ?>">
								<input type="hidden" name="_token" value="<?php echo self::createCSRF(); ?>">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		