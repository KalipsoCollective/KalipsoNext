		<div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="d-flex align-items-center">
							<h1 class="h3 fw-bold"><?php echo \KN\Helpers\Base::lang('base.user_roles'); ?></h1>
							<button data-bs-toggle="modal" data-bs-target="#addModal" class="btn btn-success ms-auto"><?php echo \KN\Helpers\Base::lang('base.add_new'); ?></button>
						</div>
						<p><?php echo $description; ?></p>
					</div>
					<div class="col-12">
						<div id="rolesTable"></div>
					</div>
				</div>
			</div>
		</div>
		<?php print_r($roles); ?>
		<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="addModalLabel"><?php echo \KN\Helpers\Base::lang('base.add_new'); ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo \KN\Helpers\Base::lang('base.close'); ?>"></button>
					</div>
					<div class="modal-body">
						<form class="row g-2" data-kn-form id="roleAdd" method="post" action="<?php echo $this->url('management/roles/add'); ?>">
							<div class="col-12 form-info">
								
							</div>
							<div class="col-12">
								<div class="form-floating">
									<input type="text" class="form-control" required name="name" id="roleName" placeholder="<?php echo \KN\Helpers\Base::lang('base.name'); ?>">
									<label for="roleName"><?php echo \KN\Helpers\Base::lang('base.name'); ?></label>
								</div>
							</div>
							<div class="col-12">
								<div class="form-floating">
									<select class="form-select" id="roleRoutes" required multiple style="height: 300px" name="routes[]" aria-label="<?php echo \KN\Helpers\Base::lang('base.routes'); ?>">
										<?php
										foreach ($roles as $route => $detail) {
											echo '
											<option value="' . $route . '"' . ($detail['default'] ? ' selected' : '') . '>
												' . \KN\Helpers\Base::lang($detail['name']) . '
											</option>';
										}	?>
									</select>
									<label for="roleRoutes"><?php echo \KN\Helpers\Base::lang('base.routes'); ?></label>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo \KN\Helpers\Base::lang('base.close'); ?></button>
						<button type="submit" form="roleAdd" class="btn btn-success"><?php echo \KN\Helpers\Base::lang('base.add'); ?></button>
					</div>
				</div>
			</div>
		</div>