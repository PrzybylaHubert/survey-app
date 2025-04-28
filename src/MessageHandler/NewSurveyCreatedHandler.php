<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\NewSurveyCreatedMessage;
use App\Message\SendSurveyNotificationMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler()]
class NewSurveyCreatedHandler
{
    private const BATCH_SIZE = 10000;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function __invoke(NewSurveyCreatedMessage $message): void
    {
        $surveyName = $message->getSurveyName();
        $surveyDescription = $message->getSurveyDescription();

        $iterableResult = $this->userRepository->getIterableUserEmails();

        $i = 0;
        foreach ($iterableResult as $user) {
            $this->messageBus->dispatch(new SendSurveyNotificationMessage(
                $user['email'],
                $surveyName,
                $surveyDescription
            ));

            if (++$i % self::BATCH_SIZE === 0) {
                $this->entityManager->clear();
            }
        }
    }
}
