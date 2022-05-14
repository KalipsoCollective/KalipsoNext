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

		<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						...
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Save changes</button>
					</div>
				</div>
			</div>
		</div>