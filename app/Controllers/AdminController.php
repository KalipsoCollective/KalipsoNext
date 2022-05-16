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

		$users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$sessions = (new Sessions)->select('COUNT(id) as total')->get();
		$logs = (new Logs)->select('COUNT(id) as total')->get();

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

		return [
			'status' => true,
			'statusCode' => 200,
			'arguments' => [
				'title' => Base::lang('base.users') . ' | ' . Base::lang('base.management'),
				'description' => Base::lang('base.users_message'),
			],
			'view' => ['admin.users', 'admin']
		];

	}

	public function userList() {

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
					'formatter' => function($row) {
						return '
						<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
							<button type="button" class="btn btn-light" data-kn-action="'.$this->get()->url('/management/users/' . $row->id . '/update').'">
								' . Base::lang('base.edit') . '
							</button>
							<button type="button" class="btn btn-danger" data-kn-action="'.$this->get()->url('/management/users/' . $row->id . '/delete').'">
								' . Base::lang('base.delete') . '
							</button>
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

	public function roleList() {

		$tableOp = (new KalipsoTable())
			->db((new Users)->pdo)
			->from('(SELECT 
					x.id, 
					x.name, 
					x.routes, 
					(SELECT COUNT(id) FROM users WHERE status != "deleted" AND role_id = x.id) AS users,
					FROM_UNIXTIME(x.created_at, "%Y.%m.%d %H:%i") AS created,
					IFNULL(FROM_UNIXTIME(x.updated_at, "%Y.%m.%d"), "-") AS updated,
					x.status
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
						if (strpos($row->routes, ',') !== false) {
							$row->routes = explode(',', $row->routes);
							$total = count($row->routes);
							$title = implode(' '.PHP_EOL, $row->routes);
						}

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
				'status' => [
					'formatter' => function($row) {

						switch ($row->status) {
							case 'active':
								$class = 'text-success';
								break;

							case 'passive':
								$class = 'text-primary';
								break;
							
							default:
								$class = 'text-danger';
								break;
						}
						return '<span class="' . $class . '">' . Base::lang('base.' . $row->status) . '</span>';

					}
				],
				'action' => [
					'exclude' => true,
					'formatter' => function($row) {
						return '
						<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
							<button type="button" class="btn btn-light" data-kn-action="'.$this->get()->url('/management/roles/' . $row->id . '/update').'">
								' . Base::lang('base.edit') . '
							</button>
							<button type="button" class="btn btn-danger" data-kn-again="'.Base::lang('base.are_you_sure').'" data-kn-action="'.$this->get()->url('/management/roles/' . $row->id . '/delete').'">
								' . Base::lang('base.delete') . '
							</button>
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
			$arguments['form_validation'] = [
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

		$id = $this->get('request')->attributes['id'];

		$alerts = [];
		$arguments = [];

		$model = new UserRoles();
		
		$getRole = $model->count('id', 'total')->where('id', $id)->get();
		if ((int)$getRole->total === 1) {

			$update = $model->where('id', $id)->delete();

			if ($update) {

				$alerts[] = [
					'status' => 'success',
					'message' => Base::lang('base.user_role_successfully_deleted')
				];
				$arguments['table_reset'] = 'rolesTable';

			} else {

				$alerts[] = [
					'status' => 'error',
					'message' => Base::lang('base.user_role_delete_problem')
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

	public function roleUpdate() {

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
			$arguments['form_validation'] = [
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

	public function sessions() {

		$users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$sessions = (new Sessions)->select('COUNT(id) as total')->get();
		$logs = (new Logs)->select('COUNT(id) as total')->get();

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

		$users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$sessions = (new Sessions)->select('COUNT(id) as total')->get();
		$logs = (new Logs)->select('COUNT(id) as total')->get();

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


	public function logs() {

		$users = (new Users)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$userRoles = (new UserRoles)->select('COUNT(id) as total')->notWhere('status', 'deleted')->get();
		$sessions = (new Sessions)->select('COUNT(id) as total')->get();
		$logs = (new Logs)->select('COUNT(id) as total')->get();

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