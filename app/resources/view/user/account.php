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
									echo 'profile';
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
		