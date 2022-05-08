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
						server: false,
						//source: '<?php echo $this->url('/management/users/list') ?>',
						source: [
							{id: 1, u_name: 'alonzo', name: 'Alonzo Forza', email: 'alonzof@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2022.05.05', updated: '2022.05.05', status: 'active', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 2, u_name: 'carlb', name: 'Carl Ben', email: 'carlb@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2022.05.06', updated: '2022.05.05', status: 'passive', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 3, u_name: 'dan14edward', name: 'Dan Edward', email: 'dan14edward@outlook.com', birth_date: '1996.08.14', role: 'admin', created: '2022.05.08', updated: '2022.05.05', status: 'active', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 4, u_name: 'hankfrank', name: 'Frank Hank', email: 'hankfrank@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2022.05.09', updated: '2022.05.05', status: 'active', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 5, u_name: 'thomopeter22', name: 'Thomas Peter', email: 'thomopeter@hotmail.com', birth_date: '1996.08.14', role: 'admin', created: '2022.08.22', updated: '2022.05.05', status: 'passive', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 6, u_name: 'time', name: 'Edward Tim', email: 'tim.edward@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2021.04.13', updated: '2022.05.05', status: 'active', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 7, u_name: 'wm', name: 'Walter Monte', email: 'waltermontee@outlook.com', birth_date: '1996.08.14', role: 'admin', created: '2021.09.10', updated: '2022.05.05', status: 'active', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 8, u_name: 'george.c', name: 'George Corte', email: 'george.c@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2022.07.12', updated: '2022.05.05', status: 'deleted', action: ''},
							{id: 9, u_name: 'hi.ben', name: 'Ben Thomas', email: 'ben_thomas@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2020.05.24', updated: '2022.05.05', status: 'active', action: '<button class="btn btn-danger btn-sm">Delete</button>'},
							{id: 10, u_name: 'otto_dan', name: 'Dan Otto', email: 'otto_dan@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2022.03.28', updated: '2022.05.05', status: 'deleted', action: ''},
							{id: 11, u_name: 'kotto_dan', name: 'Kenau Dan Otto', email: 'kotto_dan@gmail.com', birth_date: '1996.08.14', role: 'admin', created: '2022.03.28', updated: '2022.05.05', status: 'deleted', action: ''}
						],
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
		<script src="<?php echo KN\Helpers\Base::assets('js/kalipso.next.js'); ?>"></script>
	</body>
</html>