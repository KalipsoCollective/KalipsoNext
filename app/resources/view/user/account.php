		<div class="container account-pages">
			<div class="row justify-content-center">
				<div class="col-12 col-md-9">
					<div class="row">
						<div class="col-12 col-lg-4">
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
						<div class="col-12 col-lg-8">
							<?php 
							echo self::alert();

							switch (self::$request['request']) {

								case '/account/profile':
									?>
									<form class="row g-2">
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
												<input type="password" name="password" class="form-control" id="eM" placeholder="<?php echo self::lang('password'); ?>">
												<label for="eM"><?php echo self::lang('password'); ?></label>
											</div>
										</div>
									</div>
									<?php
									break;

								case '/account/sessions':
									// (new UserController)->
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
		