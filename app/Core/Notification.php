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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

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
     * Email and Notifications Models
     **/
    public $emailModel;
    public $notificationsModel;

    /**
     * @param object $container   It must be a factory object. 
     * @return void
     */
    public function __construct ($container) {

        if (file_exists($file = Base::path('app/Resources/notifications.php')))
            $this->types = require $file;
        else
            throw new \Exception(Base::lang('error.notification_hook_file_not_found'));

        $this->container = $container;
        $this->emailModel = (new EmailModel());
        $this->notificationsModel = (new NotificationsModel());

    }

    public function add($type, $data = null) {

        if (isset($this->types[$type]) !== false) {

            return $this->types[$type]($this, $data);

        }
        
    }

    /**
     * 
     * @param array $arguments  email content 
     * @return boolen|integer 
     **/
    public function addEmail($arguments) {

        $title = $arguments['title'];
        $app = Base::config('settings.name');
        $body = $arguments['body'];

        if (file_exists($template = Base::path('app/Resources/template/email.html'))) { // with template

            $footer = $app . ' (c) ' . date('Y');

            $content = str_replace([
                '[TITLE]',
                '[ALT_BODY]',
                '[APP]',
                '[BODY]',
                '[FOOTER]'
            ], [
                $title,
                trim(strip_tags($body)),
                $app,
                $body,
                $footer
            ], file_get_contents($template));

        } else {
            $content = $body;
        }

        $status = 'pending';
        if (! Base::config('settings.mail_queue')) { // Direct sending

            $status = $this->sendEmail($arguments['recipient_email'], $arguments['recipient'], $content, $title) ? 
                'completed' : 'uncompleted';

        }

        if (! is_dir($path = Base::path('app/Storage'))) mkdir($path);
        if (! is_dir($path .= '/email')) mkdir($path);
        if (! is_dir($path .= '/' . $status)) mkdir($path);

        $date = time();

        $path .= '/' . $date . '.html';

        file_put_contents($path, $content);

        return $this->emailModel->insert([
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

        if (Base::config('app.dev_mode')) $recipientMail = Base::config('settings.contact_email');
        
        $sendingType = Base::config('settings.mail_send_type');

        if (Base::config('app.dev_mode')) {
            $sendingType = 'server';
        }

        switch ($sendingType) {

            case 'smtp':
                $mail = new PHPMailer(true);
                if ($lang = Base::lang('lang.code') !== 'en') 
                    $mail->setLanguage($lang);

                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host         = Base::config('settings.smtp_address');
                    $mail->SMTPAuth     = true;
                    $mail->Username     = Base::config('settings.smtp_email_address');
                    $mail->Password     = Base::config('settings.smtp_email_pass');
                    $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port         = Base::config('settings.smtp_port');
                    $mail->CharSet      = Base::config('app.charset');

                    $reply = Base::config('settings.contact_email');
                    if (! $reply OR $reply == '') {
                        $reply = Base::config('settings.smtp_email_address');
                    }
                    //Recipients
                    $mail->setFrom(Base::config('settings.smtp_email_address'), Base::config('app.name'));
                    $mail->addAddress($recipientMail, $recipientName);      // Add a recipient
                    $mail->addReplyTo($reply, Base::config('app.name') );

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
                $headers = "Reply-To: ". Base::config('settings.contact_email') . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset='".Base::config('app.charset')."'\r\n";
                $return = mail($recipientMail, $title, $content, $headers);
                break;
        }

        return $return;

    }

}