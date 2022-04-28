<?php

/**
 * Notification Hooks 
 **/
use KN\Helpers\Base;

return [

	// User Registration Notification
	'registration' => function($hook, $external = null) {

		$title = Base::lang('notification.registration_email_title');
        $name = (empty($external['f_name']) ? $external['u_name'] : $external['f_name']);
        $link = '<a href="' . $hook->container->url('/') . '?verify-account=' . $external['token'] . '">
            ' . Base::lang('base.verify_email') . '
        </a>';
        $body = str_replace(
            ['[USER]', '[VERIFY_LINK]'], 
            [$name, $link], 
            Base::lang('notification.registration_email_body')
        );

        $email = $hook->addEmail([
            'title' => $title,
            'body' => $body,
            'recipient' => $external['u_name'],
            'recipient_email' => $external['email'],
            'recipient_id' => $external['id'],
            'token' => $external['token']
        ]);
            
        $notification = $hook->notificationsModel->insert([
            'user_id'       => $external['id'],
            'type'          => 'registration',
            'created_at'    => time()
        ]);

        if ($email AND $notification)
        	return true;
        elseif ($email OR $notification)
        	return false;
        else
        	return null;

	},

    // Account Recovery Email
    'recovery_request' => function($hook, $external = null) {

        $external = (array) $external;
        $title = Base::lang('notification.recovery_request_email_title');
        $name = (empty($external['f_name']) ? $external['u_name'] : $external['f_name']);
        $link = '<a href="' . $hook->container->url('/auth/recovery') . '?token=' . $external['token'] . '">
            ' . Base::lang('base.recovery_account') . '
        </a>';
        $body = str_replace(
            ['[USER]', '[RECOVERY_LINK]'], 
            [$name, $link], 
            Base::lang('notification.recovery_request_email_body')
        );

        $email = $hook->addEmail([
            'title' => $title,
            'body' => $body,
            'recipient' => $external['u_name'],
            'recipient_email' => $external['email'],
            'recipient_id' => $external['id'],
            'token' => $external['token']
        ]);

        if ($email)
            return true;
        else
            return null;
    },

    // Account Recovered
    'account_recovered' => function($hook, $external = null) {

        $external = (array) $external;
        $title = Base::lang('notification.account_recovered_email_title');
        $name = (empty($external['f_name']) ? $external['u_name'] : $external['f_name']);
        $body = str_replace(
            ['[USER]'], 
            [$name], 
            Base::lang('notification.account_recovered_email_body')
        );

        $email = $hook->addEmail([
            'title' => $title,
            'body' => $body,
            'recipient' => $external['u_name'],
            'recipient_email' => $external['email'],
            'recipient_id' => $external['id'],
            'token' => $external['token']
        ]);

        $notification = $hook->notificationsModel->insert([
            'user_id'       => $external['id'],
            'type'          => 'account_recovered',
            'created_at'    => time()
        ]);

        if ($email AND $notification)
            return true;
        elseif ($email OR $notification)
            return false;
        else
            return null;
    },

    // Emal Change -> Again Verify
    'email_change' => function($hook, $external = null) {

        $title = Base::lang('notification.email_change_email_title');
        $name = (empty($external['f_name']) ? $external['u_name'] : $external['f_name']);
        $link = '<a href="' . $hook->container->url('/') . '?verify-account=' . $external['token'] . '">
            ' . Base::lang('base.verify_email') . '
        </a>';
        $body = str_replace(
            ['[USER]', '[VERIFY_LINK]', '[CHANGES]'], 
            [$name, $link, $external['changes']], 
            Base::lang('notification.email_change_email_body')
        );

        $email = $hook->addEmail([
            'title' => $title,
            'body' => $body,
            'recipient' => $external['u_name'],
            'recipient_email' => $external['email'],
            'recipient_id' => $external['id'],
            'token' => $external['token']
        ]);

        if ($email)
            return true;
        else
            return null;

    },

];