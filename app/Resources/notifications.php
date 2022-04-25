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


];