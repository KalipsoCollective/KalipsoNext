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
							<button type="button" class="btn btn-light" data-kn-action="'.$this->get()->url('/management/users/' . $row->id . '/edit').'">
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


	public function userRoles() {

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