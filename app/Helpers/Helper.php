<?php

namespace App\Helpers;

use Mail;
use App\Mail\EmailVerificationMail;
use App\Mail\ResetPasswordEmail;
use App\Mail\WelcomeEmailAdmin;
use App\Mail\WelcomeEmailUser;
use App\Mail\OrderInvoiceToUser;

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
       
        // For order place to user

        elseif (isset($data['order_invoice_to_user'])) { 
            Mail::to($data['email'])->send(new OrderInvoiceToUser($data['order']));
        }
        return true;
    }

    public static function sendOTP($message,$key){
        $apiKey       = urlencode(config('app.txt_lcl_api'));
        $senderID     = urlencode(config('app.txt_lcl_sender')); 
        // $apiKey       = urlencode('NDE2NDYzNDM3YTc1NTY3MTU1NjU3NDY5MzAzMDczMzI=');
        // $senderID     = urlencode('HBSEPL'); 

        // API URL
        $url = "https://api.textlocal.in/send";

        // Prepare the data for the API request
        $data = [
            'apikey' => $apiKey,
            'sender' => $senderID,
            'message' => $message,
            'numbers' => $key,
        ];

        // Make the API request using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Process the API response
        return $responseData = json_decode($response, true);
    }
}
