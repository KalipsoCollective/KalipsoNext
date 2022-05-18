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

		$users = (new Users)->count('id', 'total')->notWhere('status', 'deleted')->get();
		$userRoles = (new UserRoles)->count('id', 'total')->get();
		$sessions = (new Sessions)->count('id', 'total')->get();
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
					CONCAT(x.f_name, " ", x.l_name) AS name,
					x.email, 
					IFNULL(FROM_UNIXTIME(x.b_date, "%Y.%m.%d"), "-") AS birth_date,
					IFNULL((SELECT name FROM user_roles WHERE status = "active" AND id = x.role_id), "-") AS role,
					FROM_UNIXTIME(x.created_at, "%Y.%m.%d %H:%i") AS created,
					IFNULL(FROM_UNIXTIME(x.updated_at, "%Y.%m.%d"), "-") AS updated,
					x.status
				FROM `users` x) AS raw')
			->process([
				'id' => [
					'primary' => true,
				],
				'u_name' => [],
				'name' => [],
				'email' => [],
				'birth_date' => [],
				'role' => [],
				'created' => [],
				'updated' => [],
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


		//$arguments = (new KalipsoTable()->);

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

		$users = (new Users)->count('id', 'total')->notWhere('status', 'deleted')->get();
		$userRoles = (new UserRoles)->count('id', 'total')->get();
		$sessions = (new Sessions)->count('id', 'total')->get();
		$logs = (new Logs)->count('id', 'total')->get();

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


	public function settings() {

		$count = '';

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


	public function logs() {

		$count = '';
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

}