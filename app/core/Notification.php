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
                        '[USER]' => '{f_name|u_name}',
                        '[VERIFY_LINK]' => '<a href="' . $baseUrl . '/?verify={token}">' . KN::lang('verify_email') . '</a>'
                    ],
                ],
                'sys'   => ['type' => 'registration', 'icon' => 'mdi mdi-hand-wave'],
            ]
        ];
    }

    public function add($type, $args) {

        $args = (array) $args;
        if (isset($this->types[$type]) !== false) {

            foreach ($this->types[$type] as $method => $parameters) {
            
                switch ($method) {
                    case 'email': // email notifications
                        
                        $title = KN::lang($parameters['title']);
                        $body = KN::lang($parameters['body']);

                        if (isset($parameters['args']) !== false) { // dynamic text

                            $title = $this->dynamicReplacer($title, $args);
                            $body = $this->dynamicReplacer($body, $args);
                            KN::dump($body);
                        }

                        $this->emailLogger([
                            'title' => $title,
                            'body' => $body,
                            'recipient' => $args['u_name'],
                            'recipient_email' => $args['email'],
                            'recipient_id' => $args['id'],
                            'token' => $args['token']
                        ]);

                        break;
                    
                    case 'sys': // system notifications

                        $externalDatas = null;

                        if (isset($parameters['external']) !== false) {

                            $externalDatas = [];
                            foreach ($parameters['external'] as $key) {
                                $externalDatas[$key] = $args[$key];
                            }
                            $externalDatas = json_encode($externalDatas);
                        }
                            
                        (new DB())->table('notifications')
                            ->insert([
                                'user_id'       => $args['id'],
                                'type'          => $parameters['type'],
                                'external_datas'=> $externalDatas,
                                'created_at'    => time()
                            ]);

                        break;
                }
            }

        }
        
    }


    public function emailLogger($arguments) {

        $subTitle = $arguments['title'];
        $appName = KN::config('settings.name');
        $title = $subTitle . ' - ' . $appName;
        $content = $arguments['body'];

        if (file_exists($template = KN::path('app/resources/template/email.html'))) { // with template

            $unsubscribe = str_replace(['[LINK]'], KN::base('account') . '?unsubscribe=' . $arguments['token'], KN::lang('noti_unsubscribe_footer'));

            $footer = $appName . ' (c) ' . date('Y');
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
                strip_tags($content),
                $appName,
                $subTitle,
                $arguments['body'],
                $footer
            ], file_get_contents($template));

        }

        $status = 'pending';
        if (! KN::config('settings.mail_queue')) { // Direct sending

            $status = $this->sendEmail($arguments['recipient_email'], $arguments['recipient'], $content, $title) ? 'completed' : 'uncompleted';

        }

        if (! is_dir($path = KN::path('app/storage'))) mkdir($path);
        if (! is_dir($path .= '/email')) mkdir($path);
        if (! is_dir($path .= '/' . $status)) mkdir($path);

        $date = time();

        $path .= '/' . $date . '.html';

        file_put_contents($path, $content);

        return (new DB())->table('email_logs')
            ->insert([
                'date'          => $date,
                'email'         => $arguments['recipient_email'],
                'name'          => $arguments['recipient'],
                'title'         => $arguments['title'],
                'user_id'       => $arguments['recipient_id'],
                'sender_id'     => (isset($arguments['sender_id']) !== false ? $arguments['sender_id'] : null),
                'file'          => $date . '.html',
                'status'        => $status
            ]);

    }

    public function sendEmail($recipientMail, $recipientName = '', string $content = '', string $title = '') {

        $return = false;

        if (KN::config('app.dev_mode')) $recipientMail = KN::config('settings.contact_email');
        
        $sendingType = KN::config('settings.mail_send_type');

        if (KN::config('settings.dev_mode')) {
            $sendingType = 'server';
        }

        switch ($sendingType) {

            case 'smtp':
                $mail = new PHPMailer(true);
                $mail->setLanguage(lang('lang_code'));

                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host         = KN::config('settings.smtp_address');
                    $mail->SMTPAuth     = true;
                    $mail->Username     = KN::config('settings.smtp_email_address');
                    $mail->Password     = KN::config('settings.smtp_email_pass');
                    $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port         = KN::config('settings.smtp_port');
                    $mail->CharSet      = KN::config('app.charset');

                    $reply = KN::config('settings.contact_email');
                    if (! $reply OR $reply == '') {
                        $reply = KN::config('settings.smtp_email_address');
                    }
                    //Recipients
                    $mail->setFrom(KN::config('settings.smtp_email_address'), KN::config('app.name'));
                    $mail->addAddress($recipientMail, $recipientName);      // Add a recipient
                    $mail->addReplyTo($reply, KN::config('app.name') );

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
                $headers = "Reply-To: ". KN::config('settings.contact_email') . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset='".KN::config('app.charset')."'\r\n";
                $return = mail($recipientMail, $title, $content, $headers);
                break;
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

}