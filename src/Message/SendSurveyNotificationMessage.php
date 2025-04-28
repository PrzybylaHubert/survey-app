<?php

declare(strict_types=1);

namespace App\Message;

class SendSurveyNotificationMessage
{
    private string $userEmail;
    private string $surveyName;
    private string $surveyDescription;

    public function __construct(
        string $userEmail,
        string $surveyName,
        string $surveyDescription
    ) {
        $this->userEmail = $userEmail;
        $this->surveyName = $surveyName;
        $this->surveyDescription = $surveyDescription;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getSurveyName(): string
    {
        return $this->surveyName;
    }

    public function getSurveyDescription(): string
    {
        return $this->surveyDescription;
    }
}
