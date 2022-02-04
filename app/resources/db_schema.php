<?php

/**
 * Database Structure
 *
 * The schema here is used to add new tables or to add new columns to existing tables.
 * Column parameters is as follows.
 *
 * > type:          Type parameters(required) -> (INT | VARCHAR | TEXT | DATE | ENUM | JSON)
 * > nullable:      True if it is an empty field.
 * > auto_inc:      True if it is an auto increment field.
 * > attr:          Attribute parameters -> (BINARY | UNSIGNED | UNSIGNED ZEROFILL | ON UPDATE CURRENT_TIMESTAMP)
 * > type_values:   ENUM -> ['on', 'off'] | INT, VARCHAR -> 255
 * > default:       Default value -> NULL, 'string' or CURRENT_TIMESTAMP
 * > index:         Index type -> (INDEX | PRIMARY | UNIQUE | FULLTEXT)
 */

return [
	'tables' => [

		/* Users Table */
		'users' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'UNSIGNED',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'u_name' => [
					'type'          => 'varchar',
					'type_values'   => 255,
					'index'         => 'UNIQUE'
				],
				'f_name' => [
					'type'          => 'varchar',
					'type_values'   => 255,
					'default'       => 'NULL',
					'nullable'      => 'true',
					'index'         => 'UNIQUE'
				],
				'l_name' => [
					'type'          => 'varchar',
					'type_values'   => 255,
					'default'       => 'NULL',
					'nullable'      => 'true',
					'index'         => 'UNIQUE'
				],
				'email' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'UNIQUE'
				],
				'password' => [
					'type'          => 'varchar',
					'type_values'   => 120,
				],
				'token' => [
					'type'          => 'varchar',
					'type_values'   => 80,
				],
				'role_id' => [
					'type'          => 'int',
					'type_values'   => 2,
					'default'       => 'NULL',
					'nullable'      => 'true',
					'index'         => 'INDEX'
				],
				'b_date' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'default'       => 0,
					'index'         => 'INDEX'
				],
				'updated_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL',
				],
				'updated_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'nullable'      => true,
					'default'       => 'NULL',
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['active', 'passive', 'deleted'],
					'default'       => 'active',
					'index'         => 'INDEX'
				]
			],
		],

		/* User Roles Table */
		'user_roles' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'name' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'UNIQUE',
				],
				'view_points' => [
					'type'          => 'text',
					'nullable'      => true
				],
				'action_points' => [
					'type'          => 'text',
					'nullable'      => true
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'updated_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'updated_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['active', 'deleted'],
					'default'       => 'active',
					'index'         => 'INDEX'
				],
			],
		],

		/* Sessions Table */
		'sessions' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'auth_code' => [
					'type'          => 'varchar',
					'type_values'   => 50,
					'index'         => 'UNIQUE',
				],
				'user_id' => [
					'type'          => 'int',
					'index'         => 'INDEX',
				],
				'header' => [
					'type'          => 'varchar',
					'type_values'   => 250,
				],
				'ip' => [
					'type'          => 'varchar',
					'type_values'   => 250,
				],
				'role_id' => [
					'type'          => 'varchar',
					'type_values'   => '80',
					'index'         => 'INDEX',
				],
				'update_session' => [
					'type'          => 'enum',
					'type_values'   => ['true', 'false'],
					'default'       => 'false',
					'index'         => 'INDEX'
				],
				'last_action_date' => [
					'type'          => 'varchar',
					'type_values'   => 80,
				],
				'last_action_point' => [
					'type'          => 'varchar',
					'nullable'      => true,
					'type_values'   => 250,
				]
			]
		],

		/* Pages Table */
		/*
		'pages' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'title' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'INDEX',
				],
				'slug' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'INDEX',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'meta' => [
					'type'          => 'varchar',
					'type_values'   => 180,
					'index'         => 'INDEX',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'content' => [
					'type'          => 'text',
					'index'         => 'FULLTEXT',
				],
				'media_id' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'updated_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'updated_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['active', 'draft', 'deleted'],
					'default'       => 'draft',
					'index'         => 'INDEX'
				],
			]
		],
		*/

		/* Contents Table */
		/*
		'contents' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'title' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'UNIQUE',
				],
				'slug' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'UNIQUE',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'content' => [
					'type'          => 'text',
					'index'         => 'FULLTEXT',
				],
				'media_id' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'category_id' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'updated_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'updated_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['active', 'passive', 'deleted'],
					'default'       => 'passive',
					'index'         => 'INDEX'
				],
			]
		],
		*/

		/* Categories Table */
		/*
		'categories' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'title' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'UNIQUE',
				],
				'slug' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'UNIQUE',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'color_code' => [
					'type'          => 'varchar',
					'type_values'   => 30,
					'index'         => 'INDEX',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'description' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'INDEX',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'updated_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'updated_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['active', 'passive', 'deleted'],
					'default'       => 'passive',
					'index'         => 'INDEX'
				],
			]
		],
		*/

		/* Medias Table */
		/*
		'medias' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'name' => [
					'type'          => 'varchar',
					'type_values'   => 255,
					'index'         => 'INDEX'
				],
				'files' => [
					'type'          => 'json',
					'default'       => 'NULL',
					'nullable'      => true
				],
				'type' => [
					'type'          => 'enum',
					'type_values'   => ['artist', 'album', 'song', 'label', 'content', 'page', 'other'],
					'default'       => 'other',
					'index'         => 'INDEX'
				],
				'size' => [
					'type'          => 'varchar',
					'type_values'   => 150,
					'index'         => 'INDEX'
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'updated_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'updated_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['active', 'deleted'],
					'default'       => 'active',
					'index'         => 'INDEX'
				],
			]
		],
		*/

		/* Environments Table */
		'environments' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'env_key' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'INDEX',
				],
				'env_value' => [
					'type'          => 'text',
					'nullable'      => true,
					'index'         => 'FULLTEXT',
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
			]
		],

		/* Email Logs Table */
		'email_logs' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'date' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX',
				],
				'email' => [
					'type'          => 'varchar',
					'type_values'   => 180,
					'index'         => 'INDEX',
				],
				'name' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'INDEX',
				],
				'title' => [
					'type'          => 'varchar',
					'type_values'   => 120,
					'index'         => 'INDEX',
				],
				'user_id' => [
					'type'          => 'int',
					'index'         => 'INDEX',
				],
				'sender_id' => [
					'type'          => 'int',
					'index'         => 'INDEX',
				],
				'file' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX',
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['pending', 'uncompleted', 'completed'],
					'default'       => 'pending',
					'index'         => 'INDEX'
				],
			]
		],

		/* Logs Table */
		'logs' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'date' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'action' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'index'         => 'INDEX'
				],
				'route' => [
					'type'          => 'varchar',
					'type_values'   => 150,
					'index'         => 'INDEX'
				],
				'endpoint' => [
					'type'          => 'text',
					'index'         => 'FULLTEXT'
				],
				'http_status' => [
					'type'          => 'int',
					'index'         => 'INDEX'
				],
				'auth_code' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL',
					'index'         => 'INDEX'
				],
				'user_id' => [
					'type'          => 'int',
					'nullable'      => true,
					'default'       => 0,
					'index'         => 'INDEX'
				],
				'ip' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL',
					'index'         => 'INDEX'
				],
				'header' => [
					'type'          => 'varchar',
					'type_values'   => 180,
					'nullable'      => true,
					'default'       => 'NULL',
				],
				'external_data' => [
					'type'          => 'text',
					'nullable'      => true,
					'default'       => 'NULL'
				]
			]
		],

		/* Views Table */
		'views' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'device_key' => [
					'type'          => 'varchar',
					'type_values'   => 32,
					'index'         => 'INDEX',
				],
				'type' => [
					'type'          => 'enum',
					'type_values'   => ['content', 'page'],
					'index'         => 'INDEX',
				],
				'content_id' => [
					'type'          => 'int',
					'type_values'   => 11,
					'index'         => 'INDEX',
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				]
			]
		],

		/* Contact Table */
		'contact' => [
			'cols' => [
				'id' => [
					'type'          => 'int',
					'auto_inc'      => true,
					'attr'          => 'unsigned',
					'type_values'   => 11,
					'index'         => 'PRIMARY'
				],
				'type' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX',
				],
				'subject' => [
					'type'          => 'varchar',
					'type_values'   => 150,
					'index'         => 'INDEX',
				],
				'message' => [
					'type'          => 'text',
					'index'         => 'FULLTEXT',
				],
				'ip' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL',
					'index'         => 'INDEX'
				],
				'header' => [
					'type'          => 'varchar',
					'type_values'   => 180,
					'nullable'      => true,
					'default'       => 'NULL',
				],
				'created_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'index'         => 'INDEX'
				],
				'created_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'index'         => 'INDEX'
				],
				'updated_at' => [
					'type'          => 'varchar',
					'type_values'   => 80,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'updated_by' => [
					'type'          => 'int',
					'type_values'   => 10,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'updated_reason' => [
					'type'          => 'varchar',
					'type_values'   => 250,
					'nullable'      => true,
					'default'       => 'NULL'
				],
				'status' => [
					'type'          => 'enum',
					'type_values'   => ['opened', 'pending', 'closed'],
					'default'       => 'opened',
					'index'         => 'INDEX'
				],
			]
		],
	],
	'table_values' => [
		'charset'   => 'utf8mb4', // You can use 'utf8' if the structure is causing problems.
		'collate'   => 'utf8mb4_unicode_520_ci', // You can use 'utf8_general_ci' if the structure is causing problems.
		'engine'    => 'InnoDB',
		'specific'  => [ // You can give specific value.
			'sessions' => [
				'engine'    => 'MEMORY'
			],
		]
	],
	'data'  => [
		'users' => [
			[
				'u_name'                => 'root',
				'email'                 => 'hello@koalapix.com',
				'password'              => '$2y$10$1i5w0tYbExemlpAAsospSOZ.n06NELYooYa5UJhdytvBEn85U8lly', // 1234
				'token'                 => 'Hl7kojH2fLdsbMUO8T0lZdTcMwCjvOGIbBk8cndJSsh2IcpN',
				'role_id'               => '1',
				'created_at'            => 1611231432,
				'created_by'            => 0,
				'status'                => 'active'
			],
		],
		'user_roles' => [
			[
				'name'                  => 'admin',
				'view_points'           => 'users,users/x,management,management/users,management/user_roles,management/topics,management/entries,management/tag_management,management/announcements,management/contacts,management/settings,account,notifications,messages,dashboard',
				'action_points'         => 'User/block,User/follow,User/addRole,User/editRole,User/deleteRole,User/verifyLink,Management/settings,Management/manageContact,Message/send,Message/move,Message/delete,Topic/addEntry,Topic/editEntry,Topic/deleteEntry,Topic/editTopic,Topic/addEntryToList,Topic/addToFavorite,Topic/addTag',
				'created_at'            => 1611231432,
				'created_by'            => 1,
				'status'                => 'active'
			]
		],
	],
];