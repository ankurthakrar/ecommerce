<?php

namespace App\Helpers;

use Mail;
use App\Mail\EmailVerificationMail;
use App\Mail\ResetPasswordEmail;
use App\Mail\WelcomeEmailAdmin;
use App\Mail\WelcomeEmailUser;

class Helper
{

    /**
     * Write code on Method
     *
     * @return response()
     */
    public static function sendMail($view = '', $data = [], $to = '', $from = '', $attechMent = '')
    {
        if (empty($view) || empty($to)) {
            return false;
        }
        $subject = isset($data['subject']) ? $data['subject'] : '';
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <info@ecommerce.com>' . "\r\n";

        // For sending otp to mail

        if (isset($data['otp'])) {
            $otp = $data['otp'];
            Mail::to($data['email'])->send(new EmailVerificationMail($otp));
        }

        // For welcome mail to user

        elseif (isset($data['welcome_user'])) { 
            Mail::to($data['email'])->send(new WelcomeEmailUser($data['user']));
            
            // This will send mail to admin
            
            Mail::to(config('app.admin_mail'))->send(new WelcomeEmailAdmin($data['user']));
        }

        // For reset password link to mail

        elseif (isset($data['reset_link'])) {
            $reset_link = $data['reset_link'];
            Mail::to($data['email'])->send(new ResetPasswordEmail($reset_link));
        }
        return true;
    }
}
