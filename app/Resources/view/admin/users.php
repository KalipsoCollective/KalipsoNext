		<div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h1 class="h3 fw-bold"><?php echo \KN\Helpers\Base::lang('base.users'); ?></h1>
						<p><?php echo $description; ?></p>
					</div>
					<div class="col-12">
						<div id="usersTable"></div>
					</div>
				</div>
			</div>
		</div>
		<script>
			(function() {
				new KalipsoTable({
					selector: "#usersTable",
					language: "<?php echo \KN\Helpers\Base::lang('lang.code'); ?>",
					source: '<?php echo $this->url('/management/users/list') ?>',
					columns: [ 
						{
							"searchable": {
								"type": "number",
								"min": 1,
								"max": 999
							},
							"orderable": true,
							"title": "#",
							"key": "id"
						},
						{
							"searchable": {
								"type": "text",
								"maxlenght": 50
							},
							"orderable": true,
							"title": "<?php echo \KN\Helpers\Base::lang('base.username'); ?>",
							"key": "username"
						},
						{
							"searchable": {
								"type": "text",
								"maxlenght": 50
							},
							"orderable": true,
							"title": "<?php echo \KN\Helpers\Base::lang('base.email'); ?>",
							"key": "email"
						},
						{
							"searchable": {
								"type": "text",
								"maxlenght": 50
							},
							"orderable": true,
							"title": "<?php echo \KN\Helpers\Base::lang('base.birth_date'); ?>",
							"key": "birthday"
						},
						{
							"searchable": {
								"type": "select",
								"datas": [
									{"value": 'active', "name": "<?php echo \KN\Helpers\Base::lang('base.active'); ?>"},
									{"value": 'passive', "name": "<?php echo \KN\Helpers\Base::lang('base.passive'); ?>"},
									{"value": 'deleted', "name": "<?php echo \KN\Helpers\Base::lang('base.deleted'); ?>"}
								],
							},
							"orderable": true,
							"title": "<?php echo \KN\Helpers\Base::lang('base.status'); ?>",
							"key": "status"
						},
						{
							"searchable": false,
							"orderable": false,
							"title": "<?php echo \KN\Helpers\Base::lang('base.action'); ?>",
							"key": "action"
						}
					],
					lengthOptions: [
						{
							"name": "10",
							"value": 10,
						},
						{
							"name": "1",
							"value": 1,
						},
						{
							"name": "50",
							"value": 50,
						},
						{
							"name": "100",
							"value": 100,
							"default": true
						},
						{
							"name": "<?php echo \KN\Helpers\Base::lang('base.all'); ?>",
							"value": 0,
						}
					],
					customize: {
						tableWrapClass: "table-responsive",
						tableClass: "table table-bordered",
						tableHeadClass: "",
						tableBodyClass: "",
						tableFooterClass: "",
						inputClass: "form-control form-control-sm",
						selectClass: "form-control form-control-sm",
						paginationUlClass: null,
		                paginationLiClass: null,
		                paginationAClass: null
					},
					tableHeader: {
						searchBar: true
					},
					tableFooter: {
						visible: true,
						searchBar: true
					}
				});
			})();
		</script>