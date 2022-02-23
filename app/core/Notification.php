<?php

/**
 * @package KN
 * @subpackage KN Notification
 */

declare(strict_types=1);

namespace App\Core;

use App\Core\DB;
use App\Helpers\KN;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Notification {

    public $types;

    public function __construct () {


        // type -> process(email or notification) -> process requirements

        $baseUrl = KN::base();

        $this->types = [
            'registration' => [
                'email' => [
                    'title' => 'noti_email_register_title', 
                    'body' => 'noti_email_register_body', 
                    'args' => [
                        '[USER]' => '{f_name|u_name}'
                        '[VERIFY_LINK]' => '<a href="' . $baseUrl . '/?verify={token}">' . KN::lang('verify_email') . '</a>'
                    ],
                ],
                'sys'   => ['title' => 'noti_sys_register_title', 'body' => 'noti_sys_register_body'],
            ]
        ];
    }

    public function add($type, $args) {

        $args = (array) $args;

        $return = false;
        if (isset($this->types[$type]) !== false) {


            foreach ($this->types[$type] as $method => $parameters) {
            
                switch ($method) {
                    case 'email': // email notifications
                        
                        $title = KN::lang($parameters['title']);
                        $body = KN::lang($parameters['body']);

                        if (isset($parameters['args']) !== false) { // dynamic text

                            $title = $this->dynamicReplacer($title, $args);
                            $body = $this->dynamicReplacer($body, $args);
                        }

                        $this->emailLogger([
                            'title' => $title,
                            'body' => $body,
                            'recipient' => $args['u_name'],
                            'recipient_email' => $args['email'],
                            'recipient_id' => $args['id']
                        ]);

                        break;
                    
                    case 'sys': // system notifications
                        


                        break;
                }
            }

            

            $add = new DB();
            $add->table('logs')
                ->insert([
                    'date'          => time(),
                    'endpoint'      => $args['request']['request'],
                    'http_status'   => $args['http_status'],
                    'auth_code'     => isset($_COOKIE[KN_SESSION_NAME]) !== false ? $_COOKIE[KN_SESSION_NAME] : null,
                    'user_id'       => isset($_SESSION['user']->id) !== false ? $_SESSION['user']->id : null,
                    'ip'            => KN::getIp(),
                    'header'        => KN::getHeader(),
                    'request'       => json_encode($args['request']),
                    'response'      => is_array($args['response']) ? json_encode($args['response']) : $args['response'],
                ]);


        }
        return $return;
        
    }


    public function dynamicReplacer($string, $arguments) {

        $arguments = (array) $arguments;

        preg_match_all('/{((?:[^{}]*|(?R))*)}/m', $string, $matches, PREG_SET_ORDER, 0);

        if (is_array($matches) AND count($matches)) {

            foreach ($matches as $match) {

                $operator = '';
                if (strpos($match[1], '|')) {

                    $operator = '|';
                    $blocks = explode('|', $match[1]);

                } elseif (strpos($match[1], '&')) {

                    $operator = '&';
                    $blocks = explode('&', $match[1]);

                } else {

                    $blocks = [$match[1]];

                }

                $extract = [];
                foreach ($blocks as $block) {
                    
                    if (isset($arguments[$block]) !== false AND ! empty($arguments[$block])) {

                        $extract[] = $arguments[$block];

                    }

                }
                
                if (count($extract)) {

                    if ($operator == '&') {

                        $replace = implode(' ', $extract);

                    } else {

                        $replace = $extract[0];
                    }


                    $string = str_replace($match[0], $replace, $string);

                }

            }

        }

        return $string;

    }


    public function emailLogger($arguments) {

        $subTitle = $arguments['title'];
        $appName = KN::config('settings.name');
        $title = $subTitle . ' - ' . $appName;
        $content = $arguments['body'];

        if (file_exists($template = KN::path('app/resources/template/email.html'))) { // with template

            $unsubscribe = str_replace(['[LINK]'], KN::base('account').'?unsubscribe=' , KN::lang('noti_unsubscribe_footer'))

            $footer = $appName . ' (c) ' . date('Y')
            $footer .= isset($arguments['unsubscribe']) !== false ? ' | ' . $unsubscribe : '';

            $content = str_replace([
                '{{TITLE}}',
                '{{ALT_CONTENT}}',
                '{{APP}}',
                '{{SUB_TITLE}}',
                '{{CONTENT}}',
                '{{FOOTER}}'
            ], [
                $title,
                strip_tags($content);
                $appName,
                $subTitle,
                $body,
                $footer
            ], file_get_contents($template));

        }

        $status = 'pending';
        if (! KN::config('settings.mail_queue')) { // Direct sending



        } else {

        }

    }

    public function sendEmail($recipientMail, $recipientName = '', string $content = '', string $title = '') {

        $return = false;

        if (config('app.dev_mode')) $recipientMail = config('settings.contact_email');
        
        $sendingType = config('settings.mail_send_type');

        if (config('settings.dev_mode')) {
            $sendingType = 'server';
        }

        switch ($sendingType) {

            case 'smtp':
                $mail = new PHPMailer(true);
                $mail->setLanguage(lang('lang_code'));

                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host         = config('settings.smtp_address');
                    $mail->SMTPAuth     = true;
                    $mail->Username     = config('settings.smtp_email_address');
                    $mail->Password     = config('settings.smtp_email_pass');
                    $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port         = config('settings.smtp_port');
                    $mail->CharSet      = config('app.charset');

                    $reply = config('settings.contact_email');
                    if (! $reply OR $reply == '') {
                        $reply = config('settings.smtp_email_address');
                    }
                    //Recipients
                    $mail->setFrom(config('settings.smtp_email_address'), config('app.name'));
                    $mail->addAddress($recipientMail, $recipientName);      // Add a recipient
                    $mail->addReplyTo($reply, config('app.name') );

                    // Content
                    $mail->isHTML(true);                                    // Set email format to HTML
                    $mail->Subject = $title;
                    $mail->Body    = $content;
                    $mail->AltBody = trim(strip_tags($content));

                    if ($mail->send()) {
                        $return = true;
                    } else {
                        $return = false;
                    }

                } catch (PHPMailerException $e) {

                    $return = false; // $e->errorMessage();
                }
                break;
            
            default:
                $headers = "Reply-To: ". config('settings.contact_email') . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $return = mail($recipientMail, $title, $content, $headers);
                break;
        }
        

        return $return;
    }
}