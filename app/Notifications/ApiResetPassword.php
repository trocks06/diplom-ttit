<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApiResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Сброс пароля в стоматологической клинике')
            ->greeting('Здравствуйте!')
            ->line('Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашей учётной записи.')
            ->line('**Ваш токен для сброса пароля:** ' . $this->token)
            ->line('Чтобы установить новый пароль, отправьте POST-запрос на следующий эндпоинт:')
            ->line('**URL:** `' . url('/api/reset-password') . '`')
            ->line('**Параметры запроса (JSON):**')
            ->line('- `token` : ваш токен')
            ->line('- `email` : ваш email')
            ->line('- `password` : новый пароль')
            ->line('- `password_confirmation` : подтверждение пароля')
            ->line('Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.')
            ->salutation('С уважением, команда стоматологии');
    }
}
