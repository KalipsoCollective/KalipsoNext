		<script src="<?php echo KN\Helpers\Base::assets('libs/bootstrap/bootstrap.bundle.min.js'); ?>"></script>
		<script>
			function init() {

				let tableVariables = {
					usersTable: {
						selector: "#usersTable",
						language: "<?php echo \KN\Helpers\Base::lang('lang.code'); ?>",
						server: true,
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
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.username'); ?>",
								"key": "u_name"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.name'); ?>",
								"key": "name"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.email'); ?>",
								"key": "email"
							},
							{
								"searchable": {
									"type": "date",
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.birth_date'); ?>",
								"key": "birth_date"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.role'); ?>",
								"key": "role"
							},
							{
								"searchable": {
									"type": "date",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.created_at'); ?>",
								"key": "created"
							},
							{
								"searchable": {
									"type": "date",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.updated_at'); ?>",
								"key": "updated"
							},
							{
								"searchable": {
									"type": "select",
									"datas": [
										{"value": 'active', "name": "<?php echo \KN\Helpers\Base::lang('base.active'); ?>"},
										{"value": 'passive', "name": "<?php echo \KN\Helpers\Base::lang('base.passive'); ?>"}
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
						customize: {
							tableWrapClass: "table-responsive",
							tableClass: "table table-bordered",
							inputClass: "form-control form-control-sm",
							selectClass: "form-control form-control-sm",
						},
						tableHeader: {
							searchBar: true
						},
						tableFooter: {
							visible: true,
							searchBar: true
						}
					},
					rolesTable: {
						selector: "#rolesTable",
						language: "<?php echo \KN\Helpers\Base::lang('lang.code'); ?>",
						server: true,
						source: '<?php echo $this->url('/management/roles/list') ?>',
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
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.name'); ?>",
								"key": "name"
							},
							{
								"searchable": false,
								"orderable": false,
								"title": "<?php echo \KN\Helpers\Base::lang('base.routes'); ?>",
								"key": "routes"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.users'); ?>",
								"key": "users"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.created_at'); ?>",
								"key": "created"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.updated_at'); ?>",
								"key": "updated"
							},
							{
								"searchable": false,
								"orderable": false,
								"title": "<?php echo \KN\Helpers\Base::lang('base.action'); ?>",
								"key": "action"
							}
						],
						customize: {
							tableWrapClass: "table-responsive",
							tableClass: "table table-bordered",
							inputClass: "form-control form-control-sm",
							selectClass: "form-control form-control-sm",
						},
						tableHeader: {
							searchBar: true
						},
						tableFooter: {
							visible: true,
							searchBar: true
						}
					},
					logsTable: {
						selector: "#logsTable",
						language: "<?php echo \KN\Helpers\Base::lang('lang.code'); ?>",
						server: true,
						source: '<?php echo $this->url('/management/logs/list') ?>',
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
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.endpoint'); ?>",
								"key": "endpoint"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.request'); ?>",
								"key": "req"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.middleware'); ?>",
								"key": "middleware"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.controller'); ?>",
								"key": "controller"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.ip'); ?>",
								"key": "ip"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.user'); ?>",
								"key": "user"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.execute_time'); ?>",
								"key": "exec_time"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.created_at'); ?>",
								"key": "created"
							},
							{
								"searchable": false,
								"orderable": false,
								"title": "<?php echo \KN\Helpers\Base::lang('base.action'); ?>",
								"key": "action"
							}
						],
						customize: {
							tableWrapClass: "table-responsive",
							tableClass: "table table-bordered",
							inputClass: "form-control form-control-sm",
							selectClass: "form-control form-control-sm",
						},
						tableHeader: {
							searchBar: true
						},
						tableFooter: {
							visible: true,
							searchBar: true
						}
					},
					sessionsTable: {
						selector: "#sessionsTable",
						language: "<?php echo \KN\Helpers\Base::lang('lang.code'); ?>",
						server: true,
						source: '<?php echo $this->url('/management/sessions/list') ?>',
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
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.auth_code'); ?>",
								"key": "auth_code"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.user'); ?>",
								"key": "user"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.role'); ?>",
								"key": "role"
							},
							{
								"searchable": true,
								"orderable": false,
								"title": "<?php echo \KN\Helpers\Base::lang('base.device'); ?>",
								"key": "header"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.ip'); ?>",
								"key": "ip"
							},
							{
								"searchable": false,
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.last_action_date'); ?>",
								"key": "last_action_date"
							},
							{
								"searchable": {
									"type": "text",
									"maxlength": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.last_action_point'); ?>",
								"key": "last_action_point"
							}
						],
						customize: {
							tableWrapClass: "table-responsive",
							tableClass: "table table-bordered",
							inputClass: "form-control form-control-sm",
							selectClass: "form-control form-control-sm",
						},
						tableHeader: {
							searchBar: true
						},
						tableFooter: {
							visible: true,
							searchBar: true
						}
					}
				}

				for(const [key, value] of Object.entries(tableVariables)) {
					window[key] = new KalipsoTable(value);
				}

			}
		</script>
		<script src="<?php echo KN\Helpers\Base::assets('libs/kalipsotable/l10n/tr.js'); ?>"></script>
		<script src="<?php echo KN\Helpers\Base::assets('libs/kalipsotable/kalipso.table.js'); ?>"></script>
		<script src="<?php echo KN\Helpers\Base::assets('js/kalipso.libs.js'); ?>"></script>
		<script src="<?php echo KN\Helpers\Base::assets('js/kalipso.next.js'); ?>"></script>
	</body>
</html>