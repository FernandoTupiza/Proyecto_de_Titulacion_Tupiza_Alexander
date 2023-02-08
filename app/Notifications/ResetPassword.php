<?php
 
namespace App\Notifications;
 
use \Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
 
class ResetPassword extends ResetPasswordNotification

{}