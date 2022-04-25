<?php

/**
 * @package KN
 * @subpackage KN Notification
 */

declare(strict_types=1);

namespace KN\Core;

use KN\Helpers\Base;
use KN\Model\Notifications as NotificationsModel;
use KN\Model\EmailLogs as EmailModel;

class Notification {

    /**
     * Notification types from Resources/notifications
     */
    public $types;

    /**
     * Factory class 
     */
    public $container;

    /**
     * @param object $container   It must be a factory object. 
     * @return void
     */
    public function __construct ($container) {

        if (file_exists($file = Base::path('app/Resources/notifications.php')))
            $this->types = $file;
        else
            throw new \Exception(Base::lang('error.notification_hook_file_not_found'));

        $this->container = $container;

    }

    public function add($type, $data = null) {

        if (isset($this->types[$type]) !== false) {

            return $this->types[$type]($this, $data);

        }
        /*
        switch ($type) {

            case 'registration':

                $title = KN::lang('noti_email_register_title');
                $name = (empty($data['f_name']) ? $data['u_name'] : $data['f_name']);
                $link = '<a href="' . KN::base('account/?verify-account=' . $data['token']) . '">
                    ' . KN::lang('verify_email') . '
                </a>';
                $body = str_replace(
                    ['[USER]', '[VERIFY_LINK]'], 
                    [$name, $link], 
                    KN::lang('noti_email_register_body')
                );

                $this->emailLogger([
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
                break;

            case 'recovery_request':

                $title = KN::lang('noti_email_recovery_request_title');
                $name = (empty($data['f_name']) ? $data['u_name'] : $data['f_name']);
                $link = '<a href="' . KN::base('account/recovery?token=' . $data['token']) . '">
                    ' . KN::lang('recovery_account') . '
                </a>';
                $body = str_replace(
                    ['[USER]', '[RECOVERY_LINK]'], 
                    [$name, $link], 
                    KN::lang('noti_email_recovery_request_body')
                );

                return $this->emailLogger([
                    'title' => $title,
                    'body' => $body,
                    'recipient' => $data['u_name'],
                    'recipient_email' => $data['email'],
                    'recipient_id' => $data['id'],
                    'token' => $data['token']
                ]);
                break;

            case 'recovery_account':

                $title = KN::lang('noti_email_recovery_account_title');
                $name = (empty($data['f_name']) ? $data['u_name'] : $data['f_name']);
                $body = str_replace(
                    ['[USER]'], 
                    [$name], 
                    KN::lang('noti_email_recovery_account_body')
                );

                $this->emailLogger([
                    'title' => $title,
                    'body' => $body,
                    'recipient' => $data['u_name'],
                    'recipient_email' => $data['email'],
                    'recipient_id' => $data['id'],
                    'token' => $data['token']
                ]);
                    
                return (new DB())->table('notifications')
                    ->insert([
                        'user_id'       => $data['id'],
                        'type'          => $type,
                        'created_at'    => time()
                    ]);
                break;

            case 'email_change':

                $title = KN::lang('noti_email_change_title');
                $name = (empty($data['f_name']) ? $data['u_name'] : $data['f_name']);
                $link = '<a href="' . KN::base('account/?verify-account=' . $data['token']) . '">
                    ' . KN::lang('verify_email') . '
                </a>';
                $body = str_replace(
                    ['[USER]', '[VERIFY_LINK]'], 
                    [$name, $link], 
                    KN::lang('noti_email_change_body')
                );

                $this->emailLogger([
                    'title' => $title,
                    'body' => $body,
                    'recipient' => $data['u_name'],
                    'recipient_email' => $data['email'],
                    'recipient_id' => $data['id'],
                    'token' => $data['token']
                ]);
                break;
        }
        */
        
    }

    /**
     * 
     * @param array $arguments  email content 
     * @return boolen|integer 
     **/
    public function addEmail($arguments) {

        $subTitle = $arguments['title'];
        $appName = KN::config('settings.name');
        $title = $subTitle . ' - ' . $appName;
        $content = $arguments['body'];

        if (file_exists($template = KN::path('app/resources/template/email.html'))) { // with template

            $unsubscribe = str_replace(
                ['[LINK]'], 
                KN::base('account') . '?unsubscribe=' . $arguments['token'], 
                KN::lang('noti_unsubscribe_footer')
            );

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

            $status = $this->sendEmail($arguments['recipient_email'], $arguments['recipient'], $content, $title) ? 
                'completed' : 'uncompleted';

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

}