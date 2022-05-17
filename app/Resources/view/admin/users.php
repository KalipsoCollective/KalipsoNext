		<div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="d-flex align-items-center">
							<h1 class="h3 fw-bold"><?php echo \KN\Helpers\Base::lang('base.users'); ?></h1>
							<button data-bs-toggle="modal" data-bs-target="#addModal" class="btn btn-success ms-auto"><?php echo \KN\Helpers\Base::lang('base.add_new'); ?></button>
						</div>
						<p><?php echo $description; ?></p>
					</div>
					<div class="col-12">
						<div id="usersTable"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="addModalLabel"><?php echo \KN\Helpers\Base::lang('base.add_new'); ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo \KN\Helpers\Base::lang('base.close'); ?>"></button>
					</div>
					<div class="modal-body">
						<form class="row g-2" data-kn-form id="userAdd" method="post" action="<?php echo $this->url('management/users/add'); ?>">
							<div class="form-loader">
								<div class="spinner-border text-light" user="status">
									<span class="visually-hidden"><?php echo \KN\Helpers\Base::lang('base.loading'); ?></span>
								</div>
							</div>
							<div class="col-12 form-info">
							</div>
							<div class="col-12">
								<div class="form-floating">
									<input type="text" class="form-control" required name="name" id="userName" placeholder="<?php echo \KN\Helpers\Base::lang('base.name'); ?>">
									<label for="userName"><?php echo \KN\Helpers\Base::lang('base.name'); ?></label>
								</div>
							</div>
							<div class="col-12">
								<div class="form-floating">
									<select class="form-select" id="userRoutes" required multiple style="height: 300px" name="routes[]" aria-label="<?php echo \KN\Helpers\Base::lang('base.routes'); ?>">
									</select>
									<label for="userRoutes"><?php echo \KN\Helpers\Base::lang('base.routes'); ?></label>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo \KN\Helpers\Base::lang('base.close'); ?></button>
						<button type="submit" form="userAdd" class="btn btn-success"><?php echo \KN\Helpers\Base::lang('base.add'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<!--
		<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="editModalLabel"><?php echo \KN\Helpers\Base::lang('base.view'); ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo \KN\Helpers\Base::lang('base.close'); ?>"></button>
					</div>
					<div class="modal-body">
						<form class="row g-2" data-kn-form id="userUpdate" method="post" action="">
							<div class="form-loader">
								<div class="spinner-border text-light" user="status">
									<span class="visually-hidden"><?php echo \KN\Helpers\Base::lang('base.loading'); ?></span>
								</div>
							</div>
							<div class="col-12 form-info">
							</div>
							<div class="col-12">
								<div class="form-floating">
									<input type="text" class="form-control" required name="name" id="theRoleName" placeholder="<?php echo \KN\Helpers\Base::lang('base.name'); ?>">
									<label for="theRoleName"><?php echo \KN\Helpers\Base::lang('base.name'); ?></label>
								</div>
							</div>
							<div class="col-12">
								<div class="form-floating">
									<select class="form-select" id="theRoleRoutes" required multiple style="height: 300px" name="routes[]" aria-label="<?php echo \KN\Helpers\Base::lang('base.routes'); ?>">
									</select>
									<label for="theRoleRoutes"><?php echo \KN\Helpers\Base::lang('base.routes'); ?></label>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo \KN\Helpers\Base::lang('base.close'); ?></button>
						<button type="submit" form="userUpdate" class="btn btn-primary"><?php echo \KN\Helpers\Base::lang('base.update'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="deleteModalLabel"><?php echo \KN\Helpers\Base::lang('base.delete_user'); ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo \KN\Helpers\Base::lang('base.close'); ?>"></button>
					</div>
					<div class="modal-body">
						<form class="row g-2" data-kn-form id="userDelete" method="post" action="">
							<div class="form-loader">
								<div class="spinner-border text-light" user="status">
									<span class="visually-hidden"><?php echo \KN\Helpers\Base::lang('base.loading'); ?></span>
								</div>
							</div>
							<div class="col-12 form-info">
							</div>
							<div class="col-12">
								<div class="form-floating">
									<select class="form-select" name="transfer_user" id="availableRoles" required 
									aria-label="<?php echo \KN\Helpers\Base::lang('base.routes'); ?>">
									</select>
									<label for="availableRoles"><?php echo \KN\Helpers\Base::lang('base.user_to_transfer_users'); ?></label>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo \KN\Helpers\Base::lang('base.close'); ?></button>
						<button type="submit" form="userDelete" class="btn btn-danger"><?php echo \KN\Helpers\Base::lang('base.delete'); ?></button>
					</div>
				</div>
			</div>
		</div> -->