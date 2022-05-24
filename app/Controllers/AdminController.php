<?php

/**
 * @package KN
 * @subpackage Controller
 */

declare(strict_types=1);

namespace KN\Controllers;

use KN\Core\Controller;
use KN\Helpers\Base;
use KN\Helpers\KalipsoTable;
use KN\Core\Model;
use KN\Model\Users;
use KN\Model\UserRoles;
use KN\Model\Sessions;
use KN\Model\Logs;

final class AdminController extends Controller {

	public function dashboard() {

		$users = (new Users)->count('id', 'total')->notWhere('status', 'deleted')->cache(60)->get();
		$userRoles = (new UserRoles)->count('id', 'total')->cache(60)->get();
		$sessions = (new Sessions)->count('id', 'total')->cache(60)->get();
		$logs = (new Logs)->count('id', 'total')->cache(60)->get();

		$count = [
			'users' => $users->total,
			'user_roles' => $userRoles->total,
			'sessions' => $sessions->total,
			'logs' => $logs->total
		];

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => [
				'title' => Base::lang('base.dashboard') . ' | ' . Base::lang('base.management'),
				'description' => Base::lang('base.dashboard_message'),
				'count' => $count,
			],
			'view' => ['admin.dashboard', 'admin']
		];

	}


	public function users() {

		$userRoles = (new UserRoles)->select('name, id')->orderBy('name', 'asc')->getAll();

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => [
				'title' => Base::lang('base.users') . ' | ' . Base::lang('base.management'),
				'description' => Base::lang('base.users_message'),
				'userRoles' => $userRoles
			],
			'view' => ['admin.users', 'admin']
		];

	}

	public function userList() {

		$container = $this->get();

		$tableOp = (new KalipsoTable())
			->db((new Users)->pdo)
			->from('(SELECT 
					x.id, 
					x.u_name, 
					x.f_name,
					x.l_name,
					x.email, 
					IFNULL(FROM_UNIXTIME(x.b_date, "%Y.%m.%d"), "-") AS birth_date,
					IFNULL((SELECT name FROM user_roles WHERE status = "active" AND id = x.role_id), "-") AS role,
					FROM_UNIXTIME(x.created_at, "%Y.%m.%d %H:%i") AS created,
					IFNULL(FROM_UNIXTIME(x.updated_at, "%Y.%m.%d"), "-") AS updated,
					x.status
				FROM `users` x WHERE status != "deleted") AS raw')
			->process([
				'id' => [
					'primary' => true,
				],
				'u_name' => [],
				'name' => [
					'exclude' => true,
					'formatter' => function($row) {

						$name = trim($row->f_name . ' ' . $row->l_name);
						return $name == '' ? '-' : $name;
					}
				],
				'email' => [],
				'birth_date' => [],
				'role' => [],
				'created' => [],
				'updated' => [],
				'status' => [
					'formatter' => function ($row) {

						switch ($row->status) {
							case 'deleted':
								$status = 'text-danger';
								break;

							case 'passive':
								$status = 'text-warning';
								break;
								
							default:
								$status = 'text-success';
								break;
						}

						return '<span class="' . $status . '">' . Base::lang('base.' . $row->status) . '</span>';

					}
				],
				'action' => [
					'exclude' => true,
					'formatter' => function($row) use ($container) {

						$buttons = '';
						if ($container->authority('management/users/:id')) {
							$buttons .= '
							<button type="button" class="btn btn-light" 
								data-kn-action="'.$this->get()->url('/management/users/' . $row->id ).'">
								' . Base::lang('base.view') . '
							</button>';
						}

						if ($container->authority('management/users/:id/delete')) {
							$buttons .= '
							<button type="button" class="btn btn-danger" 
								data-kn-again="'.Base::lang('base.are_you_sure').'" 
								data-kn-action="'.$this->get()->url('/management/users/' . $row->id . '/delete').'">
								' . Base::lang('base.delete') . '
							</button>';
						}



						return '
						<div class="btn-group btn-group-sm" role="group" aria-label="'.Base::lang('base.action').'">
							'.$buttons.'
						</div>';
					}
				],
			])
			->output();


		//$arguments = (new KalipsoTable()->);

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $tableOp,
			'view' => null
		];

	}

	public function userAdd() {

		extract(Base::input([
			'email' => 'nulled_text',
			'u_name' => 'nulled_text',
			'f_name' => 'nulled_text',
			'l_name' => 'nulled_text',
			'role_id' => 'nulled_int',
			'password' => 'nulled_password'
		], $this->get('request')->params));

		$alerts = [];
		$arguments = [];

		$model = new Users();
		
		if ($email AND $u_name AND $role_id AND $password) {

			$userNameCheck = $model->count('id', 'total')->where('u_name', $u_name)->get();
			if ((int)$userNameCheck->total === 0) {

				$userEmailCheck = $model->count('id', 'total')->where('email', $email)->get();
				if ((int)$userEmailCheck->total === 0) {

					$insert = [
						'email' => $email,
						'u_name' => $u_name,
						'f_name' => $f_name,
						'l_name' => $l_name,
						'role_id' => $role_id,
						'password' => $password,
						'token' => Base::tokenGenerator(80),
						'status' => 'active'
					];

					$insert = $model->insert($insert);

					if ($insert) {

						$alerts[] = [
							'status' => 'success',
							'message' => Base::lang('base.user_successfully_added')
						];
						$arguments['form_reset'] = true;
						$arguments['modal_close'] = '#addModal';
						$arguments['table_reset'] = 'usersTable';

					} else {

						$alerts[] = [
							'status' => 'error',
							'message' => Base::lang('base.user_add_problem')
						];
					}

				} else {

					$alerts[] = [
						'status' => 'warning',
						'message' => Base::lang('base.email_is_already_used')
					];
					$arguments['manipulation'] = [
						'#userAdd [name="email"]' => [
							'class' => ['is-invalid'],
						]
					];

				}

			} else {

				$alerts[] = [
					'status' => 'warning',
					'message' => Base::lang('base.username_is_already_used')
				];
				$arguments['manipulation'] = [
					'#userAdd [name="u_name"]' => [
						'class' => ['is-invalid'],
					]
				];
			}

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.form_cannot_empty')
			];

			$arguments['manipulation'] = [];

			if ($email) {
				$arguments['manipulation']['#userAdd [name="email"]'] = [
					'class' => ['is-invalid'],
				];
			}

			if ($u_name) {
				$arguments['manipulation']['#userAdd [name="u_name"]'] = [
					'class' => ['is-invalid'],
				];
			}

			if ($role_id) {
				$arguments['manipulation']['#userAdd [name="role_id"]'] = [
					'class' => ['is-invalid'],
				];
			}

			if ($password) {
				$arguments['manipulation']['#userAdd [name="password"]'] = [
					'class' => ['is-invalid'],
				];
			}

		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function userDelete() {

		$id = (int)$this->get('request')->attributes['id'];

		$alerts = [];
		$arguments = [];

		$model = new Users();
		
		$getUser = $model->select('id, u_name')->where('id', $id)->get();
		if (! empty($getUser)) {

			if ($id !== Base::userData('id')) {

				$update = $model->where('id', $id)->update([
					'status' => 'deleted'
				]);

				if ($update) {

					(new Sessions())->where('user_id', $id)->delete();
					$alerts[] = [
						'status' => 'success',
						'message' => Base::lang('base.user_successfully_deleted')
					];
					$arguments['table_reset'] = 'usersTable';

				} else {

					$alerts[] = [
						'status' => 'error',
						'message' => Base::lang('base.user_delete_problem')
					];
				}

			} else {

				$alerts[] = [
					'status' => 'error',
					'message' => Base::lang('base.user_delete_problem_for_own_account')
				];
			}

			

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.record_not_found')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function userDetail() {

		$id = (int)$this->get('request')->attributes['id'];

		$alerts = [];
		$arguments = [];

		$model = new Users();
		$getUser = $model->select('id, u_name, f_name, l_name, email, role_id')->where('id', $id)->get();
		if (! empty($getUser)) {

			$userRoles = (new UserRoles)->select('name, id')->orderBy('name', 'asc')->getAll();
			$options = '';

			foreach ($userRoles as $role) {
				$selected = $role->id == $getUser->role_id ? true : false;
				$options .= '
				<option value="' . $role->id. '"' . ($selected ? ' selected' : '') . '>
					' . $role->name . '
				</option>';
			}

			$arguments['modal_open'] = ['#editModal'];
			$arguments['manipulation'] = [
				'#userUpdate' => [
					'attribute' => ['action' => $this->get()->url('management/users/' . $id . '/update')],
				],
				'#theUserEmail' => [
					'attribute' => ['value' => $getUser->email],
				],
				'#theUserName' => [
					'attribute' => ['value' => $getUser->u_name],
				],
				'#thefName' => [
					'attribute' => $getUser->f_name ? ['value' => $getUser->f_name] : ['value' => ''],
				],
				'#thelName' => [
					'attribute' => $getUser->l_name ? ['value' => $getUser->l_name] : ['value' => ''],
				],
				'#theRoles' => [
					'html'	=> $options
				],
			];

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.record_not_found')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function userUpdate() {

		extract(Base::input([
			'email' => 'nulled_text',
			'u_name' => 'nulled_text',
			'f_name' => 'nulled_text',
			'l_name' => 'nulled_text',
			'role_id' => 'nulled_int',
			'password' => 'nulled_password'
		], $this->get('request')->params));

		$id = (int)$this->get('request')->attributes['id'];

		$alerts = [];
		$arguments = [];

		$model = new Users();
		$getUser = $model->select('id, u_name, f_name, l_name, email, role_id')->where('id', $id)->get();
		if (! empty($getUser)) {
		
			if ($email AND $u_name AND $role_id) {

				$userNameCheck = $model->count('id', 'total')->where('u_name', $u_name)->notWhere('id', $id)->get();
				if ((int)$userNameCheck->total === 0) {

					$userEmailCheck = $model->count('id', 'total')->where('email', $email)->notWhere('id', $id)->get();
					if ((int)$userEmailCheck->total === 0) {

						$update = [
							'email' => $email,
							'u_name' => $u_name,
							'f_name' => $f_name,
							'l_name' => $l_name,
							'role_id' => $role_id,
						];

						if ($password) {
							$update['password'] = $password;
						}

						$update = $model->where('id', $id)->update($update);

						if ($update) {

							if ($getUser->role_id !== $role_id) {
								(new Sessions)->where('user_id', $id)->update([
									'role_id' => $role_id,
									'update_session' => 'true'
								]);
							}

							$alerts[] = [
								'status' => 'success',
								'message' => Base::lang('base.user_successfully_updated')
							];
							$arguments['table_reset'] = 'usersTable';

						} else {

							$alerts[] = [
								'status' => 'error',
								'message' => Base::lang('base.user_update_problem')
							];
						}

					} else {

						$alerts[] = [
							'status' => 'warning',
							'message' => Base::lang('base.email_is_already_used')
						];
						$arguments['manipulation'] = [
							'#userAdd [name="email"]' => [
								'class' => ['is-invalid'],
							]
						];

					}

				} else {

					$alerts[] = [
						'status' => 'warning',
						'message' => Base::lang('base.username_is_already_used')
					];
					$arguments['manipulation'] = [
						'#userAdd [name="u_name"]' => [
							'class' => ['is-invalid'],
						]
					];
				}

			} else {

				$alerts[] = [
					'status' => 'warning',
					'message' => Base::lang('base.form_cannot_empty')
				];

				$arguments['manipulation'] = [];

				if ($email) {
					$arguments['manipulation']['#userUpdate [name="email"]'] = [
						'class' => ['is-invalid'],
					];
				}

				if ($u_name) {
					$arguments['manipulation']['#userUpdate [name="u_name"]'] = [
						'class' => ['is-invalid'],
					];
				}

				if ($role_id) {
					$arguments['manipulation']['#userUpdate [name="role_id"]'] = [
						'class' => ['is-invalid'],
					];
				}

			}

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.record_not_found')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function roles() {

		$roles = require(Base::path('app/Resources/endpoints.php'));

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => [
				'title' => Base::lang('base.user_roles') . ' | ' . Base::lang('base.management'),
				'description' => Base::lang('base.user_roles_message'),
				'roles' => $roles
			],
			'view' => ['admin.user_roles', 'admin']
		];

	}

	public function roleDetail() {

		$id = (int)$this->get('request')->attributes['id'];


		$alerts = [];
		$arguments = [];

		$model = new UserRoles();
		$getRole = $model->select('id, name, routes')->where('id', $id)->get();
		if (! empty($getRole)) {

			$options = '';
			$routes = strpos($getRole->routes, ',') !== false ? explode(',', $getRole->routes) : [$getRole->routes];

			$roles = require(Base::path('app/Resources/endpoints.php'));
			foreach ($roles as $route => $detail) {
				$selected = in_array($route, $routes) !== false ? true : false;
				$options .= '
				<option value="' . $route . '"' . ($selected ? ' selected' : '') . '>
					' . Base::lang($detail['name']) . '
				</option>';
			}

			$arguments['modal_open'] = ['#editModal'];
			$arguments['manipulation'] = [
				'#roleUpdate' => [
					'attribute' => ['action' => $this->get()->url('management/roles/' . $id . '/update')],
				],
				'#theRoleName' => [
					'attribute' => ['value' => $getRole->name],
				],
				'#theRoleRoutes' => [
					'html'	=> $options
				]
			];

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.record_not_found')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function roleList() {

		$container = $this->get();

		$tableOp = (new KalipsoTable())
			->db((new Users)->pdo)
			->from('(SELECT 
					x.id, 
					x.name, 
					x.routes, 
					(SELECT COUNT(id) FROM users WHERE status != "deleted" AND role_id = x.id) AS users,
					FROM_UNIXTIME(x.created_at, "%Y.%m.%d %H:%i") AS created,
					IFNULL(FROM_UNIXTIME(x.updated_at, "%Y.%m.%d %H:%i"), "-") AS updated
				FROM `user_roles` x) AS raw')
			->process([
				'id' => [
					'primary' => true,
				],
				'name' => [],
				'routes' => [
					'formatter' => function($row) {


						$title = '';
						$total = 0;
						$row->routes = strpos($row->routes, ',') !== false ? explode(',', $row->routes) : [$row->routes];
						$total = count($row->routes);
						$title = implode(' '.PHP_EOL, $row->routes);

						return '<span title="' . $title . '" class="badge bg-dark">' . $total . '</span>';

					}
				],
				'users' => [
					'formatter' => function($row) {

						return '<span class="badge bg-light text-dark">' . $row->users . '</span>';

					}
				],
				'created' => [],
				'updated' => [],
				'action' => [
					'exclude' => true,
					'formatter' => function($row) use ($container) {

						$buttons = '';
						if ($container->authority('management/roles/:id')) {
							$buttons .= '
							<button type="button" class="btn btn-light" 
								data-kn-action="'.$this->get()->url('/management/roles/' . $row->id ).'">
								' . Base::lang('base.view') . '
							</button>';
						}

						if ($container->authority('management/roles/:id/delete')) {
							$buttons .= '
							<button type="button" class="btn btn-danger" 
								data-kn-again="'.Base::lang('base.are_you_sure').'" 
								data-kn-action="'.$this->get()->url('/management/roles/' . $row->id . '/delete').'">
								' . Base::lang('base.delete') . '
							</button>';
						}

						return '
						<div class="btn-group btn-group-sm" role="group" aria-label="'.Base::lang('base.action').'">
							'.$buttons.'
						</div>';
					}
				],
			])
			->output();

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $tableOp,
			'view' => null
		];

	}

	public function roleAdd() {

		extract(Base::input([
			'name' => 'nulled_text',
			'routes' => 'nulled_text'
		], $this->get('request')->params));

		$alerts = [];
		$arguments = [];

		$routes = is_array($routes) ? implode(',', $routes) : $routes;
		$insert = [
			'name' => $name,
			'routes' => $routes,
		];

		$model = new UserRoles();
		
		$getRole = $model->count('id', 'total')->where('name', $name)->get();
		if ((int)$getRole->total === 0) {

			$insert = $model->insert($insert);

			if ($insert) {

				$alerts[] = [
					'status' => 'success',
					'message' => Base::lang('base.user_role_successfully_added')
				];
				$arguments['form_reset'] = true;
				$arguments['modal_close'] = '#addModal';
				$arguments['table_reset'] = 'rolesTable';

			} else {

				$alerts[] = [
					'status' => 'error',
					'message' => Base::lang('base.user_role_add_problem')
				];
			}

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.same_name_alert')
			];
			$arguments['manipulation'] = [
				'[name="name"]' => [
					'class' => ['is-invalid'],
				]
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function roleDelete() {

		$id = (int)$this->get('request')->attributes['id'];

		$alerts = [];
		$arguments = [];

		$model = new UserRoles();
		
		$getRole = $model->select('id, name')->where('id', $id)->get();
		if (! empty($getRole)) {

			$deletePlease = false;

			$userModel = new Users();
			$getUsers = $userModel->count('id', 'total')->where('role_id', $id)->get();
			if ((int)$getUsers->total > 0) { // affected users

				if (isset($this->get('request')->params['transfer_role']) !== false) {  // transfer step

					// user update step
					$updateUsers = $userModel->where('role_id', $id)->update(['role_id' => (int)$this->get('request')->params['transfer_role']]);
					if ($updateUsers) {

						// session update step
						$updateSessions = (new Sessions)->where('role_id', $id)->update([
							'role_id' => (int)$this->get('request')->params['transfer_role'],
							'update_session' => 'true'
						]);
						if ($updateSessions) {
							$deletePlease = true;
						}

					}

					if (! $deletePlease) {
						$alerts[] = [
							'status' => 'warning',
							'message' => Base::lang('base.user_role_transfer_problem')
						];
					}
					

				} else { // role to be transferred step

					$alerts[] = [
						'status' => 'warning',
						'message' => Base::lang('base.user_role_delete_required_transfer')
					];
					$arguments['modal_open'] = '#deleteModal';
					$arguments['attribute'] = [
						'#roleDelete' => [
							'action' => $this->get()->url('management/roles/' . $id . '/delete')
						]
					];

					$options = '';
					$userRoles = $model->select('name, id')->notWhere('id', $id)->orderBy('name', 'asc')->getAll();
					if (is_array($userRoles) AND count($userRoles)) {
						foreach ($userRoles as $role) {
							$options .= '<option value="' . $role->id . '">' . $role->name . '</option>';
						}
					}

					$info = '
						<p class="m-0 p-0 text-danger"><small>' . Base::lang('base.role_to_delete') . ': <strong>' . $getRole->name . '</strong></small></p>
						<p class="m-0 p-0 text-danger"><small>' . Base::lang('base.affected_user_count') . ': <strong>' . $getUsers->total . '</strong></small></p>';

					$arguments['manipulation'] = [
						'#roleDelete' => [
							'attribute' => ['action' => $this->get()->url('management/roles/' . $id . '/delete')],
						],
						'#availableRoles' => [
							'html'	=> $options
						],
						'#roleDelete .form-info' => [
							'html' => $info
						]
					];
				}

			} else {
				$deletePlease = true;
			}

			if ($deletePlease) {

				$update = $model->where('id', $id)->delete();

				if ($update) {

					$alerts[] = [
						'status' => 'success',
						'message' => Base::lang('base.user_role_successfully_deleted')
					];
					$arguments['table_reset'] = 'rolesTable';
					$arguments['modal_close'] = '#deleteModal';

				} else {

					$alerts[] = [
						'status' => 'error',
						'message' => Base::lang('base.user_role_delete_problem')
					];
				}

			}

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.record_not_found')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function roleUpdate() {

		$id = (int)$this->get('request')->attributes['id'];
		extract(Base::input([
			'name' => 'nulled_text',
			'routes' => 'nulled_text'
		], $this->get('request')->params));
		$routes = is_array($routes) ? implode(',', $routes) : $routes;

		$alerts = [];
		$arguments = [];

		$model = new UserRoles();
		$getRole = $model->select('id, name, routes')->where('id', $id)->get();
		if (! empty($getRole)) {


			if ($routes !=  $getRole->routes OR $name != $getRole->name) {

				$update = false;
				if ($name != $getRole->name) {

					$getSameRole = $model->count('id', 'total')->where('name', $name)->get();
					if ((int)$getSameRole->total === 0) {
						$update = true;
					}

				} else {
					$update = true;
				}


				if ($update) {

					$update = [
						'name' => $name,
						'routes' => $routes
					];

					$update = $model->where('id', $id)->update($update);

					if ($update) {

						$updateSessions = (new Sessions)->where('role_id', $id)->update([
							'update_session' => 'true'
						]);

						$alerts[] = [
							'status' => 'success',
							'message' => Base::lang('base.user_role_successfully_updated')
						];
						$arguments['table_reset'] = 'rolesTable';

					} else {

						$alerts[] = [
							'status' => 'error',
							'message' => Base::lang('base.user_role_update_problem')
						];
					}

				} else {

					$alerts[] = [
						'status' => 'warning',
						'message' => Base::lang('base.same_name_alert')
					];
					$arguments['manipulation'] = [
						'[name="name"]' => [
							'class' => ['is-invalid'],
						]
					];

				}

			} else {

				$alerts[] = [
					'status' => 'warning',
					'message' => Base::lang('base.no_change')
				];
			}

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.record_not_found')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	public function sessions() {

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => [
				'title' => Base::lang('base.sessions') . ' | ' . Base::lang('base.management'),
				'description' => Base::lang('base.sessions_message')
			],
			'view' => ['admin.sessions', 'admin']
		];

	}

	public function sessionList() {

		$container = $this->get();

		$tableOp = (new KalipsoTable())
			->db((new Logs)->pdo)
			->from('(SELECT 
					x.id, 
					x.auth_code, 
					x.header,
					x.ip, 
					x.last_action_date, 
					x.last_action_point,
					IFNULL((SELECT u_name FROM users WHERE id = x.user_id), "-") AS user,
					IFNULL((SELECT name FROM user_roles WHERE id = x.role_id), "-") AS role
				FROM `sessions` x) AS raw')
			->process([
				'id' => [
					'primary' => true,
				],
				'auth_code' => [],
				'user' => [],
				'role' => [],
				'role' => [],
				'header' => [
					'formatter' => function($row) {

						$device = Base::userAgentDetails($row->header);
						$return = '
						<strong class="p-2 strong" title="' . $row->header . '">
							<i title="' . $device['os'] . '" class="' . $device['p_icon'] . '"></i> 
							<i title="' . $device['browser'] . ' ' . $device['version'] . '" class="' . $device['b_icon'] . '"></i>
						</strong>';
						return $return;
					}
				],
				'ip' => [],
				'last_action_date' => [
					'formatter' => function($row) {
						return date('d.m.Y H:i:s', (int)$row->last_action_date);
					}
				],
				'last_action_point' => [],
			])
			->output();

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $tableOp,
			'view' => null
		];

	}

	public function logs() {


		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => [
				'title' => Base::lang('base.logs') . ' | ' . Base::lang('base.management'),
				'description' => Base::lang('base.logs_message')
			],
			'view' => ['admin.logs', 'admin']
		];

	}

	public function logList() {

		$container = $this->get();
		$blockList = file_exists($file = Base::path('app/Storage/security/ip_blacklist.json')) ? file_get_contents($file) : null; 
		if (empty($blockList)) {
			$blockList = [];
		} else {
			$blockList = @json_decode($blockList, true);
			if (! is_array($blockList)) {
				$blockList = [];
			}
		}

		$tableOp = (new KalipsoTable())
			->db((new Logs)->pdo)
			->from('(SELECT 
					x.id, 
					x.endpoint, 
					x.method,
					x.controller, 
					x.middleware, 
					x.http_status, 
					x.auth_code, 
					x.ip, 
					x.header,
					x.exec_time, 
					x.created_by, 
					CONCAT(x.http_status,x.method) AS req,
					IFNULL((SELECT u_name FROM users WHERE id = x.created_by), "-") AS user,
					IFNULL(FROM_UNIXTIME(x.created_at, "%Y.%m.%d %H:%i"), "-") AS created
				FROM `logs` x) AS raw')
			->process([
				'id' => [
					'primary' => true,
				],
				'endpoint' => [],
				'req' => [
					'formatter' => function($row) {

						if ($row->http_status >= 200 AND $row->http_status < 300) {
							$class = 'text-success';
						} elseif ($row->http_status >= 300 AND $row->http_status < 400) {
							$class = 'text-primary';
						} elseif ($row->http_status >= 400 AND $row->http_status < 500) {
							$class = 'text-warning';
						} else {
							$class = 'text-danger';
						}
						return '<strong class="'.$class.'">' . $row->method . ' ' . $row->http_status . '</strong>';
					}
				],
				'middleware' => [],
				'controller' => [],
				'ip' => [],
				'user' => [],
				'exec_time' => [],
				'created' => [],
				'action' => [
					'exclude' => true,
					'formatter' => function($row) use ($blockList, $container) {

						$buttons = '';
						if ($container->authority('management/logs/:ip/block')) {

							if (isset($blockList[$row->ip]) !== false) {
								$class = 'btn-success';
								$text = Base::lang('base.remove_ip_block');
							} else {
								$class = 'btn-danger';
								$text = Base::lang('base.block_ip');
							}

							if ($row->ip == Base::getIp())
								$class .= ' disabled';

							$buttons .= '
							<button type="button" class="btn ' . $class . '" 
								data-kn-action="'.$this->get()->url('/management/logs/' . $row->ip . '/block').'">
								' . $text . '
							</button>';
						}

						return '
						<div class="btn-group btn-group-sm" role="group" aria-label="'.Base::lang('base.action').'">
							'.$buttons.'
						</div>';
					}
				],
			])
			->output();

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $tableOp,
			'view' => null
		];

	}

	public function logIpBlock() {

		$ip = $this->get('request')->attributes['ip'];
		/* maybe one day
		extract(Base::input([
			'reason' => 'nulled_text',
		], $this->get('request')->params));
		*/

		$blockList = file_exists($file = Base::path('app/Storage/security/ip_blacklist.json')) ? json_decode(file_get_contents($file), true) : null;
		if (is_null($blockList)) {

			if (! is_dir($dir = Base::path('app/Storage'))) {
				mkdir($dir);
			}

			if (! is_dir($dir .= '/security')) {
				mkdir($dir);
			}

			touch($file);
			$blockList = [];

		} 

		$alerts = [];
		$arguments = [];

		if (isset($blockList[$ip]) !== false) {

			unset($blockList[$ip]);

		} else {

			$blockList[$ip] = [
				'date' => time(),
				'user' => Base::userData('id')
			];

		}

		if (file_put_contents($file, json_encode($blockList, JSON_PRETTY_PRINT))) {

			$alerts[] = [
				'status' => 'success',
				'message' => Base::lang('base.ip_block_list_updated')
			];
			$arguments['table_reset'] = 'logsTable';

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.ip_block_list_not_updated')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}

	private function getSettingParameters() {

		$userRoles = (new UserRoles)->select('name, id')->orderBy('name', 'asc')->getAll();
		$_userRoles = [];
		foreach ($userRoles as $role) {
			$_userRoles[$role->id] = $role->name;
		}
		$userRoles = $_userRoles;

		$languages = [];
		foreach (Base::config('app.available_languages') as $lang) {
			$languages[$lang] = Base::lang('langs.' . $lang);
		}

		$groups = [
			'basic' => [
				'name' => Base::lang('settings.basic_settings'),
				'items' => [
					'name' => [
						'type' => 'input',
						'value' => Base::config('settings.name'),
						'required' => true,
						'name' => Base::lang('settings.name'),
						'info' => Base::lang('settings.name_info'),
					],
					'description' => [
						'type' => 'input',
						'value' => json_decode(Base::config('settings.description'), true),
						'required' => true,
						'name' => Base::lang('settings.description'),
						'info' => Base::lang('settings.description_info'),
						'multilingual' => true,
					],
					'contact_email' => [
						'type' => 'input',
						'value' => Base::config('settings.contact_email'),
						'required' => true,
						'name' => Base::lang('settings.contact_email'),
						'info' => Base::lang('settings.contact_email_info'),
					],
					'separator' => [
						'type' => 'input',
						'value' => Base::config('settings.separator'),
						'required' => true,
						'name' => Base::lang('settings.separator'),
						'info' => Base::lang('settings.separator_info'),
					],
					'language' => [
						'type' => 'select',
						'value' => Base::config('settings.language'),
						'options' => $languages,
						'required' => true,
						'name' => Base::lang('settings.language'),
						'info' => Base::lang('settings.language_info'),
					],
					'default_user_role' => [
						'type' => 'select',
						'value' => Base::config('settings.default_user_role'),
						'options' => $userRoles,
						'required' => true,
						'numeric' => true,
						'name' => Base::lang('settings.default_user_role'),
						'info' => Base::lang('settings.default_user_role_info'),
					],
					'last_updated_at' => [
						'type' => 'hidden',
						'value' => Base::config('settings.last_updated_at'),
						'name' => Base::lang('base.updated_at'),
					],
				]
			],
			'secure' => [
				'name' => Base::lang('settings.secure_settings'),
				'items' => [
					'ssl' => [
						'type' => 'check',
						'value' => Base::config('settings.ssl'),
						'name' => Base::lang('settings.ssl'),
						'info' => Base::lang('settings.ssl_info'),
					],
					'log' => [
						'type' => 'check',
						'value' => Base::config('settings.log'),
						'name' => Base::lang('settings.log'),
						'info' => Base::lang('settings.log_info'),
					],
					'maintenance_mode' => [
						'type' => 'check',
						'value' => Base::config('settings.maintenance_mode'),
						'name' => Base::lang('settings.maintenance_mode'),
						'info' => Base::lang('settings.maintenance_mode_info'),
					],
					'maintenance_mode_desc' => [
						'type' => 'input',
						'value' => json_decode(Base::config('settings.maintenance_mode_desc'), true),
						'name' => Base::lang('settings.maintenance_mode_desc'),
						'info' => Base::lang('settings.maintenance_mode_desc_info'),
						'multilingual' => true,
					]
				]
			],
			'email' => [
				'name' => Base::lang('settings.email_settings'),
				'items' => [
					'mail_send_type' => [
						'type' => 'select',
						'value' => Base::config('settings.mail_send_type'),
						'options' => [
							'server' => Base::lang('base.server'),
							'smtp' => Base::lang('base.smtp'),
						],
						'required' => true,
						'name' => Base::lang('settings.mail_send_type'),
						'info' => Base::lang('settings.mail_send_type_info'),
					],
					'smtp_address' => [
						'type' => 'input',
						'value' => Base::config('settings.smtp_address'),
						'required' => true,
						'name' => Base::lang('settings.smtp_address'),
						'info' => Base::lang('settings.smtp_address_info'),
					],
					'smtp_port' => [
						'type' => 'input',
						'value' => Base::config('settings.smtp_port'),
						'required' => true,
						'numeric' => true,
						'name' => Base::lang('settings.smtp_port'),
						'info' => Base::lang('settings.smtp_port_info'),
					],
					'smtp_email_address' => [
						'type' => 'input',
						'value' => Base::config('settings.smtp_email_address'),
						'required' => true,
						'name' => Base::lang('settings.smtp_email_address'),
						'info' => Base::lang('settings.smtp_email_address_info'),
					],
					'smtp_email_pass' => [
						'type' => 'input',
						'value' => Base::config('settings.smtp_email_pass'),
						'required' => true,
						'name' => Base::lang('settings.smtp_email_pass'),
						'info' => Base::lang('settings.smtp_email_pass_info'),
					],
					'smtp_secure' => [
						'type' => 'select',
						'value' => Base::config('settings.smtp_secure'),
						'options' => [
							'ssl' => Base::lang('base.ssl'),
							'tls' => Base::lang('base.tls'),
						],
						'required' => true,
						'name' => Base::lang('settings.smtp_secure'),
						'info' => Base::lang('settings.smtp_secure_info'),
					]
				]
			],
			'optimization' => [
				'name' => Base::lang('settings.optimization_settings'),
				'items' => [
					'mail_queue' => [
						'type' => 'check',
						'value' => Base::config('settings.mail_queue'),
						'name' => Base::lang('settings.mail_queue'),
						'info' => Base::lang('settings.mail_queue_info'),
					],
					'view_cache' => [
						'type' => 'check',
						'value' => Base::config('settings.view_cache'),
						'name' => Base::lang('settings.view_cache'),
						'info' => Base::lang('settings.view_cache_info'),
					],
					'db_cache' => [
						'type' => 'check',
						'value' => Base::config('settings.db_cache'),
						'name' => Base::lang('settings.db_cache'),
						'info' => Base::lang('settings.db_cache_info'),
					],
					'route_cache' => [
						'type' => 'check',
						'value' => Base::config('settings.route_cache'),
						'name' => Base::lang('settings.route_cache'),
						'info' => Base::lang('settings.route_cache_info'),
					]
				]
			]
		];

		return [
			'user_roles' => $userRoles,
			'languages' => $languages,
			'groups' => $groups
		];

	}

	public function settings() {

		$parameters = $this->getSettingParameters();

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => [
				'title' => Base::lang('base.settings') . ' | ' . Base::lang('base.management'),
				'description' => Base::lang('base.settings_message'),
				'groups' => $parameters['groups'],
				'languages' => $parameters['languages']
			],
			'view' => ['admin.settings', 'admin']
		];

	}

	public function settingsUpdate() {

		$parameters = $this->getSettingParameters();

		$parameterList = [];
		foreach ($parameters['groups'] as $group) {
			foreach ($group['items'] as $name => $values) {
				if ($values['type'] == 'hidden') {
					continue;
				}
				$parameterList[$name] = $values['type'] == 'check' ? 
					'check_as_boolean' : (
						isset($values['numeric']) !== false ? 'int' : 'text'
					);
			}
		}
		
		extract(Base::input($parameterList, $this->get('request')->params));

		$settings = '<?php ' . PHP_EOL . PHP_EOL . 'return [' . PHP_EOL;
		foreach ($parameterList as $variable => $type) {
			$settings .= '	\'' . $variable . '\' => ';
			if (is_array($$variable)) {
				$$variable = json_encode($$variable);
			}
			switch ($type) {
				case 'check_as_boolean':
					$settings .= ($$variable ? 'true' : 'false') . ',';
					break;

				case 'text':
					$settings .= '\'' . $$variable . '\',';
					break;
				
				default:
					$settings .= $$variable . ',';
					break;
			}
			$settings .= PHP_EOL;
		}
		$settings .= '	\'last_updated_at\' => ' . time() . ',' . PHP_EOL;
		$settings .= '	\'last_updated_by\' => ' . Base::userData('id') . ',' . PHP_EOL;
		$settings .= '];';

		if (! file_exists($file = Base::path('app/Resources/config/settings.php'))) {
			touch($file);
		} 

		$alerts = [];
		$arguments = [];

		

		if (file_put_contents($file, $settings)) {

			$alerts[] = [
				'status' => 'success',
				'message' => Base::lang('base.settings_updated')
			];
			$arguments['reload'] = true;
			$arguments['reload_timeout'] = 5000;

		} else {

			$alerts[] = [
				'status' => 'warning',
				'message' => Base::lang('base.settings_not_updated')
			];
		}

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => $arguments,
			'alerts' => $alerts,
			'view' => null
		];

	}


}