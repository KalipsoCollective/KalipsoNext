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
        $link = '<a href="' . $hook->container->url('/') . ' ?verify-account=' . $data['token'] . '">
            ' . Base::lang('base.verify_email') . '
        </a>';
        $body = str_replace(
            ['[USER]', '[VERIFY_LINK]'], 
            [$name, $link], 
            Base::lang('notification.registration_email_body')
        );

        $hook->addEmail([
            'title' => $title,
            'body' => $body,
            'recipient' => $data['u_name'],
            'recipient_email' => $data['email'],
            'recipient_id' => $data['id'],
            'token' => $data['token']
        ]);
            
        (new DB())->table('notifications')
            ->insert([
                'user_id'       => $data['id'],
                'type'          => $type,
                'created_at'    => time()
            ]);

	},


];