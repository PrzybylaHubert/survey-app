<?php

declare(strict_types=1);

namespace App\Message;

class NewSurveyCreatedMessage
{
    private string $surveyName;
    private string $surveyDescription;

    public function __construct(string $surveyName, string $surveyDescription)
    {
        $this->surveyName = $surveyName;
        $this->surveyDescription = $surveyDescription;
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
