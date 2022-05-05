		<script src="<?php echo KN\Helpers\Base::assets('libs/bootstrap/bootstrap.bundle.min.js'); ?>"></script>
		<script>
			function init() {
	
				// Stored alert remove action
				const alerts = document.querySelectorAll('.kn-alert');
				if (alerts.length) {

					for (var i = alerts.length - 1; i >= 0; i--) {
						let element = alerts[i]
						setTimeout(() => {
							element.classList.add('out');
							setTimeout(() => {
								element.remove();
							}, 800);
						}, 5000);
						
					}
				}

				let tableVariables = {
					usersTable: {
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
								"key": "u_name"
							},
							{
								"searchable": {
									"type": "text",
									"maxlenght": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.name'); ?>",
								"key": "name"
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
								"key": "birth_date"
							},
							{
								"searchable": {
									"type": "text",
									"maxlenght": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.role'); ?>",
								"key": "role"
							},
							{
								"searchable": {
									"type": "date",
									"maxlenght": 50
								},
								"orderable": true,
								"title": "<?php echo \KN\Helpers\Base::lang('base.created_at'); ?>",
								"key": "created"
							},
							{
								"searchable": {
									"type": "date",
									"maxlenght": 50
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
					}
				}

				for(const [key, value] of Object.entries(tableVariables)) {
					window[key] = new KalipsoTable(value);
				}

			}
		</script>
		<script src="<?php echo KN\Helpers\Base::assets('libs/kalipsotable/l10n/tr.js'); ?>"></script>
		<script src="<?php echo KN\Helpers\Base::assets('libs/kalipsotable/kalipso.table.js'); ?>"></script>
		<script src="<?php echo KN\Helpers\Base::assets('js/kalipso.next.js'); ?>"></script>
	</body>
</html>