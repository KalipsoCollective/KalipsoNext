<?php

/**
 * 	KalipsoNext - Localization File
 * 	English(en)
 **/

return [
    'lang' => [
        'code' => 'en',
    ],
    'err' => 'Error',
    'error' => [
        'page_not_found' => 'Page not found!',
        'method_not_allowed' => 'Method not allowed!',
        'controller_not_defined' => 'Controller is not defined!',
        'unauthorized' => 'You are not authorized.',
        'view_definition_not_found' => 'Controller did not send view parameter!',
        'csrf_token_mismatch' => 'CSRF key mismatch.',
        'csrf_token_incorrect' => 'CSRF key invalid.',
        'username_is_already_used' => 'Username is already used.',
        'notification_hook_file_not_found' => 'Notification hook file not found!',
        'a_problem_occurred' => 'A problem occurred!',
        'endpoint_file_is_not_found' => 'Authority endpoint file not found!',
        'ip_blocked' => 'Your IP address has been blocked!',
    ],
    'notification' => [
        'registration_email_title' => 'Your Account Has Been Created!',
        'registration_email_body' => 'Hi [USER], <br>Your account has been created. You can verify your email address with the link below. <br>[VERIFY_LINK]',
        'recovery_request_email_title' => 'Account Recovery',
        'recovery_request_email_body' => 'Hi [USER], <br>We received your account recovery request. You can set your new password with the link below. <br>[RECOVERY_LINK]',
        'account_recovered_email_title' => 'Your Account Has Been Recovered!',
        'account_recovered_email_body' => 'Hi [USER], <br>Your account has been recovered. If you did not do this, please contact us.',
        'email_change_email_title' => 'Your Email Address Has Been Updated!',
        'email_change_email_body' => 'Hi [USER], <br>Your email address has been updated. You can verify with the link below. <br>[VERIFY_LINK] <br>[CHANGES]',
    ],
    'auth' => [
        'auth' => 'Profile',
        'auth_action' => 'Profile - Sub Pages',
        'auth_logout' => 'Logout',
        'management' => 'Management',
        'management_users' => 'Management - Users',
        'management_users_list' => 'Management - Users - List',
        'management_users_add' => 'Management - Users - Add',
        'management_users_detail' => 'Management - Users - Detail',
        'management_users_update' => 'Management - Users - Edit',
        'management_users_delete' => 'Management - Users - Delete',
        'management_roles' => 'Management - Roller',
        'management_roles_list' => 'Management - Roller - List',
        'management_roles_add' => 'Management - Roller - Add',
        'management_roles_detail' => 'Management - Roller - Detail',
        'management_roles_update' => 'Management - Roller - Edit',
        'management_roles_delete' => 'Management - Roller - Delete',
        'management_sessions' => 'Management - Sessions',
        'management_sessions_list' => 'Management - Sessions - List',
        'management_settings' => 'Management - Settings',
        'management_logs' => 'Management - Logs',
        'management_logs_list' => 'Management - Logs - List',
        'management_logs_ip_block' => 'Management - Logs - IP Block',
    ],
    'base' => [
        'sandbox' => 'Sandbox',
        'sandbox_message' => 'You can access all the tools that will help you in the development process from this screen.',
        'clear_storage' => 'Clear Storage',
        'clear_storage_message' => 'Allows you to delete files inside the storage folder.',
        'session' => 'Session',
        'session_message' => 'Shows the data within the session.',
        'php_info' => 'PHP Info',
        'php_info_message' => 'Shows server PHP information.',
        'db_init' => 'Prepare DB',
        'db_init_message' => 'Prepares database tables according to the schema.',
        'db_init_success' => 'Database has been prepared successfully.',
        'db_init_problem' => 'There was a problem while preparing the database. -> [ERROR]',
        'db_seed' => 'Seed DB',
        'db_seed_message' => 'Inserts data into tables within the schema.',
        'column' => 'Column',
        'table' => 'Table',
        'data' => 'Data',
        'type' => 'Type',
        'auto_inc' => 'Auto Increment',
        'attribute' => 'Attribute',
        'default' => 'Default',
        'index' => 'Index',
        'yes' => 'yes',
        'no' => 'no',
        'charset' => 'Charset',
        'collate' => 'Collate',
        'engine' => 'Engine',
        'db_name' => 'Database Name',
        'db_charset' => 'Database Charset',
        'db_collate' => 'Database Collate',
        'db_engine' => 'Database Engine',
        'db_init_alert' => 'If there is no database named [DB_NAME], add it with the [COLLATION] collation.',
        'db_init_start' => 'Good, Prepare!',
        'db_seed_success' => 'Database has been seeded successfully.',
        'db_seed_problem' => 'There was a problem while seeding the database. -> [ERROR]',
        'db_seed_start' => 'Good, Seed!',
        'clear_storage_success' => 'Storage folder is cleared.',
        'folder' => 'Folder',
        'delete' => 'Delete',
        'folder_not_found' => 'Folder not found!',
        'change_language' => 'Change Language',
        'seeding' => 'Seeding...',
        'go_to_home' => 'Go to Home',
        'home' => 'Home',
        'welcome' => 'Welcome!',
        'welcome_message' => 'It is the start page of KalipsoNext.', 
        'login' => 'Login',
        'login_message' => 'It is the sample login page.',
        'register' => 'Register',
        'register_message' => 'It is the sample register page.',
        'logout' => 'Logout',
        'account' => 'Account',
        'account_message' => 'It is the sample login page.',
        'email_or_username' => 'Email or Username',
        'password' => 'Password',
        'recovery_account' => 'Recovery Account',
        'recovery_account_message' => 'From this page, you can get a password reset link by entering your e-mail address.',
        'email' => 'Email Address',
        'username' => 'Username', 
        'name' => 'Name',
        'surname' => 'Surname',
        'form_cannot_empty' => 'The form cannot be empty!',
        'email_is_already_used' => 'Email address is already in use.',
        'username_is_already_used' => 'Username is already in use.',
        'registration_problem' => 'There was a problem during registration.',
        'registration_successful' => 'Registration successful!',
        'verify_email' => 'Verify Email Address',
        'verify_email_not_found' => 'Email verification link is invalid!',
        'verify_email_problem' => 'There was a problem verifying the email!',
        'verify_email_success' => 'Email verification successful.',
        'your_account_has_been_blocked' => 'Your account has been deleted, please contact us.',
        'account_not_found' => 'Account not found!',
        'your_login_info_incorrect' => 'Your login information is incorrect!',
        'welcome_back' => 'Welcome back!',
        'login_problem' => 'There was a problem starting the session.',
        'profile' => 'Profile',
        'profile_message' => 'You can edit your profile from this page.',
        'sessions' => 'Sessions',
        'sessions_message' => 'You can view active sessions from this page.',
        'device' => 'Device',
        'ip' => 'IP',
        'last_action_point' => 'Last Action Point',
        'last_action_date' => 'Last Action Date',
        'action' => 'Action',
        'terminate' => 'Terminate',
        'session_terminated' => 'Session terminated.',
        'session_not_terminated' => 'The session could not be terminated!',
        'signed_out' => 'Signed out.',
        'login_information_updated' => 'Your login information has been updated.',
        'birth_date' => 'Birth Date',
        'update' => 'Update',
        'save_problem' => 'There was a problem saving.',
        'save_success' => 'Successfully saved.',
        'recovery_request_successful' => 'We\'ve sent you the account recovery link, don\'t forget to check your email.',
        'recovery_request_problem' => 'There was a problem sending the account recovery link.',
        'new_password' => 'New Password',
        'change_password' => 'Change Password',
        'account_recovered' => 'The account has been recovered, you can log in with your new password.',
        'account_not_recovered' => 'There was a problem recovering the account.',
        'account_not_verified' => 'Account verification not done.',
        'management' => 'Management',
        'toggle_navigation' => 'Toggle Navigation',
        'dashboard' => 'Dashboard',
        'dashboard_message' => 'The dashboard is the shortest way to see a summary of what\'s going on.',
        'users' => 'Users',
        'users_message' => 'This is the page where you can manage users.',
        'user_roles' => 'User Roles',
        'user_roles_message' => 'This is the page where you can manage user roles.',
        'logs' => 'Logs',
        'logs_message' => 'This is the page where you can review all log records.',
        'settings' => 'Settings',
        'settings_message' => 'You can update all settings from this screen.',
        'view' => 'View',
        'status' => 'Status',
        'all' => 'All',
        'active' => 'Active',
        'passive' => 'Passive',
        'deleted' => 'Deleted',
        'role' => 'Role',
        'created_at' => 'Created',
        'updated_at' => 'Updated',
        'edit' => 'Edit',
        'routes' => 'Routes',
        'add_new' => 'Add New',
        'close' => 'Close',
        'add' => 'Add',
        'user_role_successfully_added' => 'The user role has been successfully added.',
        'user_role_add_problem' => 'There was a problem adding the user role.',
        'user_role_successfully_deleted' => 'User role deleted successfully.',
        'user_role_delete_problem' => 'There was a problem deleting the user role.',
        'user_role_successfully_updated' => 'The user role has been successfully updated.',
        'user_role_update_problem' => 'There was a problem updating the user role.',
        'same_name_alert' => 'There is already another record with the same name.',
        'loading' => 'Loading...',
        'are_you_sure' => 'Are you sure?',
        'record_not_found' => 'Record not found!',
        'delete_role' => 'Delete Role',
        'role_to_transfer_users' => 'Role to Transfer Users',
        'user_role_delete_required_transfer' => 'To be able to delete this role, you must transfer the relevant members!',
        'role_to_delete' => 'Role to Delete',
        'affected_user_count' => 'Number of Users to be Affected',
        'user_role_transfer_problem' => 'Problem occurred while transferring users to new role!',
        'no_change' => 'No change!',
        'copyright' => 'Copyright',
        'all_rights_reserved' => 'All rights reserved.',
        'language' => 'Language',
        'user_successfully_added' => 'The user has been successfully added.',
        'user_add_problem' => 'There was a problem adding the user.',
        'user_successfully_deleted' => 'User deleted successfully.',
        'user_delete_problem' => 'There was a problem deleting the user.',
        'user_successfully_updated' => 'The user has been successfully updated.',
        'user_update_problem' => 'There was a problem updating the user.',
        'user_delete_problem_for_own_account' => 'You cannot delete your own account!',
        'middleware' => 'Middleware',
        'controller' => 'Controller',
        'request' => 'Request',
        'endpoint' => 'Endpoint',
        'user' => 'User',
        'execute_time' => 'Execute Time',
        'block_ip' => 'Block IP',
        'remove_ip_block' => 'Remove IP Block',
        'ip_block_list_not_updated' => 'Failed to update IP block list!',
        'ip_block_list_updated' => 'IP block list updated.',
        'auth_code' => 'Auth Code',
    ],
    'app' => [
        
    ]
];