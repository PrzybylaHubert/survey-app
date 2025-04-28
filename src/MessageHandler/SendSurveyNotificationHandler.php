<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendSurveyNotificationMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler()]
class SendSurveyNotificationHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function __invoke(SendSurveyNotificationMessage $message): void
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($message->getUserEmail())
            ->subject('A new survey has been created: ' . $message->getSurveyName())
            ->text("Hello,\n\nA new survey has been created:\n\nName: {$message->getSurveyName()}\nDescription: {$message->getSurveyDescription()}\n\nThank you!");

        $this->mailer->send($email);
    }
}
