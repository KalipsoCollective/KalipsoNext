		<div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h1 class="h3 fw-bold"><?php echo \KN\Helpers\Base::lang('base.dashboard'); ?></h1>
						<p><?php echo $description; ?></p>
					</div>
					<?php
					if ($this->authority('/management/users')) {
					?>
						<div class="col-12 col-md-3">
							<div class="card shadow mb-4">
								<div class="card-body">
									<h2 class="card-title fw-bold"><i class="ti ti-user"></i> <?php echo $count['users']; ?></h2>
									<p class="card-text fw-bolder"><?php echo \KN\Helpers\Base::lang('base.users'); ?></p>
									<div class="d-flex">
										<a href="<?php echo $this->url('/management/users'); ?>" class="btn btn-dark btn-sm ms-auto">
											<?php echo \KN\Helpers\Base::lang('base.view'); ?> <i class="ti ti-arrow-right"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php 
					}
					if ($this->authority('/management/roles')) {
					?>
						<div class="col-12 col-md-3">
							<div class="card shadow mb-4">
								<div class="card-body">
									<h2 class="card-title fw-bold"><i class="ti ti-users"></i> <?php echo $count['user_roles']; ?></h2>
									<p class="card-text fw-bolder"><?php echo \KN\Helpers\Base::lang('base.user_roles'); ?></p>
									<div class="d-flex">
										<a href="<?php echo $this->url('/management/roles'); ?>" class="btn btn-dark btn-sm ms-auto">
											<?php echo \KN\Helpers\Base::lang('base.view'); ?> <i class="ti ti-arrow-right"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php 
					}

					if ($this->authority('/management/sessions')) {
					?>
						<div class="col-12 col-md-3">
							<div class="card shadow mb-4">
								<div class="card-body">
									<h2 class="card-title fw-bold"><i class="ti ti-devices"></i> <?php echo $count['sessions']; ?></h2>
									<p class="card-text fw-bolder"><?php echo \KN\Helpers\Base::lang('base.sessions'); ?></p>
									<div class="d-flex">
										<a href="<?php echo $this->url('/management/sessions'); ?>" class="btn btn-dark btn-sm ms-auto">
											<?php echo \KN\Helpers\Base::lang('base.view'); ?> <i class="ti ti-arrow-right"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php 
					}

					if ($this->authority('/management/logs')) {
					?>
						<div class="col-12 col-md-3">
							<div class="card shadow mb-4">
								<div class="card-body">
									<h2 class="card-title fw-bold"><i class="ti ti-list-search"></i> <?php echo $count['logs']; ?></h2>
									<p class="card-text fw-bolder"><?php echo \KN\Helpers\Base::lang('base.logs'); ?></p>
									<div class="d-flex">
										<a href="<?php echo $this->url('/management/logs'); ?>" class="btn btn-dark btn-sm ms-auto">
											<?php echo \KN\Helpers\Base::lang('base.view'); ?> <i class="ti ti-arrow-right"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php 
					}   ?>
				</div>
			</div>
		</div>